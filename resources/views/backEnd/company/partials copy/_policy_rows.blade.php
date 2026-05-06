@forelse($policies as $p)
<tr>
    <td>{{ $p->policy_date }}</td>
    <td>{{ $p->policy_name }}</td>
    <td>{{ $p->policy_category ?? '-' }}</td>
    <td>{{ $p->policy_valid ?? '-' }}</td>
    <td>{{ $p->view_to_employees ? 'Yes' : 'No' }}</td>
    <td>{{ basename($p->policy_file) }}</td>
    <td>
        <button class="btn btn-sm btn-danger deletePolicyBtn" data-id="{{ $p->id }}">
            <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">No policiesfff added yet.</td>
</tr>
@endforelse
