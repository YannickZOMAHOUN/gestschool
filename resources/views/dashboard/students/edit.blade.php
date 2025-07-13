@extends('layouts.template')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 font-weight-light"><i class="fas fa-book-open me-2"></i>Gestion des Notes et Moyennes</h5>
            <div class="badge bg-white text-primary rounded-pill p-2">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d/m/Y') }}
            </div>
        </div>
        <div class="card-body p-4">
            <form id="note-form" class="mb-4">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" value="{{ $recording->year->year }}" readonly>
                            <label><i class="fas fa-calendar me-2"></i>Année scolaire</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" value="{{ $recording->classroom->classroom }}" readonly>
                            <label><i class="fas fa-door-open me-2"></i>Classe</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select id="student_id" class="form-select select2">
                                <option value="">-- Choisissez un élève --</option>
                                @foreach ($classRecordings as $rec)
                                    <option value="{{ $rec->student->id }}" {{ $rec->student->id == $recording->student->id ? 'selected' : '' }}>
                                        {{ $rec->student->name }} {{ $rec->student->surname }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="student_id"><i class="fas fa-user-graduate me-2"></i>Élève</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <select id="semester" class="form-select">
                                <option value="">-- Choisissez --</option>
                                <option value="1">Semestre 1</option>
                                <option value="2">Semestre 2</option>
                            </select>
                            <label for="semester"><i class="fas fa-calendar-week me-2"></i>Semestre</label>
                        </div>
                    </div>
                </div>
            </form>

            <div id="student-info" class="alert alert-info mt-4 mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                    <span>Sélectionnez un élève et un semestre pour afficher les résultats</span>
                </div>
                <div id="loading-indicator" class="spinner-border text-primary" style="display: none;" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>

            <div class="table-responsive rounded-lg border">
                <table class="table table-hover table-bordered mb-0" id="notes-table">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center align-middle">Matière</th>
                            <th class="text-center align-middle" style="width: 100px;">Coefficient</th>
                            <th class="text-center align-middle" style="width: 100px;">Note</th>
                            <th class="text-center align-middle" style="width: 150px;">Moyenne Coefficientée</th>
                            <th class="text-center align-middle" style="width: 150px;">Appréciation</th>
                            <th class="text-center align-middle" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-2x mb-2"></i><br>
                                Aucune donnée à afficher
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-group-divider">
                        <tr class="table-active">
                            <th colspan="3" class="text-end fw-normal">Total :</th>
                            <th id="total-moyenne-coefficiee" class="text-center fw-bold">-</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr class="table-primary">
                            <th colspan="3" class="text-end fw-normal">Moyenne Semestrielle :</th>
                            <th id="moyenne-generale" class="text-center fw-bold">-</th>
                            <th colspan="2"></th>
                        </tr>
                        <tr id="moyenne-annuelle-row" class="table-success" style="display: none;">
                            <th colspan="3" class="text-end fw-normal">Moyenne Annuelle :</th>
                            <th id="moyenne-annuelle" class="text-center fw-bold">-</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="fas fa-database me-1"></i> Dernière mise à jour: {{ now()->format('H:i:s') }}
                </div>
                <button id="print-btn" class="btn btn-outline-primary" disabled>
                    <i class="fas fa-print me-1"></i> Imprimer le bulletin
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                            <i class="fas fa-trash-alt text-danger fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Suppression de note</h6>
                            <p class="mb-0 text-muted">Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Confirmer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    // Initialisation de Select2 pour la recherche d'élèves
    $('.select2').select2({
        placeholder: "Rechercher un élève",
        allowClear: true,
        width: '100%'
    });

    function formatRank(rank) {
        return rank === 1 ? '1<sup>er</sup>' : rank + '<sup>ème</sup>';
    }

    function formatMoyenne(value) {
        if (!value) return '-';
        const num = parseFloat(value);
        return num.toFixed(2) + ' / 20';
    }

    $('#student_id, #semester').change(function () {
        const studentId = $('#student_id').val();
        const semester = $('#semester').val();
        const yearId = '{{ $recording->year->id }}';
        const classroomId = '{{ $recording->classroom->id }}';

        if (!studentId || !semester) {
            $('#student-info').html(`
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        <span>Sélectionnez un élève et un semestre pour afficher les résultats</span>
                    </div>
                </div>
            `);
            $('#print-btn').prop('disabled', true);
            return;
        }

        // Afficher l'indicateur de chargement
        $('#loading-indicator').show();
        $('#notes-table tbody').html(`
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2 mb-0">Chargement des données...</p>
                </td>
            </tr>
        `);

        $.getJSON("{{ route('get.student.notes') }}", {
            student_id: studentId,
            semester: semester,
            year_id: yearId,
            classroom_id: classroomId
        }, function (data) {
            let notesHtml = '';
            let appreciations = ['Médiocre','Insuffisant','Passable','Assez Bien','Bien','Très Bien','Honorable'];

            if (data.notes && data.notes.length > 0) {
                data.notes.forEach(note => {
                    let appr = '<span class="badge bg-secondary">N/A</span>';
                    if (note.note !== null) {
                        if (note.note < 5) appr = `<span class="badge bg-danger">${appreciations[0]}</span>`;
                        else if (note.note < 10) appr = `<span class="badge bg-warning text-dark">${appreciations[1]}</span>`;
                        else if (note.note < 12) appr = `<span class="badge bg-info">${appreciations[2]}</span>`;
                        else if (note.note < 14) appr = `<span class="badge bg-primary">${appreciations[3]}</span>`;
                        else if (note.note < 16) appr = `<span class="badge bg-success">${appreciations[4]}</span>`;
                        else if (note.note < 19) appr = `<span class="badge bg-success">${appreciations[5]}</span>`;
                        else appr = `<span class="badge bg-dark">${appreciations[6]}</span>`;
                    }

                    const editUrl = `/notes/${note.id}/edit`;
                    notesHtml += `
                        <tr>
                            <td>${note.subject}</td>
                            <td class="text-center">${note.coefficient}</td>
                            <td class="text-center fw-bold ${note.note < 10 ? 'text-danger' : note.note < 12 ? 'text-warning' : 'text-success'}">
                                ${note.note ?? '-'}
                            </td>
                            <td class="text-center">${note.moyenne_coefficiee ? parseFloat(note.moyenne_coefficiee).toFixed(2) : '-'}</td>
                            <td class="text-center">${appr}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="${editUrl}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger delete-button" data-id="${note.id}" data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            } else {
                notesHtml = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-book-open fa-2x mb-2"></i><br>
                            Aucune note disponible pour ce semestre
                        </td>
                    </tr>
                `;
            }

            $('#notes-table tbody').html(notesHtml);

            // Formatage des moyennes
            $('#total-moyenne-coefficiee').html(formatMoyenne(data.total_moyenne_coefficiee));
            $('#moyenne-generale').html(formatMoyenne(data.moyenne_generale));
            $('#moyenne-annuelle').html(formatMoyenne(data.moyenne_annuelle));
            $('#moyenne-annuelle-row').toggle(semester == 2);

            // Mise à jour des informations étudiantes
            let infoHtml = `
                <div class="alert alert-success d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-graduate me-2"></i>
                        <strong>${data.student_name}</strong>
                    </div>
                    <div class="d-flex gap-4">
                        <div><strong>Rang Semestriel :</strong> <span class="badge bg-primary">${data.rank ? formatRank(data.rank) : 'N/A'}</span></div>
            `;

            if (semester == 2) {
                infoHtml += `
                        <div><strong>Rang Annuel :</strong> <span class="badge bg-success">${data.rank_annuel ? formatRank(data.rank_annuel) : 'N/A'}</span></div>
                `;
            }

            infoHtml += `</div></div>`;
            $('#student-info').html(infoHtml);

            // Activer le bouton d'impression
            $('#print-btn').prop('disabled', false);

            // Initialiser les tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }).fail(function() {
            $('#student-info').html(`
                <div class="alert alert-danger d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>Erreur lors du chargement des données</span>
                    </div>
                </div>
            `);
        }).always(function() {
            $('#loading-indicator').hide();
        });
    });

    // Gestion de l'impression
    $('#print-btn').click(function() {
        window.print();
    });

    // Gestion de la suppression
    $(document).on('click', '.delete-button', function (e) {
        e.preventDefault();
        const noteId = $(this).data('id');
        $('#deleteForm').attr('action', `/notes/${noteId}`);
        $('#deleteModal').modal('show');
    });

    $('#deleteForm').submit(function (e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $('#deleteModal').modal('hide');
                $('body').append(`
                    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.5); z-index: 1060;">
                        <div class="spinner-border text-white" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                `);
            },
            success: function () {
                showToast('success', 'Succès', 'La note a été supprimée avec succès.');
                $('#semester').change();
            },
            error: function () {
                showToast('danger', 'Erreur', 'Une erreur est survenue lors de la suppression.');
            },
            complete: function() {
                $('.position-fixed').remove();
            }
        });
    });

    function showToast(type, title, message) {
        const toast = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div class="toast show align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>${title}</strong><br>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        `;
        $('body').append(toast);
        setTimeout(() => $('.toast').toast('hide').remove(), 5000);
    }
});
</script>

<style>
    .card-header.bg-gradient-primary {
        background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
    }
    .card-header.bg-gradient-danger {
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    }
    .table th {
        font-weight: 500;
    }
    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .select2-container--default .select2-selection--single {
        height: 58px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 56px;
    }
</style>
@endsection
