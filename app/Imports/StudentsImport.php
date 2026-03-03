<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Recording;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Collection;

class StudentsImport implements ToArray
{
    protected $classroom_id;
    protected $year_id;
    public array $matriculesImportes = [];

    /**
     * Correspondance entre les clés internes et les en-têtes possibles du fichier.
     * Toutes les valeurs sont en minuscules sans accents (après normalisation).
     */
    protected array $columnAliases = [
        'matricule'  => ['matricule', 'mat', 'numero_matricule', 'n_matricule'],
        'name'       => ['noms', 'nom', 'name', 'last_name', 'lastname'],
        'surname'    => ['prenoms', 'prenom', 'surname', 'first_name', 'firstname'],
        'sex'        => ['sexe', 'sex', 'genre', 'gender'],
        'birthday'   => ['date_de_naissance', 'date_naissance', 'birthday', 'datenaissance', 'naissance'],
        'birthplace' => ['lieu_de_naissance', 'lieu_naissance', 'birthplace', 'lieunaissance'],
        'number'     => ['numero', 'telephone', 'phone', 'number', 'tel', 'contact', 'num'],
        'aptitude'   => ['aptitude_eps', 'aptitude', 'apte', 'etat_sante'],
    ];

    /**
     * Valeurs acceptées pour le sexe → normalisées en M ou F.
     */
    protected array $sexMap = [
        'm'        => 'M',
        'masculin' => 'M',
        'male'     => 'M',
        'homme'    => 'M',
        'h'        => 'M',
        'f'        => 'F',
        'feminin'  => 'F',
        'female'   => 'F',
        'femme'    => 'F',
    ];

    public function __construct($classroom_id, $year_id)
    {
        $this->classroom_id = $classroom_id;
        $this->year_id      = $year_id;
    }

    // -------------------------------------------------------------------------
    // Point d'entrée principal
    // -------------------------------------------------------------------------

    public function array(array $rows): void
    {
        if (empty($rows)) {
            Log::warning("StudentsImport : fichier vide.");
            return;
        }

        // 1. Détecter automatiquement la ligne d'en-tête
        [$headerRowIndex, $columnMap] = $this->detectHeaderRow($rows);

        if ($headerRowIndex === null) {
            Log::error("StudentsImport : impossible de trouver une ligne d'en-tête reconnue dans le fichier.");
            return;
        }

        Log::info("StudentsImport : en-tête détecté à la ligne " . ($headerRowIndex + 1) . ".");

        // Vérifier si des colonnes obligatoires sont manquantes
        $missing = array_diff(array_keys($this->columnAliases), array_keys($columnMap));
        if (!empty($missing)) {
            Log::warning("StudentsImport : colonnes introuvables → " . implode(', ', $missing));
        }

        // 2. Traiter uniquement les lignes après l'en-tête
        $dataRows = array_slice($rows, $headerRowIndex + 1);

        foreach ($dataRows as $index => $row) {
            $this->processRow($row, $columnMap, $headerRowIndex + 2 + $index);
        }
    }

    // -------------------------------------------------------------------------
    // Détection automatique de la ligne d'en-tête
    // -------------------------------------------------------------------------

    /**
     * Parcourt les lignes et retourne [index, columnMap] de la première ligne
     * qui contient au moins la colonne 'matricule' (colonne clé minimale requise).
     *
     * On construit un "score" : plus une ligne reconnaît d'alias connus,
     * plus elle est candidate. La ligne avec le meilleur score est retenue.
     */
    protected function detectHeaderRow(array $rows): array
    {
        $bestScore      = 0;
        $bestIndex      = null;
        $bestColumnMap  = [];

        // On ne cherche que dans les 20 premières lignes pour ne pas scanner
        // toutes les données d'un grand fichier.
        $searchLimit = min(20, count($rows));

        for ($i = 0; $i < $searchLimit; $i++) {
            $row = $rows[$i];
            $columnMap = $this->buildColumnMap($row);
            $score     = count($columnMap);

            // On exige au minimum de reconnaître 'matricule' pour valider la ligne
            if ($score > $bestScore && isset($columnMap['matricule'])) {
                $bestScore     = $score;
                $bestIndex     = $i;
                $bestColumnMap = $columnMap;
            }
        }

        return [$bestIndex, $bestColumnMap];
    }

    /**
     * Construit un tableau [ clé_interne => index_colonne ] pour une ligne donnée.
     * Les valeurs de la ligne servent de "noms de colonnes" (c'est la ligne d'en-tête).
     */
    protected function buildColumnMap(array $row): array
    {
        $map = [];

        // Aplatir tous les alias connus dans un lookup rapide : alias → clé_interne
        $aliasLookup = [];
        foreach ($this->columnAliases as $internalKey => $aliases) {
            foreach ($aliases as $alias) {
                $aliasLookup[$alias] = $internalKey;
            }
        }

        foreach ($row as $colIndex => $cellValue) {
            if (empty($cellValue) || !is_string($cellValue)) continue;

            $normalized = $this->normalizeHeader($cellValue);

            if (isset($aliasLookup[$normalized]) && !isset($map[$aliasLookup[$normalized]])) {
                $map[$aliasLookup[$normalized]] = $colIndex;
            }
        }

        return $map;
    }

