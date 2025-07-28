@if($assignments->isEmpty())
    <div class="alert alert-info">Aucune affectation trouvée pour cette année</div>
@else
    <table class="table">
        <thead>
            <tr>
                <th>Enseignant</th>
                <th>Classe</th>
                <th>Matière</th>
                <th>Année</th>
                <th>Prof Principal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->user->name }} {{ $assignment->user->surname }}</td>
                    <td>{{ $assignment->classroom->name }}</td>
                    <td>{{ $assignment->subject->name }}</td>
                    <td>{{ $assignment->year->year }}</td>
                    <td>{{ $assignment->is_principal ? 'Oui' : 'Non' }}</td>
                    <td>
                        <form class="delete-assignment" action="{{ route('teacher-assignment.destroy', $assignment->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
