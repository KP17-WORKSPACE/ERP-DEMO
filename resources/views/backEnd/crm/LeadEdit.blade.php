<?php try { ?>






{{ Form::open([
    'class' => 'form-horizontal',
    'files' => true,
    'route' => ['crm-leads.update', $edit->id],
    'method' => 'PUT',
    'enctype' => 'multipart/form-data',
    'id' => 'crm-leads-edit-form',
]) }}

<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2 sticky-top" style="background-color: #f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left mb-0">
                Edit - <span class="font-weight-600" id="new_code">{{ $edit->code }}</span>
            </h4>
        
        </div>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">

            <a class="btn btn-light text-dark" href="{{url('crm-leads/show?lead_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
            </button>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">

                </ul>
            </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">



            <div class="row" id="top-row">


                <div class="col-4 mb-2">
                    <label class="form-label">Customer</label>
                    <select class="form-control js-example-basic-single" name="company_name" id="edit_company_name"
                        required>

                        @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" @if ($edit->cust_id == $value->id) selected @endif>
                                {{ trim(@$value->customer_name_display) }}@if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code']) ({{ trim(@$value->code) }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="lead_id" id="edit_lead_id" value="{{ $edit->id }}">
                <div class="col-2 mb-2">
                    <label class="form-label">Lead Name</label>

                    <input class="form-control capitalize-title" type="text" name="lead_name" autocomplete="off" id="edit_lead_name"
                        value="{{ $edit->lead_name }}" required>
                </div>


                  

                @php
                    // Convert comma-separated tags into array
                    $tagsArray = isset($edit->tags) ? explode(',', $edit->tags) : [];
                @endphp

                <div class="col-2 mb-2">
                    <label class="form-label" for="">
                        Brand
                        <a href="#" class="text-success ms-2" style="float:right" data-bs-toggle="modal" data-bs-target="#addBrand" title="Add Brand">
                            <i class="ico icon-outline-add-square"></i>
                        </a>
                    </label>
                    <select class="form-control js-example-basic-single" name="tags[]" id="edit_tags" multiple required>
                        @foreach ($brand as $value)
                            <option value="{{ $value->title }}"
                                {{ in_array($value->title, $tagsArray) ? 'selected' : '' }}>
                                {{ $value->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Sales Person</label>
                    <select class="form-control js-example-basic-single" name="owner" id="edit_owner" required>
                        @foreach ($staff as $value)
                            <option value="{{ @$value->user_id }}" @if ($edit->owner == $value->id) selected @endif>
                                {{ @$value->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- @if (session('logged_session_data.company_id') == 1) --}}

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Company</label>
                    <select class="form-control js-example-basic-single" name="company" id="edit_company" required>

                        @foreach ($company as $value)
                            <option value="{{ $value->id }}" @if ($edit->company_id == $value->id) selected @endif>
                                {{ @$value->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- @else
                    <input type="hidden" name="company" id="edit_company_hidden" value="{{ $edit->company_id }}" />

                @endif --}}


            </div>

        </div>
    </div>
    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                    data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                    aria-selected="true">Extra Fields</button>
            </li>
        </ul>
       
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">

               <div class="row gap-rows">
                 <div class="col-2 mb-2">
                    <label class="form-label" for="">Contact Person Name</label>
                    <input class="form-control capitalize-title" type="text" name="cust_name" autocomplete="off" id="edit_cust_name"
                        value="{{ $edit->cust_name }}" required>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Designation</label>
                    

                        
                        <select class="form-control js-example-basic-single" name="cust_designation"
                                        id="edit_cust_designation">
                                        <option value="">--Designation--</option>
                                        @if (count($designation) > 0)
                                            @foreach ($designation as $val)
                                                <option value="{{ $val->title }}"
                                                    {{ trim(strtolower($val->title)) == trim(strtolower($edit->cust_designation)) ? 'selected' : '' }}
                                                    aria-describedby="">{{ $val->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                </div>


                <div class="col-2 mb-2">
                    <label class="form-label" for="">Mobile</label>
                    <input class="form-control" type="text" name="cust_no" autocomplete="off" id="edit_cust_no"
                        value="{{ $edit->cust_no }}">

                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Email</label>
                    <input class="form-control" type="text" name="cust_email" autocomplete="off" id="edit_cust_email" data-bs-toggle="modal" data-bs-target="#EmailModal"
                        value="{{ $edit->cust_email }}">

                </div>


                <div class="col-2 mb-2">
                    <label class="form-label" for="">Address</label>
                    <input class="form-control" type="text" name="address" autocomplete="off" id="edit_address"  data-bs-toggle="modal" data-bs-target="#AddressModal"
                        value="{{ $edit->address }}">
                </div>



              

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Source</label>
                    <select class="form-control js-example-basic-single" name="source" id="edit_source">
                        <option value="">-Select-</option>
                        <option value="Chat" @if ($edit->source == 'Chat') selected @endif>Chat</option>
                        <option value="Call" @if ($edit->source == 'Call') selected @endif>Call</option>
                        <option value="Mail" @if ($edit->source == 'Mail') selected @endif>Mail</option>
                        <option value="Website" @if ($edit->source == 'Website') selected @endif>Website</option>
                        <option value="Gitex 2023" @if ($edit->source == 'Gitex 2023') selected @endif>Gitex 2023
                        </option>
                        <option value="Gitex" @if ($edit->source == 'Gitex') selected @endif>Gitex</option>
                        <option value="Ecommerce" @if ($edit->source == 'Ecommerce') selected @endif>Ecommerce</option>
                        <option value="Other" @if ($edit->source == 'Other') selected @endif>Other</option>
                    </select>
                </div>


                <div class="col-2 mb-2" id="editsourcediv" style="display: none;">
                    <label class="form-label" for="">Other Source</label>
                    <input class="form-control" type="text" name="source_o" autocomplete="off"
                        id="edit_source_o" value="{{ $edit->source_o }}" style="display: none;"
                        placeholder="Source">

                </div>

                {{-- <div class="col-2 mb-2"> --}}
                    {{-- <label class="form-label" for="">Created By</label> --}}
                    <input class="form-control" type="hidden" name="createdby" autocomplete="off"
                        id="edit_createdby" value="{{ $edit->createdby->full_name }}" readonly>
                {{-- </div> --}}

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Date</label>

                    <input class="form-control date-picker" id="edit_date" type="text" autocomplete="off"
                        name="date" value="{{ @App\SysHelper::normalizeToDmy($edit->date) }}" required>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Lead Type</label>
                    <select class="form-control js-example-basic-single" name="isproject" id="edit_isproject">
                        <option value="4" @if ($edit->isproject == 4) selected @endif>Project</option>
                        <option value="1" @if ($edit->isproject == 1) selected @endif>Reseller</option>
                        <option value="2" @if ($edit->isproject == 2) selected @endif>Enduser</option>
                        <option value="3" @if ($edit->isproject == 3) selected @endif>E-Commerce</option>
                    </select>
                </div>


                <div class="col-2 mb-2">
                    <label class="form-label" for="">Status</label>
                    <select class="form-control js-example-basic-single" name="status" id="edit_status_new"
                        required>
                        <option value="1" @if ($edit->status == 1) selected @endif>New</option>
                        <option value="2" @if ($edit->status == 2) selected @endif>Qualified</option>
                        <option value="3" @if ($edit->status == 3) selected @endif>Unqualified</option>
                        <option value="4" @if ($edit->status == 4) selected @endif>Pending Response
                        </option>
                        <option value="10" @if ($edit->status == 10) selected @endif>Closed</option>
                    </select>
                </div>

               

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Attachment</label>
                    <input type="file" class="form-control" name="doc[]" id="doc" multiple="multiple">

                </div>





                <div class="mb-2 col-4">
                    <label class="form-label" for="">Notes</label>
                    <input class="form-control" name="note" rows="3" data-bs-toggle="modal" data-bs-target="#narrationModal" autocomplete="off" id="edit_note" value="{{$edit->note}}">
                </div>
               </div>

            </div>
        </div>
    </div>

    


</div>




{{ Form::close() }}




<div class="tab-wrap mb-3" id="internal-note">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="internal-notes-tab" data-bs-toggle="tab"
                data-bs-target="#internal-notes" type="button" role="tab" aria-controls="internal-notes"
                aria-selected="true">Internal Notes</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="internal-notes" role="tabpanel"
            aria-labelledby="internal-notes-tab">
            <!-- <h4 class="mb-3 color-sub-head">Shipping Address</h4> -->
            <div class="row">


                <div class="col-7">
                    <div class="">
                        @if ($edit->note != '')
                            <b>Lead Notes :- </b>

                            <div class="mt-2" style="">


                                                <div class="card border-0 rounded-3 mb-2">
                            <div class="card-body py-0">

                            

                                <!-- Top Row: Right-Aligned Icons -->
                                <div class="d-flex justify-content-between mb-0">


                        <!-- Comment -->
                                <p class="mb-0 text-break fw-semibold" style="font-size:11px">
                                        {!!   nl2br($edit->note) !!}

                                </p>


                                <div class="d-flex align-items-baseline gap-2">
                                        @if ($edit->doc)
                                                        <a href="{{ asset('public/uploads/crm_lead_doc/' . $edit->doc) }}"
                                                        target="_blank" class="btn btn-sm btn-light me-1"  style="min-height:17px">
                                                            <i class="ico icon-bold-paperclip" style="font-size:11px"></i>
                                                        </a>
                                                    @endif
                                                 
                                </div>

                                </div>

                                <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                <div class="text-end small text-muted">

                                    <span style="font-size:10px">
                                        
                                        {{ $edit->createdby->first_name }} {{ $edit->createdby->last_name }}
                                    </span>

                                    <span>•</span>

                                    <span style="font-size:10px">
                                        <i class="ico icon-bold-clock me-1"></i>
                                        {{ date('d/m/Y h:i A', strtotime($edit->created_at)) }}
                                    </span>

                                    @if ($edit->deleted_at)
                                    <span>•</span>
                                        
                                        <span class="text-danger" style="font-size:10px">
                                            Deleted: {{ date('d/m/Y h:i A', strtotime($edit->deleted_at)) }}
                                        </span>
                                    @endif

                                </div>

                            </div>
</div>
    
                              
                           
                            </div>

        
                        @endif


                        @if (isset($comments))
                            <div class="mt-2" style="">
                                @foreach ($comments as $cmts)


                            <div class="card border-0 rounded-3 mb-2">
                            <div class="card-body py-0">

                            

                                <!-- Top Row: Right-Aligned Icons -->
                                <div class="d-flex justify-content-between mb-0">


                        <!-- Comment -->
                                <p class="mb-0 fw-semibold text-break @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif" style="font-size:11px">
                                     {!!   nl2br($cmts->comments) !!}
                                </p>


                                <div class="d-flex align-items-baseline gap-2">
                                        @if ($cmts->commentsdoc)
                                                        <a href="{{ asset('public/uploads/crm_lead_doc/' . $cmts->commentsdoc) }}"
                                                        target="_blank" class="btn btn-sm btn-light me-1" style="min-height:17px">
                                                            <i class="ico icon-bold-paperclip" style="font-size:11px"></i>
                                                        </a>
                                                    @endif

                                                    @if ($cmts->created_by == Auth::user()->id)
                                                        @if ($cmts->deleted_at)
                                                            <a href="{{ url('crm-leads-comments-restore/' . $cmts->id) }}"
                                                            onclick="return confirm('Are you sure you want to restore this comment?')"
                                                            class="btn btn-sm btn-light" style="min-height:17px">
                                                                <i class="ico icon-bold-restart" style="font-size:11px"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ url('crm-leads-comments-delete/' . $cmts->id) }}"
                                                            onclick="return confirm('Are you sure you want to delete this comment?')"
                                                            class="btn btn-sm btn-light" style="min-height:17px">
                                                                <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:11px"></i>
                                                            </a>
                                                        @endif
                                            @endif
                                </div>


                                

                                </div>

                                <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                <div class="text-end small text-muted">

                                    <span style="font-size:10px">
                          
                                        {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}
                                    </span>

                                    <span>•</span>

                                    <span style="font-size:10px">
                                        <i class="ico icon-bold-clock me-1"></i>
                                        {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                    </span>

                                    @if ($cmts->deleted_at)
                                    <span>•</span>
                                        
                                        <span class="text-danger" style="font-size:10px">
                                            Deleted: {{ date('d/m/Y h:i A', strtotime($cmts->deleted_at)) }}
                                        </span>
                                    @endif

                                </div>

                            </div>
                             </div>

                                    
                                @endforeach
                            </div>

                        @endif



                    </div>
                </div>

                <div class="col-5">
                    <label class="font-weight-bold form-label">Internal Note</label>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-comments-add', 'method' => 'POST', 'id' => 'crm-leads-comments-add']) }}
                    <textarea name="comments" class="form-control capitalize-title" id="" cols="10" rows="3" required></textarea>
                   
                    <input type="hidden" id="commentsid" name="commentsid" value="{{ $edit->id }}" />
                      <div class="row mt-2">

                            <div class="col-md-4 d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-light   d-flex align-items-center gap-2">
                                                <i class="ico icon-outline-add-square fs-5 text-success "></i>
                                                <span>Add Note</span>
                                            </button>
                            </div>
                            <div class="col-md-8">
                                <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                            </div>

                        
                        </div>

                    {{ Form::close() }}
                </div>



            </div>


        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('edit_note');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('show.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
        });

        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });
</script>

<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Notes</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea" rows="6"
                            placeholder="Write narration here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>




<script>

    
    $(document).ready(function() {
        // Simple Enter key navigation for #top-row - jump to next field
        $('#top-row').on('keydown', 'input, select, textarea', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                
                // Get all focusable elements in top-row
                var $focusableElements = $('#top-row').find('input:visible:not([disabled]):not([readonly]), select:visible:not([disabled]), textarea:visible:not([disabled]):not([readonly])');
                var currentIndex = $focusableElements.index(this);
                
                // Move to next element
                if (currentIndex > -1 && currentIndex < $focusableElements.length - 1) {
                    var $nextElement = $focusableElements.eq(currentIndex + 1);
                    
                    // Focus or open Select2 dropdown
                    if ($nextElement.hasClass('js-example-basic-single') || $nextElement.hasClass('select2-hidden-accessible')) {
                        $nextElement.select2('open');
                    } else {
                        $nextElement.focus();
                    }
                }
            }
        });



                // When Company select2 opens, prefill the search box with the currently selected option
        // so the user can edit/change the selection easily.
        $('#edit_company_name').on('select2:open', function() {
            var selectedText = $(this).find('option:selected').text().trim();
            var $search = $('.select2-container--open .select2-search__field');
            if ($search.length) {
                // Don't prefill if placeholder or empty
                if (selectedText && selectedText !== 'Select') {
                    $search.val(selectedText);
                    // trigger input so Select2 reacts to the injected value
                    $search.trigger('input');

                    // move cursor to end for easier editing (works in modern browsers)
                    var el = $search.get(0);
                    try {
                        if (el && el.setSelectionRange) {
                            var len = selectedText.length * 2; // safe trick to put cursor at the end
                            el.setSelectionRange(len, len);
                        }
                    } catch (e) {
                        // ignore if setSelectionRange not supported
                    }
                } else {
                    $search.val('');
                    $search.trigger('input');
                }
            }
        });

    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput2Address = document.getElementById('edit_address');
        const narrationTextarea2Address = document.getElementById('narrationTextarea2Address');
        const insertButton2Address = document.getElementById('insertNarration2Address');
        const narrationModal2Address = document.getElementById('AddressModal');

        // Pre-fill textarea when modal opens
        narrationModal2Address.addEventListener('show.bs.modal', () => {
            narrationTextarea2Address.value = referenceInput2Address.value;
        });

        // On insert button click, update input and close modal
        insertButton2Address.addEventListener('click', () => {
            referenceInput2Address.value = narrationTextarea2Address.value;
            bootstrap.Modal.getInstance(narrationModal2Address).hide();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput2Email = document.getElementById('edit_cust_email');
        const narrationTextarea2Email = document.getElementById('narrationTextarea2Email');
        const insertButton2Email = document.getElementById('insertNarration2Email');
        const narrationModal2Email = document.getElementById('EmailModal');

        // Pre-fill textarea when modal opens
        narrationModal2Email.addEventListener('show.bs.modal', () => {
            narrationTextarea2Email.value = referenceInput2Email.value;
        });

        // On insert button click, update input and close modal
        insertButton2Email.addEventListener('click', () => {
            referenceInput2Email.value = narrationTextarea2Email.value;
            bootstrap.Modal.getInstance(narrationModal2Email).hide();
        });
    });
</script>



<div class="modal side-panel fade" id="AddressModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea2Address" rows="6"
                            placeholder="Write address here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2Address" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="EmailModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Email</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <input class="form-control" id="narrationTextarea2Email" 
                            placeholder="Write email here...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2Email" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="addBrand" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 157px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Add Brand</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{ Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'brand',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
                'id' => 'addBrandForm',
            ]) }}
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="form-label">@lang('Brand') @lang('Name')<span>*</span></label>
                                        <input class="primary-input form-control" type="text" name="title" id="new_brand_title"
                                            autocomplete="off" value="">
                                        <input type="hidden" name="id" value="">
                                        <span class="focus-border"></span>
                                        <div id="new_brand_error" class="text-danger mt-1" style="display:none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="saveBrandAjax">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#saveBrandAjax').on('click', function(e) {
            e.preventDefault();

            var title = $('#new_brand_title').val().trim();
            var $error = $('#new_brand_error');
            $error.hide().text('');

            if (!title) {
                $error.text('Brand name is required.').show();
                return;
            }

            var $button = $(this);
            $button.prop('disabled', true);

            $.ajax({
                url: "{{ url('brand') }}",
                type: 'POST',
                data: {
                    title: title,
                    _token: $('input[name="_token"]').first().val()
                },
                success: function(response) {
                    if (response.success && response.title) {
                        var newOption = new Option(response.title, response.title, true, true);
                        $('#edit_tags').append(newOption).trigger('change');
                        $('#new_brand_title').val('');
                        $('#addBrand').modal('hide');
                    } else {
                        $error.text(response.message || 'Unable to save brand.').show();
                    }
                },
                error: function(xhr) {
                    var message = 'Unable to save brand. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.title) {
                        message = xhr.responseJSON.errors.title[0];
                    }
                    $error.text(message).show();
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });

        $('#addBrandForm').on('submit', function(e) {
            e.preventDefault();
            $('#saveBrandAjax').trigger('click');
        });

        $('#addBrand').on('shown.bs.modal', function() {
            $('#new_brand_title').trigger('focus');
        });

        $('#addBrand').on('hidden.bs.modal', function() {
            $('#new_brand_title').val('');
            $('#new_brand_error').hide().text('');
        });
    });
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
