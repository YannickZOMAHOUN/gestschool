<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Recording;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsImport implements ToModel, WithStartRow
{
    protected $classroom_id;
    protected $year_id;
    protected $matriculesImportes = [];

    public function __construct($classroom_id, $year_id)
    {
        $this->classroom_id = $classroom_id;
        $this->year_id = $year_id;
    }

    public function startRow(): int
    {
        return 3;
    }

    public function model(array $row)
    {
        try {
            if (empty($row[1])) {
                Log::warning("Matricule vide ignoré. Ligne : " . json_encode($row));
                return null;
            }

            $matricule = trim($row[1]);
            $sex = strtoupper(substr(trim($row[4]), 0, 1));
            $birthday = $this->convertExcelDate($row[5]);
            $phone = $this->formatPhoneNumber(trim($row[8]));

            return DB::transaction(function () use ($row, $matricule, $sex, $birthday, $phone) {
                $existingStudent = Student::where('matricule', $matricule)->first();

                if ($existingStudent) {
                    $alreadyRecorded = Recording::where([
                        'student_id' => $existingStudent->id,
                        'classroom_id' => $this->classroom_id,
                        'year_id' => $this->year_id,
                    ])->exists();

                    if ($alreadyRecorded) {
                        Log::info("Déjà existant : {$matricule}");
                        return null;
                    }

                    Recording::create([
                        'student_id' => $existingStudent->id,
                        'classroom_id' => $this->classroom_id,
                        'year_id' => $this->year_id,
                    ]);

                    Log::info("Enregistrement ajouté à l'étudiant existant : {$matricule}");
                    $this->matriculesImportes[] = $matricule;
                    return $existingStudent;
                }
                // Créer l'élève
                $student = Student::create([
                    'matricule' => $matricule,
                    'name' => trim($row[2]),
                    'surname' => trim($row[3]),
                    'sex' => $sex,
                    'birthday' => $birthday,
                    'birthplace' => trim($row[6]),
                    'number' => $phone,
                    'aptitude' => trim($row[9]),
                ]);

                // Enregistrement de la classe
                Recording::create([
                    'student_id' => $student->id,
                    'classroom_id' => $this->classroom_id,
                    'year_id' => $this->year_id,
                ]);

                Log::info("Étudiant importé : {$matricule}");
                $this->matriculesImportes[] = $matricule;

                return $student;
            });
        } catch (\Throwable $e) {
            Log::error("Erreur ligne Excel : " . json_encode($row) . " — " . $e->getMessage());
            return null;
        }
    }

    protected function convertExcelDate($dateValue)
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

            throw new \Exception("Format de date invalide");
        } catch (\Exception $e) {
            Log::warning("Erreur date : '{$dateValue}' — " . $e->getMessage());
            return '2001-01-01';
        }
    }

    protected function formatPhoneNumber($phoneNumber)
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
