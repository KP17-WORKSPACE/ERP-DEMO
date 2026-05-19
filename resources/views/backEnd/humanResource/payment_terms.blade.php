@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        $canAdd = in_array(105, @$module_links) || Auth::user()->role_id == 1;
    @endphp
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div id="data-details" role="tabpanel" aria-labelledby="data-tab">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="mb-0">Payment Terms</h4>
                    <div class="d-flex align-items-center gap-2">
                        @if ($canAdd)
                            <button type="button" class="btn btn-light text-dark" id="btnOpenAddPaymentTerm">
                                <i class="ico icon-outline-add-square text-success"></i> Add
                            </button>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('company/policy') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Company Policy
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('/department') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Department
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('/designation') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Designation
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('/legal-entity') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Business Entity
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('/industry') }}">
                                        <i class="ico icon-outline-layers text-success title-15 me-2"></i>
                                        Industry Type
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('/business-activity') }}">
                                        <i class="ico icon-outline-layers text-success title-15 me-2"></i>
                                        Business Sector
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ route('role') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Role
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('module') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Module
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ route('base_setup') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Base Setup
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ route('daily-quotes.index') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Daily Quote
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('currency-settings') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Manage Currency
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('company') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Company Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('payment-cheque-print-template') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Cheque Print Templates
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('shipping-add') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Shipping
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('vat-settings') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        VAT Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('accountgroup-add') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Main Heads
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('book-close') }}">
                                        <i class="ico icon-outline-settings text-success title-15 me-2"></i>
                                        Book Closed
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark"
                                        href="{{ url('book-close-doc-number') }}">
                                        <i class="ico icon-outline-settings text-success title-15 me-2"></i>
                                        Book Close Doc No
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if (session()->has('message-success') || session()->has('message-danger') || session()->has('message-success-delete') || session()->has('message-danger-delete'))
                    <div class="mb-3">
                        @if (session()->has('message-success'))
                            <div class="alert alert-success mb-2">{{ session()->get('message-success') }}</div>
                        @endif
                        @if (session()->has('message-danger'))
                            <div class="alert alert-danger mb-2">{{ session()->get('message-danger') }}</div>
                        @endif
                        @if (session()->has('message-success-delete'))
                            <div class="alert alert-success mb-2">{{ session()->get('message-success-delete') }}</div>
                        @endif
                        @if (session()->has('message-danger-delete'))
                            <div class="alert alert-danger mb-2">{{ session()->get('message-danger-delete') }}</div>
                        @endif
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-body">
                        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>@lang('Payment Terms')</th>
                                    <th>Schedule</th>
                                    <th>@lang('Created By')</th>
                                    <th class="text-center" width="100">@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paymentterms as $paymentterm)
                                    @php
                                        $schedule = $paymentterm->payment_schedule;
                                        if (is_string($schedule)) {
                                            $schedule = json_decode($schedule, true) ?: [];
                                        }
                                        if (!is_array($schedule)) {
                                            $schedule = [];
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ @$paymentterm->title }}</td>
                                        <td>
                                            @if (!empty($schedule))
                                                @foreach ($schedule as $row)
                                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                                        {{ rtrim(rtrim(number_format((float) ($row['percentage'] ?? 0), 2), '0'), '.') }}%
                                                        / {{ (int) ($row['days'] ?? 0) }} days
                                                    </span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ @$paymentterm->createdby->full_name }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-light btn-edit-payment-term"
                                                    data-id="{{ $paymentterm->id }}"
                                                    data-title="{{ e($paymentterm->title) }}"
                                                    data-schedule='@json($schedule)'>
                                                    <i class="ico icon-outline-pen-2 text-success" style="font-size: 16px;"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                    data-bs-target="#deletePaymentTermModal{{ $paymentterm->id }}">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-danger"
                                                        style="font-size: 16px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal side-panel fade" id="deletePaymentTermModal{{ $paymentterm->id }}"
                                        data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Delete Payment Terms</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center py-4">
                                                    <div class="mb-3">
                                                        <i class="ico icon-bold-trash-bin-2 text-danger"
                                                            style="font-size: 40px;"></i>
                                                    </div>
                                                    <h5 class="fw-semibold mb-2">@lang('lang.are_you_sure_to_delete')</h5>
                                                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    {{ Form::open(['url' => 'payment-terms/' . $paymentterm->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                                    <button type="submit" class="btn btn-light">
                                                        <i class="ico icon-outline-trash-bin-minimalistic text-danger"></i>
                                                        Delete
                                                    </button>
                                                    {{ Form::close() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Add / Edit Modal --}}
    <div class="modal fade" id="paymentTermModal" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="paymentTermModalTitle">Add Payment Terms</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="paymentTermForm" method="POST" action="{{ url('payment-terms') }}">
                    @csrf
                    <input type="hidden" name="id" id="payment_term_id" value="">
                    <div class="modal-body">
                        <label class="form-label">@lang('Payment Terms') @lang('lang.title') <span class="text-danger">*</span></label>
                        <input class="form-control mb-3" type="text" name="title" id="payment_term_title"
                            autocomplete="off" required>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Payment Schedule <span class="text-danger">*</span></label>
                            <small class="text-muted" style="width: 214px;">Total must be exactly 100%</small>
                        </div>

                        <div id="paymentScheduleRows" class="mb-2"></div>

                        <div class="d-flex justify-content-end align-items-center">
                            <span id="scheduleTotalLabel" class="fw-semibold text-danger">Total: 0%</span>
                        </div>
                        <div id="scheduleValidationMsg" class="text-danger small mt-2 d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-light" id="btnPaymentTermSubmit" disabled>
                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                            <span id="btnPaymentTermSubmitText">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>

    <script>
        (function() {
            const form = document.getElementById('paymentTermForm');
            const rowsContainer = document.getElementById('paymentScheduleRows');
            const totalLabel = document.getElementById('scheduleTotalLabel');
            const validationMsg = document.getElementById('scheduleValidationMsg');
            const submitBtn = document.getElementById('btnPaymentTermSubmit');
            const storeUrl = {!! json_encode(url('payment-terms')) !!};

            function escapeHtml(str) {
                if (str === null || str === undefined) {
                    str = '';
                }
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function buildScheduleRow(percentage = '', days = '') {
                const row = document.createElement('div');
                row.className = 'payment-schedule-row row g-2 align-items-end mb-2';
                row.innerHTML = `
                    <div class="col-5">
                        <label class="form-label small mb-1">Percentage (%)</label>
                        <input type="number" class="form-control schedule-percentage" name="percentages[]"
                            min="0.01" max="100" step="0.01" placeholder="%" value="${escapeHtml(percentage)}" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small mb-1">Days</label>
                        <input type="number" class="form-control schedule-days" name="days[]"
                            min="0" step="1" placeholder="Days" value="${escapeHtml(days)}" required>
                    </div>
                    <div class="col-2 d-flex gap-1 pb-1">
                        <button type="button" class="btn btn-sm btn-light btn-add-schedule-row" title="Add row">
                            <i class="ico icon-outline-add-square text-success"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light btn-remove-schedule-row" title="Remove row">
                            <i class="ico icon-outline-minus-square text-danger"></i>
                        </button>
                    </div>
                `;
                return row;
            }

            function getScheduleTotal() {
                let total = 0;
                rowsContainer.querySelectorAll('.schedule-percentage').forEach(function(input) {
                    const val = parseFloat(input.value);
                    if (!isNaN(val)) {
                        total += val;
                    }
                });
                return Math.round(total * 100) / 100;
            }

            function updateScheduleUI() {
                const total = getScheduleTotal();
                const rowCount = rowsContainer.querySelectorAll('.payment-schedule-row').length;

                totalLabel.textContent = 'Total: ' + total + '%';
                totalLabel.classList.remove('text-success', 'text-danger', 'text-warning');

                rowsContainer.querySelectorAll('.btn-remove-schedule-row').forEach(function(btn) {
                    btn.disabled = rowCount <= 1;
                });

                if (total === 100) {
                    totalLabel.classList.add('text-success');
                    validationMsg.classList.add('d-none');
                    submitBtn.disabled = false;
                } else {
                    totalLabel.classList.add(total > 100 ? 'text-danger' : 'text-warning');
                    validationMsg.textContent = total > 100
                        ? 'Total exceeds 100%. Remove or reduce percentages.'
                        : 'Total must be exactly 100% before saving.';
                    validationMsg.classList.remove('d-none');
                    submitBtn.disabled = true;
                }
            }

            function parseScheduleData(raw) {
                if (!raw) {
                    return [];
                }
                if (typeof raw === 'string') {
                    try {
                        return JSON.parse(raw);
                    } catch (err) {
                        var textarea = document.createElement('textarea');
                        textarea.innerHTML = raw;
                        try {
                            return JSON.parse(textarea.value);
                        } catch (err2) {
                            return [];
                        }
                    }
                }
                return Array.isArray(raw) ? raw : [];
            }

            function normalizeSchedule(schedule) {
                return parseScheduleData(schedule).map(function(item) {
                    return {
                        percentage: item && item.percentage !== undefined && item.percentage !== null ? item.percentage : '',
                        days: item && item.days !== undefined && item.days !== null ? item.days : ''
                    };
                });
            }

            function resetScheduleRows(schedule, isEdit) {
                rowsContainer.innerHTML = '';
                var rows = normalizeSchedule(schedule);
                if (!rows.length) {
                    rows = isEdit
                        ? [{ percentage: '', days: '' }]
                        : [{ percentage: 100, days: 0 }];
                }
                rows.forEach(function(item) {
                    rowsContainer.appendChild(buildScheduleRow(item.percentage, item.days));
                });
                updateScheduleUI();
            }

            function openAddPaymentTerm() {
                document.getElementById('paymentTermModalTitle').textContent = 'Add Payment Terms';
                document.getElementById('btnPaymentTermSubmitText').textContent = 'Save';
                document.getElementById('payment_term_id').value = '';
                document.getElementById('payment_term_title').value = '';
                form.action = storeUrl;
                form.querySelectorAll('input[name="_method"]').forEach(function(el) {
                    el.remove();
                });
                resetScheduleRows([{ percentage: 100, days: 0 }], false);
                new bootstrap.Modal(document.getElementById('paymentTermModal')).show();
            }

            function openEditPaymentTerm(id, title, schedule) {
                document.getElementById('paymentTermModalTitle').textContent = 'Edit Payment Terms';
                document.getElementById('btnPaymentTermSubmitText').textContent = 'Update';
                document.getElementById('payment_term_id').value = id;
                document.getElementById('payment_term_title').value = title || '';
                form.action = storeUrl + '/' + id;
                form.querySelectorAll('input[name="_method"]').forEach(function(el) {
                    el.remove();
                });
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
                resetScheduleRows(schedule, true);
                new bootstrap.Modal(document.getElementById('paymentTermModal')).show();
            }

            var btnOpenAdd = document.getElementById('btnOpenAddPaymentTerm');
            if (btnOpenAdd) {
                btnOpenAdd.addEventListener('click', openAddPaymentTerm);
            }

            document.querySelectorAll('.btn-edit-payment-term').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var schedule = parseScheduleData(btn.getAttribute('data-schedule'));
                    openEditPaymentTerm(
                        btn.getAttribute('data-id'),
                        btn.getAttribute('data-title') || '',
                        schedule
                    );
                });
            });

            rowsContainer.addEventListener('click', function(e) {
                const addBtn = e.target.closest('.btn-add-schedule-row');
                const removeBtn = e.target.closest('.btn-remove-schedule-row');
                if (addBtn) {
                    const row = addBtn.closest('.payment-schedule-row');
                    row.parentNode.insertBefore(buildScheduleRow(), row.nextSibling);
                    updateScheduleUI();
                }
                if (removeBtn && rowsContainer.querySelectorAll('.payment-schedule-row').length > 1) {
                    removeBtn.closest('.payment-schedule-row').remove();
                    updateScheduleUI();
                }
            });

            rowsContainer.addEventListener('input', updateScheduleUI);

            form.addEventListener('submit', function(e) {
                if (getScheduleTotal() !== 100) {
                    e.preventDefault();
                    updateScheduleUI();
                    return false;
                }
                submitBtn.disabled = true;
            });
        })();
    </script>

@endsection
