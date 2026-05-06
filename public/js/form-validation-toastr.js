/**
 * Universal Form Validation with Toastr Notifications
 * 
 * Usage:
 *   1. Include this script after jQuery and Toastr
 *   2. Initialize: FormValidator.init('your-form-id');
 *   3. Or with options: FormValidator.init('your-form-id', { showAllErrors: true });
 * 
 * Options:
 *   - showAllErrors: boolean (default: true) - Show all errors at once or one by one
 *   - scrollToFirst: boolean (default: true) - Scroll to first invalid field
 *   - highlightFields: boolean (default: true) - Add error class to invalid fields
 *   - errorClass: string (default: 'is-invalid') - CSS class for invalid fields
 *   - toastrPosition: string (default: 'toast-top-right') - Toastr position
 *   - toastrTimeout: number (default: 5000) - Toastr display time in ms
 *   - activateTab: boolean (default: true) - Activate tab containing first invalid field
 *   - tabAnimationDelay: number (default: 150) - Delay in ms before focusing field after tab switch
 * 
 * @version 1.1.0
 * @author VENUS ERP
 */

var FormValidator = (function($) {
    'use strict';

    // Default configuration
    var defaults = {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        errorClass: 'is-invalid',
        successClass: 'is-valid1',
        toastrPosition: 'toast-top-right',
        toastrTimeout: 5000,
        activateTab: true,           // Auto-activate tab containing invalid field
        tabAnimationDelay: 150,      // Delay before focus after tab activation
        onValidationFail: null,      // callback function
        onValidationPass: null,      // callback function
        customValidators: {}         // custom validation rules
    };

    var settings = {};
    var formId = '';

    /**
     * Initialize the validator
     * @param {string} formIdParam - The form ID (without #)
     * @param {object} options - Configuration options
     */
    function init(formIdParam, options) {
        formId = formIdParam;
        settings = $.extend({}, defaults, options);

        // Configure toastr
        configureToastr();

        // Inject validation highlight CSS if not already present
        injectHighlightStyles();

        // Bind form submit event
        bindFormSubmit();

        // Bind real-time validation on blur (optional)
        bindFieldBlur();

        return this;
    }

    /**
     * Inject CSS styles for validation highlight animation
     */
    function injectHighlightStyles() {
        if ($('#form-validator-styles').length) return;

        var styles = 
            '<style id="form-validator-styles">' +
            '.validation-highlight {' +
            '  animation: validationPulse 0.5s ease-in-out 2;' +
            '  box-shadow: 0 0 0 1px rgba(220, 53, 69, 0.5) !important;' +
            '}' +
            '@keyframes validationPulse {' +
            '  0%, 100% { box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.5); }' +
            '  50% { box-shadow: 0 0 0 6px rgba(220, 53, 69, 0.3); }' +
            '}' +
            '.select2-container--default .select2-selection--single.is-invalid,' +
            '.select2-container--default .select2-selection--multiple.is-invalid {' +
            '  border-color: #dc3545 !important;' +
            '}' +
            '</style>';

        $('head').append(styles);
    }

    /**
     * Configure toastr settings
     */
    function configureToastr() {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: true,
                progressBar: true,
                positionClass: settings.toastrPosition,
                preventDuplicates: true,
                onclick: null,
                showDuration: '300',
                hideDuration: '1000',
                timeOut: settings.toastrTimeout,
                extendedTimeOut: 0,              // Don't extend on hover - closes immediately after timeout
                showEasing: 'swing',
                hideEasing: 'linear',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut',
                tapToDismiss: true               // Click to dismiss
            };
        }
    }

    /**
     * Bind form submit event
     */
    function bindFormSubmit() {
        $('#' + formId).on('submit', function(e) {
            var validationResult = validateForm();

            if (!validationResult.isValid) {
                e.preventDefault();
                e.stopPropagation();

                showValidationErrors(validationResult.errors);

                if (settings.scrollToFirst && validationResult.firstInvalidField) {
                    activateTabAndFocus(validationResult.firstInvalidField);
                }

                if (typeof settings.onValidationFail === 'function') {
                    settings.onValidationFail(validationResult.errors);
                }

                return false;
            }

            if (typeof settings.onValidationPass === 'function') {
                settings.onValidationPass();
            }

            return true;
        });
    }

    /**
     * Bind field blur event for real-time validation
     */
    function bindFieldBlur() {
        $('#' + formId).on('blur', 'input[required], select[required], textarea[required]', function() {
            validateField($(this));
        });

        // Also validate on change for select elements
        $('#' + formId).on('change', 'select[required]', function() {
            validateField($(this));
        });
    }

    /**
     * Validate the entire form
     * @returns {object} - { isValid: boolean, errors: array, firstInvalidField: element }
     */
    function validateForm() {
        var errors = [];
        var firstInvalidField = null;
        var $form = $('#' + formId);

        // Clear previous validation states
        clearValidationStates();

        // Find all required fields
        $form.find('input[required], select[required], textarea[required]').each(function() {
            var $field = $(this);
            var fieldError = validateField($field);

            if (fieldError) {
                errors.push(fieldError);
                if (!firstInvalidField) {
                    firstInvalidField = $field;
                }
            }
        });

        // Run custom validators
        $.each(settings.customValidators, function(selector, validator) {
            $form.find(selector).each(function() {
                var $field = $(this);
                var customError = validator($field);
                if (customError) {
                    errors.push(customError);
                    if (!firstInvalidField) {
                        firstInvalidField = $field;
                    }
                    if (settings.highlightFields) {
                        $field.addClass(settings.errorClass).removeClass(settings.successClass);
                    }
                }
            });
        });

        return {
            isValid: errors.length === 0,
            errors: errors,
            firstInvalidField: firstInvalidField
        };
    }

    /**
     * Validate a single field
     * @param {jQuery} $field - The field to validate
     * @returns {object|null} - Error object or null if valid
     */
    function validateField($field) {
        var value = getFieldValue($field);
        var isEmpty = isFieldEmpty(value);
        var labelText = getFieldLabel($field);
        var fieldName = $field.attr('name') || $field.attr('id') || 'Unknown field';

        // Check if field is required and empty
        if ($field.prop('required') && isEmpty) {
            if (settings.highlightFields) {
                $field.addClass(settings.errorClass).removeClass(settings.successClass);
                // Handle Select2 styling
                applySelect2ErrorStyle($field, true);
            }

            return {
                field: $field,
                fieldName: fieldName,
                label: labelText,
                message: labelText + ' is required'
            };
        }

        // Field is valid
        if (settings.highlightFields) {
            $field.removeClass(settings.errorClass);
            applySelect2ErrorStyle($field, false);
            if (value) {
                $field.addClass(settings.successClass);
            }
        }

        return null;
    }

    /**
     * Apply or remove error styling for Select2 elements
     * @param {jQuery} $field - The select field
     * @param {boolean} hasError - Whether to apply or remove error style
     */
    function applySelect2ErrorStyle($field, hasError) {
        if (!$field.hasClass('select2-hidden-accessible') && !$field.data('select2')) {
            return; // Not a Select2 element
        }

        var $select2Container = $field.next('.select2-container');
        if (!$select2Container.length) return;

        var $selection = $select2Container.find('.select2-selection');
        
        if (hasError) {
            $selection.addClass(settings.errorClass);
        } else {
            $selection.removeClass(settings.errorClass);
        }
    }

    /**
     * Get the value of a field (handles different input types)
     * @param {jQuery} $field - The field
     * @returns {string|array} - Field value
     */
    function getFieldValue($field) {
        var type = $field.attr('type');
        var tagName = $field.prop('tagName').toLowerCase();

        if (tagName === 'select') {
            if ($field.prop('multiple')) {
                return $field.val() || [];
            }
            return $field.val();
        }

        if (type === 'checkbox') {
            return $field.is(':checked') ? $field.val() : '';
        }

        if (type === 'radio') {
            var name = $field.attr('name');
            return $('input[name="' + name + '"]:checked').val() || '';
        }

        if (type === 'file') {
            return $field[0].files.length > 0 ? $field.val() : '';
        }

        return $.trim($field.val());
    }

    /**
     * Check if a field value is empty
     * @param {string|array} value - The value to check
     * @returns {boolean} - True if empty
     */
    function isFieldEmpty(value) {
        if (value === null || value === undefined) {
            return true;
        }

        if (Array.isArray(value)) {
            return value.length === 0;
        }

        return $.trim(value) === '';
    }

    /**
     * Get the label text for a field
     * @param {jQuery} $field - The field
     * @returns {string} - Label text
     */
    function getFieldLabel($field) {
        var labelText = '';
        var fieldId = $field.attr('id');
        var fieldName = $field.attr('name');

        // Method 1: Find label with 'for' attribute
        if (fieldId) {
            var $label = $('label[for="' + fieldId + '"]');
            if ($label.length) {
                labelText = cleanLabelText($label.text());
            }
        }

        // Method 2: Find label as parent or sibling
        if (!labelText) {
            var $parentLabel = $field.closest('label');
            if ($parentLabel.length) {
                labelText = cleanLabelText($parentLabel.clone().children().remove().end().text());
            }
        }

        // Method 3: Find label as previous sibling
        if (!labelText) {
            var $prevLabel = $field.closest('.form-group, .col, .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, [class*="col-"]').find('label').first();
            if ($prevLabel.length) {
                labelText = cleanLabelText($prevLabel.text());
            }
        }

        // Method 4: Find label in same container
        if (!labelText) {
            var $container = $field.parent();
            var $containerLabel = $container.find('label').first();
            if (!$containerLabel.length) {
                $containerLabel = $container.prev('label');
            }
            if ($containerLabel.length) {
                labelText = cleanLabelText($containerLabel.text());
            }
        }

        // Method 5: Use placeholder
        if (!labelText) {
            labelText = $field.attr('placeholder') || '';
        }

        // Method 6: Use field name as fallback
        if (!labelText) {
            labelText = formatFieldName(fieldName || fieldId || 'This field');
        }

        return labelText;
    }

    /**
     * Clean label text by removing asterisks and extra whitespace
     * @param {string} text - Raw label text
     * @returns {string} - Cleaned text
     */
    function cleanLabelText(text) {
        return $.trim(text)
            .replace(/\*/g, '')           // Remove asterisks
            .replace(/:/g, '')            // Remove colons
            .replace(/\s+/g, ' ')         // Normalize whitespace
            .replace(/^\s+|\s+$/g, '');   // Trim
    }

    /**
     * Format field name to readable text
     * @param {string} name - Field name
     * @returns {string} - Formatted name
     */
    function formatFieldName(name) {
        if (!name) return 'This field';

        return name
            .replace(/\[\]/g, '')                    // Remove array brackets
            .replace(/[_\-]/g, ' ')                  // Replace underscores and hyphens
            .replace(/([a-z])([A-Z])/g, '$1 $2')     // Add space before capitals
            .replace(/\b\w/g, function(l) {          // Capitalize first letter of each word
                return l.toUpperCase();
            });
    }

    /**
     * Show validation errors using toastr
     * @param {array} errors - Array of error objects
     */
    function showValidationErrors(errors) {
        if (typeof toastr === 'undefined') {
            console.error('Toastr is not loaded. Please include toastr library.');
            alert('Please fill in all required fields:\n\n' + errors.map(function(e) {
                return '• ' + e.message;
            }).join('\n'));
            return;
        }

        // Clear existing toasts
        toastr.clear();

        if (settings.showAllErrors) {
            // Show all errors in a single toastr
            var errorMessages = errors.map(function(e) {
                return '<li>' + escapeHtml(e.label) + '</li>';
            }).join('');

            var errorCount = errors.length;
            var title = errorCount === 1 
                ? 'Required Field Missing' 
                : errorCount + ' Required Fields Missing';

            toastr.error(
                '<ul style="margin: 0; padding-left: 20px;">' + errorMessages + '</ul>',
                title,
                { timeOut: settings.toastrTimeout + (errors.length * 500) }
            );
        } else {
            // Show errors one by one
            errors.forEach(function(error, index) {
                setTimeout(function() {
                    toastr.error(error.message, 'Validation Error');
                }, index * 300);
            });
        }
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} - Escaped text
     */
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    /**
     * Find the tab pane containing the element
     * @param {jQuery} $element - The element to find tab for
     * @returns {object|null} - { tabPane: jQuery, tabButton: jQuery } or null
     */
    function findContainingTab($element) {
        if (!$element || !$element.length) return null;

        // Find the closest tab-pane ancestor
        var $tabPane = $element.closest('.tab-pane');
        
        if (!$tabPane.length) {
            return null; // Element is not inside a tab
        }

        var tabPaneId = $tabPane.attr('id');
        if (!tabPaneId) return null;

       

        // Find the tab button that controls this pane
        // Support Bootstrap 4/5 tab structures
        var $tabButton = $('[data-bs-target="#' + tabPaneId + '"], [data-target="#' + tabPaneId + '"], [href="#' + tabPaneId + '"], button[aria-controls="' + tabPaneId + '"]').first();

        if (!$tabButton.length) {
            // Try finding by id match in nav-tabs
            $tabButton = $('.nav-tabs [id="' + tabPaneId + '-tab"], .nav-tabs button[id="' + tabPaneId + '-tab"]').first();
        }

        if ($tabButton.length) {
            return {
                tabPane: $tabPane,
                tabButton: $tabButton
            };
        }

        return null;
    }

    /**
     * Activate tab and focus on the element
     * @param {jQuery} $element - Element to focus on
     */
    function activateTabAndFocus($element) {
        if (!$element || !$element.length) return;

        var tabInfo = findContainingTab($element);

        if (tabInfo && settings.activateTab) {
            // Element is inside a tab - need to activate it first
            var $tabButton = tabInfo.tabButton;
            var $tabPane = tabInfo.tabPane;

            // Check if tab is already active
            var isTabActive = $tabPane.hasClass('show') && $tabPane.hasClass('active');

            if (!isTabActive) {
                // Activate the tab using Bootstrap's Tab API or manual click
                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    // Bootstrap 5
                    var tabInstance = bootstrap.Tab.getOrCreateInstance($tabButton[0]);
                    tabInstance.show();
                } else if ($.fn.tab) {
                    // Bootstrap 4 jQuery plugin
                    $tabButton.tab('show');
                } else {
                    // Fallback: trigger click
                    $tabButton.trigger('click');
                }

                // Wait for tab animation to complete before scrolling and focusing
                setTimeout(function() {
                    scrollToElement($element);
                }, settings.tabAnimationDelay);
            } else {
                // Tab already active, just scroll and focus
                scrollToElement($element);
            }
        } else {
            // Element is not in a tab, or tab activation disabled
            scrollToElement($element);
        }
    }

    /**
     * Scroll to an element and focus it
     * @param {jQuery} $element - Element to scroll to
     */
    function scrollToElement($element) {
        if (!$element || !$element.length) return;

        // Make sure element is visible (not hidden by collapsed parent, modal, etc.)
        var $hiddenParents = $element.parents(':hidden');
        if ($hiddenParents.length) {
            // Try to show hidden parents (for collapse elements)
            $hiddenParents.each(function() {
                var $hidden = $(this);
                if ($hidden.hasClass('collapse')) {
                    $hidden.addClass('show');
                }
            });
        }

        var offset = $element.offset();
        if (offset) {
            $('html, body').animate({
                scrollTop: offset.top - 120
            }, 200, function() {
                focusElement($element);
            });
        } else {
            // If offset not available, just try to focus
            focusElement($element);
        }
    }

    /**
     * Focus an element (handles Select2 and regular inputs)
     * @param {jQuery} $element - Element to focus
     */
    function focusElement($element) {
        if (!$element || !$element.length) return;

        // Check if it's a Select2 element
        if ($element.hasClass('select2-hidden-accessible') || $element.data('select2')) {
            // Open Select2 dropdown
            try {
                $element.select2('open');
                // Close after brief moment to just highlight it
                setTimeout(function() {
                    $element.select2('close');
                    $element.select2('focus');
                }, 100);
            } catch (e) {
                // Fallback if Select2 not initialized
                $element.focus();
            }
        } else {
            // Regular input focus
            $element.focus();
        }

        // Add a brief highlight animation
        $element.addClass('validation-highlight');
        setTimeout(function() {
            $element.removeClass('validation-highlight');
        }, 1500);
    }

    /**
     * Clear all validation states
     */
    function clearValidationStates() {
        var $form = $('#' + formId);
        
        $form.find('.' + settings.errorClass)
            .removeClass(settings.errorClass);

        $form.find('.' + settings.successClass)
            .removeClass(settings.successClass);

        // Clear Select2 error states
        $form.find('.select2-container .select2-selection.' + settings.errorClass)
            .removeClass(settings.errorClass);

        // Remove highlight class
        $form.find('.validation-highlight')
            .removeClass('validation-highlight');
    }

    /**
     * Manually trigger validation
     * @returns {boolean} - True if form is valid
     */
    function validate() {
        var result = validateForm();

        if (!result.isValid) {
            showValidationErrors(result.errors);

            if (settings.scrollToFirst && result.firstInvalidField) {
                activateTabAndFocus(result.firstInvalidField);
            }
        }

        return result.isValid;
    }

    /**
     * Add a custom validator
     * @param {string} selector - jQuery selector for fields
     * @param {function} validator - Validation function that returns error object or null
     */
    function addValidator(selector, validator) {
        settings.customValidators[selector] = validator;
    }

    /**
     * Reset the form validation
     */
    function reset() {
        clearValidationStates();
        if (typeof toastr !== 'undefined') {
            toastr.clear();
        }
    }

    /**
     * Destroy the validator and remove event handlers
     */
    function destroy() {
        $('#' + formId).off('submit');
        $('#' + formId).off('blur', 'input[required], select[required], textarea[required]');
        $('#' + formId).off('change', 'select[required]');
        clearValidationStates();
    }

    // Public API
    return {
        init: init,
        validate: validate,
        addValidator: addValidator,
        reset: reset,
        destroy: destroy,
        getSettings: function() { return settings; }
    };

})(jQuery);
