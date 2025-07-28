@forelse($assignments as $assignment)
    <tr>
        <td>{{ $assignment->classroom->name ?? '-' }}</td>
        <td>{{ $assignment->subject->name ?? '-' }}</td>
        <td>{{ $assignment->user->name ?? '-' }} {{ $assignment->user->surname ?? '' }}</td>
        <td class="text-center">
            @if($assignment->is_principal)
                <span class="badge bg-success"><i class="fas fa-check"></i></span>
            @else
                <span class="badge bg-secondary"><i class="fas fa-times"></i></span>
            @endif
        </td>
        <td>
            <form class="d-inline delete-assignment" action="{{ route('teacher-assignments.destroy', $assignment->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
            @if(!$assignment->is_principal)
                <form class="d-inline make-principal" action="{{ route('principal-teachers.set', $assignment->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning" title="Définir comme principal">
                        <i class="fas fa-star"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted py-4">
            <i class="fas fa-info-circle fa-2x mb-2"></i><br>
            Aucune affectation pour cette année
        </td>
    </tr>
@endforelse
