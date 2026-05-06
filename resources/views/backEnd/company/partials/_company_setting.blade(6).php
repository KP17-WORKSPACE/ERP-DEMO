@php
    $settings = $company->settings ?? null;
@endphp

<div class="accordion" id="settingsAccordion">

    {{-- ======================= 1. COMPANY SETTINGS ======================= --}}
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseCompanySetting">
                1. Account Setting
            </button>
        </h2>
        <div id="collapseCompanySetting" class="accordion-collapse collapse show" data-bs-parent="#settingsAccordion">
            <div class="accordion-body">
                <div class="row gy-2">

                    {{-- Currency --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1">Currency</label>
                        <select name="currency" id="settingCurrency" class="form-select form-select-sm setting-input">
                            <option value="" data-symbol="" data-rate="" data-rcode="" data-pcode="">Select Currency</option>
                            @foreach ($currency as $c)
                                <option value="{{ $c->code }}"
                                    data-symbol="{{ $c->symbol }}"
                                    data-rate="{{ $c->rate }}"
                                    data-rcode="{{ $c->r_code }}"
                                    data-pcode="{{ $c->p_code }}"
                                    {{ old('currency', $settings->currency ?? '') == $c->code ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Currency Symbol --}}
                    <div class="col-lg-1">
                        <label class="form-label mb-1">Symbol</label>
                        <input type="text" name="currency_symbol" id="currencySymbol"
                               class="form-control form-control-sm setting-input"
                               value="{{ old('currency_symbol', $settings->currency_symbol ?? '') }}">
                    </div>

                    {{-- Currency Digit Display --}}
                    <div class="col-lg-1">
                        <label class="form-label mb-1">Currency Digit</label>
                        <input type="number" name="currency_digit"
                               min="0"
                               class="form-control form-control-sm setting-input"
                               value="{{ old('currency_digit', $settings->currency_digit ?? 2) }}">
                    </div>

                    {{-- R Code --}}
                    <div class="col-lg-1">
                        <label class="form-label mb-1">R Code</label>
                        <input type="text" name="r_code" id="currencyRCode"
                               class="form-control form-control-sm setting-input"
                               value="{{ old('r_code', $settings->r_code ?? '') }}">
                    </div>

                    {{-- P Code --}}
                    <div class="col-lg-1">
                        <label class="form-label mb-1">P Code</label>
                        <input type="text" name="p_code" id="currencyPCode"
                               class="form-control form-control-sm setting-input"
                               value="{{ old('p_code', $settings->p_code ?? '') }}">
                    </div>

                    {{-- Book Closed --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1">Book Closed</label>
                        <input type="text" name="book_closed"
                               class="form-control form-control-sm date-picker setting-input"
                               value="{{ old('book_closed',
                                    optional($settings)->book_closed
                                        ? \Carbon\Carbon::parse($settings->book_closed)->format('Y-m-d')
                                        : ''
                               ) }}">
                    </div>

                    {{-- Sales Code --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1">Sales Code <span class="text-danger">*</span></label>
                        <input type="text" name="sales_code"
                               class="form-control form-control-sm setting-input"
                               value="{{ old('sales_code', $settings->sales_code ?? '') }}">
                    </div>

                    {{-- Other Code --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1">All Code <span class="text-danger">*</span></label>
                        <input type="text" name="other_code"
                               class="form-control form-control-sm setting-input"
                               value="{{ old('other_code', $settings->other_code ?? '') }}">
                    </div>

                    {{-- Customer Code --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1 d-block">Customer Code</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input setting-input"
                                   type="checkbox"
                                   name="is_customer_code"
                                   value="1"
                                   {{ old('is_customer_code', $settings->is_customer_code ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Supplier Code --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1 d-block">Supplier Code</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input setting-input"
                                   type="checkbox"
                                   name="is_supplier_code"
                                   value="1"
                                   {{ old('is_supplier_code', $settings->is_supplier_code ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Account Code --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1 d-block">Account Code</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input setting-input"
                                   type="checkbox"
                                   name="is_account_code"
                                   value="1"
                                   {{ old('is_account_code', $settings->is_account_code ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- Sub Account Code --}}
                    <div class="col-lg-2">
                        <label class="form-label mb-1 d-block">Sub Account Code</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input setting-input"
                                   type="checkbox"
                                   name="is_subaccount_code"
                                   value="1"
                                   {{ old('is_subaccount_code', $settings->is_subaccount_code ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Account code settings moved into Company Setting section -->

</div>

{{-- Settings are saved as part of master Save button — no local Save button needed --}}

<script>
    $(document).ready(function() {
        // Currency selection handler - auto-fill related fields
        $('#settingCurrency').on('change', function() {
            var $selected = $(this).find('option:selected');
            
            $('#currencySymbol').val($selected.data('symbol') || '');
            $('#currencyRCode').val($selected.data('rcode') || '');
            $('#currencyPCode').val($selected.data('pcode') || '');
            
            // Also update the form input fields with currency dropdown data
            $('input[name="currency_symbol"]').val($selected.data('symbol') || '');
            $('input[name="r_code"]').val($selected.data('rcode') || '');
            $('input[name="p_code"]').val($selected.data('pcode') || '');
        });

        // Trigger on page load if currency is already selected
        if ($('#settingCurrency').val()) {
            $('#settingCurrency').trigger('change');
        }
    });
</script>