    // -------------------------------------------------------------------------
    // Traitement d'une ligne de données
    // -------------------------------------------------------------------------

    protected function processRow(array $row, array $columnMap, int $lineNumber): void
    {
        try {
            $matricule = trim((string) $this->getCol($row, $columnMap, 'matricule', ''));

            if (empty($matricule)) {
                // Ligne vide (fin de tableau, totaux, etc.) — on ignore silencieusement
                return;
            }

            $sex      = $this->normalizeSex((string) $this->getCol($row, $columnMap, 'sex', ''));
            $birthday = $this->convertExcelDate($this->getCol($row, $columnMap, 'birthday'));
            $phone    = $this->formatPhoneNumber(trim((string) $this->getCol($row, $columnMap, 'number', '')));

            DB::transaction(function () use ($row, $columnMap, $matricule, $sex, $birthday, $phone, $lineNumber) {
                $existingStudent = Student::where('matricule', $matricule)->first();

                if ($existingStudent) {
                    $alreadyRecorded = Recording::where([
                        'student_id'   => $existingStudent->id,
                        'classroom_id' => $this->classroom_id,
                        'year_id'      => $this->year_id,
                    ])->exists();

                    if ($alreadyRecorded) {
                        Log::info("StudentsImport ligne {$lineNumber} : déjà enregistré ({$matricule}).");
                        return;
                    }

                    Recording::create([
                        'student_id'   => $existingStudent->id,
                        'classroom_id' => $this->classroom_id,
                        'year_id'      => $this->year_id,
                    ]);

                    Log::info("StudentsImport ligne {$lineNumber} : enregistrement ajouté ({$matricule}).");
                    $this->matriculesImportes[] = $matricule;
                    return;
                }

                $student = Student::create([
                    'matricule'  => $matricule,
                    'name'       => trim((string) $this->getCol($row, $columnMap, 'name', '')),
                    'surname'    => trim((string) $this->getCol($row, $columnMap, 'surname', '')),
                    'sex'        => $sex,
                    'birthday'   => $birthday,
                    'birthplace' => trim((string) $this->getCol($row, $columnMap, 'birthplace', '')),
                    'number'     => $phone,
                    'aptitude'   => trim((string) $this->getCol($row, $columnMap, 'aptitude', '')),
                ]);

                Recording::create([
                    'student_id'   => $student->id,
                    'classroom_id' => $this->classroom_id,
                    'year_id'      => $this->year_id,
                ]);

                Log::info("StudentsImport ligne {$lineNumber} : étudiant créé ({$matricule}).");
                $this->matriculesImportes[] = $matricule;
            });

        } catch (\Throwable $e) {
            Log::error("StudentsImport ligne {$lineNumber} : " . $e->getMessage() . " — " . json_encode($row));
        }
    }

    // -------------------------------------------------------------------------
    // Utilitaires
    // -------------------------------------------------------------------------

    /**
     * Lit une valeur depuis une ligne brute via la map de colonnes (par index).
     */
    protected function getCol(array $row, array $columnMap, string $key, $default = null)
    {
        if (!isset($columnMap[$key])) return $default;
        return $row[$columnMap[$key]] ?? $default;
    }

    /**
     * Normalise un en-tête : minuscules, suppression accents, espaces/tirets → underscores.
     */
    protected function normalizeHeader(string $header): string
    {
        $header = mb_strtolower(trim($header));
        $header = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $header) ?: $header;
        $header = preg_replace('/[\s\-]+/', '_', $header);
        $header = preg_replace('/[^a-z0-9_]/', '', $header);

        return $header;
    }

    /**
     * Normalise la valeur du sexe (Masculin → M, Féminin → F, etc.)
     */
    protected function normalizeSex(?string $value): string
    {
        if (empty($value)) return '';

        $normalized = mb_strtolower(trim($value));
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized) ?: $normalized;

        return $this->sexMap[$normalized] ?? strtoupper(substr($value, 0, 1));
    }

    protected function convertExcelDate($dateValue): string
    {
        try {
            if ($dateValue instanceof \DateTime) {
                return Carbon::instance($dateValue)->format('Y-m-d');
            }

            if (is_string($dateValue) && preg_match('#^\d{2}/\d{2}/\d{4}$#', $dateValue)) {
                return Carbon::createFromFormat('d/m/Y', $dateValue)->format('Y-m-d');
            }

            if (is_numeric($dateValue)) {
                return Carbon::createFromTimestamp((int)(($dateValue - 25569) * 86400))->format('Y-m-d');
            }

            throw new \Exception("Format de date invalide : {$dateValue}");
        } catch (\Exception $e) {
            Log::warning("StudentsImport : erreur date '{$dateValue}' — " . $e->getMessage());
            return '2001-01-01';
        }
    }

    protected function formatPhoneNumber(?string $phoneNumber): ?string
    {
        if (empty($phoneNumber)) return null;

        $clean = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (str_starts_with($clean, '229')) {
            $clean = substr($clean, 3);
        }

        if (str_starts_with($clean, '0')) {
            $clean = substr($clean, 1);
        }

        return '+229 01' . $clean;
    }
}
