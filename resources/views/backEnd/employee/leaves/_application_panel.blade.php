@php
    $isEdit = isset($leave) && $leave;
    $isDraft = $isEdit && ($leave->approve_status === 'D');
    $hasAnyApproval = $isEdit && $leave->chain && $leave->chain->steps->where('status', '!=', 'P')->count() > 0;
    $formAction = $isEdit ? route('employee.leaves.update', $leave->id) : route('employee.leaves.store');
    $documentNo = $isEdit ? ($leave->leave_application_no ?: ('LR-' . $leave->id)) : ($leaveApplicationNo ?? '');
@endphp

<form id="leaveApplicationForm" class="form-horizontal" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="action_type" id="leave_action_type" value="draft">

    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2 sticky-top" style="background-color:#f7f8fd">
        <h4 class="purchase-order-content-header-left mb-0">
            {{ $isEdit ? 'Edit - ' : 'New (' }}<span class="font-weight-600">{{ $documentNo }}</span>{{ $isEdit ? '' : ')' }}
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            @if(!$hasAnyApproval)
                <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2 leave-action-btn" data-action="draft">
                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                    <span>Save</span>
                </button>
                @if (!$isEdit || $isDraft)
                    <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2 leave-action-btn" data-action="submit">
                        <i class="ico icon-outline-send-square text-success"></i>
                        <span>Save & Submit for Approval</span>
                    </button>
                @endif
            @endif
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu"></ul>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <fieldset {!! $hasAnyApproval ? 'disabled="disabled"' : '' !!}>
                @include('backEnd.employee.leaves._application_form', ['leave' => $leave ?? null, 'disableHandover' => $isEdit])
            </fieldset>
        </div>
    </div>

</form>

    <div class="modal fade" id="leaveDeclarationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Employee Declaration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        I hereby certify that the information provided in this leave application is true and accurate. I understand that approval of this leave request is subject to company policy and management approval. I confirm that I have completed the necessary work handover arrangements and understand that any false information or misuse of leave may result in disciplinary action.
                    </p>
                    <div class="form-check mb-2">
                        <input class="form-check-input leave-declaration-check" type="checkbox" name="declaration_info_confirmed" id="leave_declaration_info_confirmed" value="1" {{ old('declaration_info_confirmed', $leave->declaration_info_confirmed ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="leave_declaration_info_confirmed">I confirm that the above information is correct.</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input leave-declaration-check" type="checkbox" name="declaration_handover_confirmed" id="leave_declaration_handover_confirmed" value="1" {{ old('declaration_handover_confirmed', $leave->declaration_handover_confirmed ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="leave_declaration_handover_confirmed">I confirm that I have handed over my responsibilities appropriately.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input leave-declaration-check" type="checkbox" name="declaration_policy_agreed" id="leave_declaration_policy_agreed" value="1" {{ old('declaration_policy_agreed', $leave->declaration_policy_agreed ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="leave_declaration_policy_agreed">I understand and agree to comply with the Company's Leave Policy.</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2" id="agreeAndSubmitBtn">
                        <i class="ico icon-outline-send-square text-success"></i>
                        <span>Agree & Submit</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
$(function () {
    // Move modal to body to prevent z-index/backdrop conflicts from nested containers
    $('#leaveDeclarationModal').appendTo('body');

    $('.leave-action-btn').on('click', function (e) {
        e.preventDefault();
        var action = $(this).data('action');
        $('#leave_action_type').val(action === 'submit' ? 'submit' : 'draft');
        
        var form = $('#leaveApplicationForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (action === 'submit') {
            $('#leaveDeclarationModal').modal('show');
        } else {
            form.submit();
        }
    });

    $('#agreeAndSubmitBtn').on('click', function () {
        if (!$('#leave_declaration_info_confirmed').is(':checked') ||
            !$('#leave_declaration_handover_confirmed').is(':checked') ||
            !$('#leave_declaration_policy_agreed').is(':checked')) {
            alert('Please check all declaration checkboxes to proceed.');
            return;
        }

        $('<input>').attr({type: 'hidden', name: 'declaration_info_confirmed', value: '1'}).appendTo('#leaveApplicationForm');
        $('<input>').attr({type: 'hidden', name: 'declaration_handover_confirmed', value: '1'}).appendTo('#leaveApplicationForm');
        $('<input>').attr({type: 'hidden', name: 'declaration_policy_agreed', value: '1'}).appendTo('#leaveApplicationForm');

        $('#leaveApplicationForm')[0].submit();
    });
});
</script>
