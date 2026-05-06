let formControls = null;

$(document).ready(function() {
    formControls = document.querySelectorAll('.form-group .form-control');

    formControls.forEach(control => {
        control.addEventListener('input', function() {
            const value = this.value;
            $(this).toggleClass('active', value && value !== "");
        });
    });

    new CustomMultiSelect(document.querySelector('#inputCompany'), 'Select', console.log);
});
