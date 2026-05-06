'use strict';



// for date picker icon click
$('#apply_date_icon').on('click', function() {
    $('#apply_date').focus();
});
// for datepicker icon click
$('#to-date-icon').on('click', function() {
    $('#leave_to').focus();
});
// for datepicker icon click
$('#homework_date_icon').on('click', function() {
    $('#homework_date').focus();
});
// for datepicker icon click
$('#submission_date_icon').on('click', function() {
    $('#submission_date').focus();
});
$('#notice_date_icon').on('click', function() {
    $('#notice_date').focus();
});
$('#publish_on_icon').on('click', function() {
    $('#publish_on').focus();
});
$('#event_start_date').on('click', function() {
    $('#event_from_date').focus();
});
$('#event_end_date').on('click', function() {
    $('#event_to_date').focus();
});
$('#book_return_date_icon').on('click', function() {
    $('#due_date').focus();
});
$('#receive_date_icon').on('click', function() {
    $('#receive_date').focus();
});
// for upload attach file when apply leave
var fileInput = document.getElementById('attach_file');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderAttachFile').placeholder = fileName;
    }
}
// for global modal 
// $('body').on('click', '.nom_epi', function() { alert("hello"); })
$(document).ready(function() {
    $('body').on("click", ".modalLink", function(e) {

        e.preventDefault();
        $('.modal-backdrop').show();
        $("#showDetaildModal").show();
        $("div.modal-dialog").removeClass('modal-md');
        $("div.modal-dialog").removeClass('modal-lg');
        $("div.modal-dialog").removeClass('modal-bg');
        var modal_size = $(this).attr('data-modal-size');
        if (modal_size !== '' && typeof modal_size !== typeof undefined && modal_size !== false) {
            $("#modalSize").addClass(modal_size);
        } else {
            $("#modalSize").addClass('modal-md');
        }
        var title = $(this).attr('title');
        $("#showDetaildModalTile").text(title);
        var data_title = $(this).attr('data-original-title');
        $("#showDetaildModalTile").text(data_title);
        $("#showDetaildModal").modal('show');
        $('div.ajaxLoader').show();
        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            success: function(data) {
                $("#showDetaildModalBody").html(data);
                $("#showDetaildModal").modal('show');
            }
        });
    });
});
// for global Delete
$(document).ready(function() {
    $('body').on("click", ".deleteUrl", function(e) {

        e.preventDefault();
        $('.modal-backdrop').show();
        $("#showDetaildModal").show();
        $("div.modal-dialog").removeClass('modal-md');
        $("div.modal-dialog").removeClass('modal-lg');
        $("div.modal-dialog").removeClass('modal-bg');
        var modal_size = $(this).attr('data-modal-size');
        if (modal_size !== '' && typeof modal_size !== typeof undefined && modal_size !== false) {
            $("#modalSize").addClass(modal_size);
        } else {
            $("#modalSize").addClass('modal-md');
        }
        var title = $(this).attr('title');
        $("#showDetaildModalTile").text(title);
        var data_title = $(this).attr('data-original-title');
        $("#showDetaildModalTile").text(data_title);
        $("#showDetaildModal").modal('show');
        $('div.ajaxLoader').show();
        $.ajax({
            type: "GET",
            url: $(this).attr('href'),
            success: function(data) {
                $("#showDetaildModalBody").html(data);
                $("#showDetaildModal").modal('show');
            }
        });
    });
});
// select staff name from selecting role name
$(document).ready(function() {
    $("#staffNameByRole").on('change', function() {
        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };
        // get section for student
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'staffNameByRole',
            success: function(data) {
                console.log(data);
                var a = '';
                $.each(data, function(i, item) {
                    if (item.length) {
                        $('#selectStaffs').find('option').not(':first').remove();
                        $('#selectStaffsDiv ul').find('li').not(':first').remove();
                        $.each(item, function(i, staffs) {
                            $('#selectStaffs').append($('<option>', {
                                value: staffs.id,
                                text: staffs.full_name
                            }));
                            $("#selectStaffsDiv ul").append("<li data-value='" + staffs.id + "' class='option'>" + staffs.full_name + "</li>");
                        });
                    } else {
                        $('#selectStaffsDiv .current').html('SELECT *');
                        $('#selectStaffs').find('option').not(':first').remove();
                        $('#selectStaffsDiv ul').find('li').not(':first').remove();
                    }
                });
                console.log(a);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });
});
// select item name from selecting item category name
$(document).ready(function() {
    $("#item_category_id").on('change', function() {
        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };
        console.log("test");

        console.log(formData);
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'getItemByCategory',
            success: function(data) {
                console.log(data);
                var a = '';
                $.each(data, function(i, item) {
                    if (item.length) {
                        $('#selectItems').find('option').not(':first').remove();
                        $('#selectItemsDiv ul').find('li').not(':first').remove();
                        $.each(item, function(i, items) {
                            $('#selectItems').append($('<option>', {
                                value: items.id,
                                text: items.item_name
                            }));
                            $("#selectItemsDiv ul").append("<li data-value='" + items.id + "' class='option'>" + items.item_name + "</li>");
                        });
                    } else {
                        $('#selectItemsDiv .current').html('SELECT *');
                        $('#selectItems').find('option').not(':first').remove();
                        $('#selectItemsDiv ul').find('li').not(':first').remove();
                    }
                });
                console.log(a);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });
});
// for add staff earnings in payroll
function addMoreEarnings() {
    var table = document.getElementById("tableID");
    var table_len = (table.rows.length);
    var id = parseInt(table_len);
    var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'><td width='70%' class='pr-30'><div class='input-effect mt-10'><input class='primary-input form-control' type='text' id='earningsType" + id + "' name='earningsType[]'><label for='earningsType" + id + "'>Type</label><span class='focus-border'></span></div></td><td width='20%' class='pr-30'><div class='input-effect mt-10'><input class='primary-input form-control' type='number' id='earningsValue" + id + "' name='earningsValue[]'><label for='earningsValue" + id + "'>Value</label><span class='focus-border'></span></div></td><td width='10%' class='pt-30'><button class='primary-btn icon-only fix-gr-bg close-deductions' onclick='delete_earings(" + id + ")'><span class='ti-close'></span></button></td></tr>";
}

function delete_earings(id) {
    var table = document.getElementById("tableID");
    var rowCount = table.rows.length;
    $("#row" + id).html("");
}
// for minus staff deductions in payroll
function addDeductions() {
    var table = document.getElementById("tableDeduction");
    var table_len = (table.rows.length);
    var id = parseInt(table_len);
    var row = table.insertRow(table_len).outerHTML = "<tr id='DeductionRow" + id + "'><td width='70%' class='pr-30'><div class='input-effect mt-10'><input class='primary-input form-control' type='text' id='deductionstype" + id + "' name='deductionstype[]'><label for='deductionstype" + id + "'>Type</label><span class='focus-border'></span></div></td><td width='20%' class='pr-30'><div class='input-effect mt-10'><input class='primary-input form-control' type='number' id='deductionsValue" + id + "' name='deductionsValue[]'><label for='deductionsValue" + id + "'>Value</label><span class='focus-border'></span></div></td><td width='10%' class='pt-30'><button class='primary-btn icon-only fix-gr-bg close-deductions' onclick='delete_deduction(" + id + ")'><span class='ti-close'></span></button></td></tr>";
}

function delete_deduction(id) {
    var tables = document.getElementById("tableDeduction");
    var rowCount = tables.rows.length;
    $("#DeductionRow" + id).html("");
}



// payroll calculate for staff
function calculateSalary() {
    var basicSalary = $("#basicSalary").val();
    if (basicSalary == 0) {
        alert('Please Add Employees Basic Salary from Staff Update Form First');
    } else {
        var earningsType = document.getElementsByName('earningsValue[]');
        var earningsValue = document.getElementsByName('earningsValue[]');
        var tax = $("#tax").val();
        var total_earnings = 0;
        var total_deduction = 0;
        var deductionstype = document.getElementsByName('deductionstype[]');
        var deductionsValue = document.getElementsByName('deductionsValue[]');
        for (var i = 0; i < earningsValue.length; i++) {
            var inp = earningsValue[i];
            if (inp.value == '') {
                var inpvalue = 0;
            } else {
                var inpvalue = inp.value;
            }
            total_earnings += parseInt(inpvalue);
        }
        for (var j = 0; j < deductionsValue.length; j++) {
            var inpd = deductionsValue[j];
            if (inpd.value == '') {
                var inpdvalue = 0;
            } else {
                var inpdvalue = inpd.value;
            }
            total_deduction += parseInt(inpdvalue);
        }
        var gross_salary = parseInt(basicSalary) + parseInt(total_earnings) - parseInt(total_deduction);
        var net_salary = parseInt(basicSalary) + parseInt(total_earnings) - parseInt(total_deduction) - parseInt(tax);

        $("#total_earnings").val(total_earnings);
        $("#total_deduction").val(total_deduction);
        $("#gross_salary").val(gross_salary);
        $("#final_gross_salary").val(gross_salary);
        $("#net_salary").val(net_salary);
    }




    if ($('#total_earnings').val() != '') {
        $('#total_earnings').focus();
    }

    if ($('#total_deduction').val() != '') {
        $('#total_deduction').focus();
    }

    if ($('#net_salary').val() != '') {
        $('#net_salary').focus();
    }
}

function validateForm() {




    var x = $("#payment_mode").val();
    if (x === "") {
        $('.modal_input_validation').show();
        $(".modal_input_validation").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation").addClass("red_alert");
        return false;
    }




    if (x == 3) {
        if ($("form#payroll-payslip #accounts").val() == "") {
            alert('Please select an account');
            return false;
        }


        if (parseInt($("form#payroll-payslip #balance").val()) < parseInt($("form#payroll-payslip #payment_balance").val())) {
            alert('The Account has no available balance');
            return false;
        }

    }








    return true;
    preventDefault();
}

function validateToDoForm() {
    var todo_title = $("#todo_title").val();
    if (todo_title === "") {
        $('.modal_input_validation').show();
        $(".modal_input_validation").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation").addClass("red_alert");
        return false;
    }
    return true;
    preventDefault();
}

$("select.niceSelect").on('change', function() {
    $('.modal_input_validation').hide();
});
// student subject drop down info by section change
$(document).ready(function() {
    $("#sectionSelectStudent").on('change', function() {
        var url = $('#url').val();
        var formData = {
            id: $(this).val(),
            class: $('#classSelectStudent').val()
        };
        //console.log(formData);
        // get subjects dropdown
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'ajaxSubjectDropdown',
            success: function(data) {
                console.log(data);
                var a = '';
                $.each(data, function(i, item) {
                    if (item.length) {
                        $('#subjectSelect').find('option').not(':first').remove();
                        $('#subjectSelecttDiv ul').find('li').not(':first').remove();
                        $.each(item, function(i, subjectsName) {
                            $('#subjectSelect').append($('<option>', {
                                value: subjectsName.id,
                                text: subjectsName.subject_name
                            }));
                            $("#subjectSelecttDiv ul").append("<li data-value='" + subjectsName.id + "' class='option'>" + subjectsName.subject_name + "</li>");
                        });
                    } else {
                        $('#subjectSelecttDiv .current').html('Subject *');
                        $('#subjectSelect').find('option').not(':first').remove();
                        $('#subjectSelecttDiv ul').find('li').not(':first').remove();
                    }
                });
                console.log(a);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });
});
// for upload attach file when add Homework
var fileInput = document.getElementById('homework_file');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderHomeworkName').placeholder = fileName;
    }
}
// for upload content when change in role in available for
$(document).ready(function() {
    $('body').on("change", "#available_for", function(e) {
        e.preventDefault();
        role_id = $(this).val();
        if (role_id == '2') {
            $(".forStudentWrapper").slideDown();
        } else {
            //$('.forStudentWrapper').hide();
            $(".forStudentWrapper").slideUp();
        }
    });
});
// for staff photo  in Staff Add Module
var fileInput = document.getElementById('staff_photo');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderStaffsName').placeholder = fileName;
    }
}

// for Company Logo in Company Add Module
var fileInput = document.getElementById('company_logo');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderCompanyLogo').placeholder = fileName;
    }
}
// for PO Attach File
var fileInput = document.getElementById('po_attach_file');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderPOAttachFile').placeholder = fileName;
    }
}


// for Digital Stamp in Company Add Module
var fileInput = document.getElementById('digital_stamp');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderDigitalStamp').placeholder = fileName;
    }
}

// for upload content in teacher module
var fileInput = document.getElementById('upload_content_file');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderUploadContent').placeholder = fileName;
    }
}
// for upload Event File  in communication module
var fileInput = document.getElementById('upload_event_image');
if (fileInput) {
    //alert("staffs photo");
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        document.getElementById('placeholderEventFile').placeholder = fileName;
    }
}
// for upload Holiday File  in communication module
var fileInput = document.getElementById('upload_holiday_image');
if (fileInput) {
    fileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var fileInput = event.srcElement;
        var fileName = fileInput.files[0].name;
        console.log(fileName);
        document.getElementById('placeholderHolidayFile').placeholder = fileName;
    }
}


// Btn Add To Tab
$('#btn_addtotab').on('click', function() {
    //function getTotalInSell(row = null) {
    var tab_name = $('#addtotab_pagename').val();
    var page_url = $('#addtotab_url').val();
    $.ajax({
        type: "POST",
        data: {
            tab_name: tab_name,
            page_url: page_url
        },

        //url: 'http://syscom.company/venus-erp/page-tabs-add',
        url: 'http://localhost:81/venus-erp/page-tabs-add',
        success: function(data) {
            var data = JSON.parse(data);
            if (data['data'] == "SUCCESS") {
                //window.location.href = "http://syscom.company/venus-erp/admin-dashboard";
                window.location.href = "http://localhost:81/venus-erp/admin-dashboard";
            } else {}
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });

});

function closetotab(id = null) {

    $.ajax({
        type: "POST",
        data: {
            id: id,
        },
        //url: 'http://syscom.company/venus-erp/page-tabs-del',
        url: 'http://localhost:81/venus-erp/page-tabs-del',
        success: function(data) {
            var data = JSON.parse(data);
            if (data['data'] == "SUCCESS") {
                //window.location.href = "http://syscom.company/venus-erp/admin-dashboard";
                window.location.href = "http://localhost:81/venus-erp/admin-dashboard";
            } else {

            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });

}


// for add member  in Library module
$(document).ready(function() {
    $('body').on("change", "#member_type", function(e) {
        e.preventDefault();
        role_id = $(this).val();

        $(".forStudentWrapper").slideUp();
        $("#selectStaffsDiv").slideDown();

        $('#select_student').find('option').not(':first').remove();
        $('#select_student_div ul').find('li').not(':first').remove();


        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };

        console.log(formData);
        // get section for student
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'staffNameByRole',
            success: function(data) {
                console.log(data);
                var a = '';
                $.each(data, function(i, item) {
                    if (item.length) {
                        $('#selectStaffs').find('option').not(':first').remove();
                        $('#selectStaffsDiv ul').find('li').not(':first').remove();
                        $.each(item, function(i, staffs) {

                            $('#selectStaffs').append($('<option>', {
                                value: staffs.user_id,
                                text: staffs.full_name
                            }));
                            $("#selectStaffsDiv ul").append("<li data-value='" + staffs.user_id + "' class='option'>" + staffs.full_name + "</li>");

                        });
                    } else {
                        $('#selectStaffsDiv .current').html('SELECT *');
                        $('#selectStaffs').find('option').not(':first').remove();
                        $('#selectStaffsDiv ul').find('li').not(':first').remove();
                    }
                });
                console.log(a);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });
});

$(document).ready(function() {
    $('body').on("click", "#addRowBtn", function(e) {

        var tableLength = $("#productTable tbody tr").length;
        var url = $('#url').val();




        $.ajax({
            type: "GET",
            dataType: 'json',
            url: url + '/' + 'get-receive-item',
            success: function(response) {

                console.log('sdcds');



                var tr = '<tr class="mt-40">' + '<td class="border-top-0"><div class="input-effect">';


                tr += '<select class="niceSelect form-control" name="products[]" id="productName" style="display:none">' + '<option value="">Select product</option>';

                $.each(response, function(index, value) {
                    tr += '<option value="' + value.id + '">' + value.item_name + '</option>';
                });

                tr += '</select>';

                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";
                tr += "<span class='current'>Select product</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select product' class='option selected'>Select product</li>";


                $.each(response, function(key, value) {
                    tr += "<li data-value=" + value.id + " class='option'>" + value.item_name + "</li>";
                });

                tr += "</ul>";
                tr += '</div>';


                tr += "</td>";

                tr += "<td class='border-top-0'>";
                tr += "<div class='input-effect'>";
                tr += "<input class='primary-input form-control' type='text' step='any' id='part_number' name='part_number[]' autocomplete='off'>";
                tr += "<span class='focus-border'></span>";
                tr += "</div>";
                tr += "</td>";
                tr += "<td class='border-top-0'>";
                tr += "<div class='input-effect'>";
                tr += "<input class='primary-input form-control' type='text' step='any' id='new_part_number' name='new_part_number[]' autocomplete='off'>";
                tr += "<span class='focus-border'></span>";
                tr += "</div>";
                tr += "</td>";


                tr += "<td class=\"border-top-0\">";

                tr += "<div class=\"input-effect\">";
                tr += "<select class=\"niceSelect w-100 bb form-control\" name=\"denomination[]\" id=\"denomination\" style=\"display:none\">";
                tr += "<option data-display=\"Select denomination\" value=\"\">Select denomination</option>";

                var lists = ['Meter (m)', 'Centimeter (cm)', 'Millimeter (mm)', 'Foot (ft)', 'Inch (in)', 'Yard (yd)', 'Kilogram (kg)', 'Gram(gm)', 'Milligram (mg)', 'Pound (lb)', 'Ounce (oz)', 'Liter (l)', 'Milliliter (ml)', 'Gallon (gal)', 'Piece (pcs)', 'Unit (u)'];
                var shortForm = ['Meter (m)', 'Centimeter (cm)', 'Millimeter (mm)', 'Foot (ft)', 'Inch (in)', 'Yard (yd)', 'Kilogram (kg)', 'Gram(gm)', 'Milligram (mg)', 'Pound (lb)', 'Ounce (oz)', 'Liter (l)', 'Milliliter (ml)', 'Gallon (gal)', 'Piece (pcs)', 'Unit (u)'];
                var countList = 0;
                for (i = 0; i < lists.length; i++) {
                    tr += "<option value=\"" + shortForm[i] + "\">" + lists[i] + "</option>";
                }


                tr += "</select>";


                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";
                tr += "<span class='current'>Select denomination</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select denomination' class='option selected'>Select denomination</li>";

                var countList = 0;
                for (i = 0; i < lists.length; i++) {
                    tr += "<li data-value=\"" + shortForm[i] + "\" class='option'>" + lists[i] + "</li>";
                }




                tr += "</ul>";




                tr += "</div>";
                tr += "</td>";


                tr += "<td class=\"border-top-0\">";
                tr += "<div class=\"input-effect\">";
                tr += "<input class=\"primary-input form-control\" type=\"number\" id=\"quantity\" name=\"quantity[]\" autocomplete=\"off\">";
                tr += "<span class=\"focus-border\"></span>";
                tr += "</div>";
                tr += "</td>";

                tr += "<td class=\"border-top-0\">";
                tr += "<div class=\"input-effect\">";
                tr += "<input class=\"primary-input form-control \" type=\"number\" id=\"unit_price\" name=\"unit_price[]\" autocomplete=\"off\"  placeholder=\"0.00\">";
                tr += "<span class=\"focus-border\"></span>";
                tr += "</div>";
                tr += "</td>";

                tr += "<td class=\"border-top-0\">";
                tr += "<div class=\"input-effect\">";
                tr += "<input class=\"primary-input form-control \" type=\"number\" id=\"total_price\" name=\"total_price[]\" readonly=\"\" autocomplete=\"off\" placeholder=\"0.00\">";
                tr += "<span class=\"focus-border\"></span>";
                tr += "</div>";
                tr += "</td>";

                tr += "<td class=\"border-top-0\">";
                tr += "<div class=\"input-effect\">";
                tr += "<input class=\"primary-input form-control \" type=\"number\" id=\"sale_price\" name=\"sale_price[]\" placeholder=\"0.00\" autocomplete=\"off\">";
                tr += "<span class=\"focus-border\"></span>";
                tr += "</div>";
                tr += "</td>";

                tr += "<td style=\"border:0px\" >";
                tr += "<button class=\"primary-btn icon-only fix-gr-bg\" type=\"button\" id=\"delete-receive-item\">";
                tr += "<span class=\"ti-trash\"></span>";
                tr += "</button>";

                tr += "</td>";
                tr += "</tr>";


                $("#productTable tbody tr:last").after(tr);


            },
            error: function(response) {
                console.log('Error:', response);
            }

        }); // get the product data
    });
});



// add new row when sell a product in Item Sell List
function addRowInSell() {
    $("#addRowBtn").button("loading");
    var tableLength = $("#productTable tbody tr").length;
    var url = $('#url').val();
    var tableRow;
    var arrayNumber;
    var count;
    if (tableLength > 0) {
        tableRow = $("#productTable tbody tr:last").attr('id');
        arrayNumber = $("#productTable tbody tr:last").attr('class');
        count = tableRow.substring(3);
        count = Number(count) + 1;
        arrayNumber = Number(arrayNumber) + 1;
    } else {
        // no table row
        count = 1;
        arrayNumber = 0;
    }
    $.ajax({
        url: url + '/' + 'get-receive-item',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
                console.log(response);
                $("#addRowBtn").button("reset");
                var tr = '<tr id="row' + count + '" class="' + arrayNumber + '">' + '<td class="border-top-0"><div class="input-effect">';



                tr += '<select class="niceSelect form-control" name="item_id[]" id="productName' + count + '"  style="display:none">' + '<option value="">Select Item</option>';

                $.each(response, function(index, value) {
                    tr += '<option value="' + value.id + '">' + value.item_name + '</option>';
                });
                tr += '</select>';


                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";
                tr += "<span class='current'>Select Item</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select Item' class='option selected'>Select Item</li>";


                $.each(response, function(key, value) {
                    tr += "<li data-value=" + value.id + " class='option'>" + value.item_name + "</li>";
                });
                tr += "</ul>";



                tr += '</div></td>' + '<td class="border-top-0" width=""><div class="input-effect">' + '<input type="text" name="unit_price[]" onkeyup="getTotalByPrice(' + count + ')" id="unit_price' + count + '"  autocomplete="off" class="primary-input form-control"  min="1" />' + '<span class="focus-border"></span>' + '</div></td>' + '<td class="border-top-0"><div class="input-effect">' + '<input type="text" name="quantity[]" onkeyup="getTotalInSell(' + count + ')" id="quantity' + count + '" autocomplete="off"  class="form-control primary-input" />' + '<input type="hidden" name="costValue[]" id="costValue' + count + '" autocomplete="off" class="form-control primary-input" />' + '<span class="focus-border"></span>' + '</div></td>' + '<td class="border-top-0"><div class="input-effect">' + '<input type="text" name="total[]" id="total' + count + '" autocomplete="off" class="form-control primary-input" value= "0.00" />' + '<input type="hidden" name="totalValue[]" id="totalValue' + count + '" autocomplete="off" class="form-control primary-input" />' + '<span class="focus-border"></span>' + '</div></td>' + '<td class="border-top-0"><button type="button" class="removeProductRowBtn primary-btn icon-only fix-gr-bg" onclick="removeProductRow(' + count + ')"><span class="ti-trash"></span></button></td>' + '</td>' + '</tr>';
                if (tableLength > 0) {
                    $("#productTable tbody tr:last").after(tr);
                } else {
                    $("#productTable tbody").append(tr);
                }
                $('.common-select').addClass("new_select_css");
            } // /success
    }); // get the product data
}
//for table row Total by Unit Price
function getTotalByPrice(row = null) {

    if (row) {
        product_id = $("#productName" + row).val();

        if (product_id > 0) {
            var total = Number($("#unit_price" + row).val()) * Number($("#quantity" + row).val());
            total = total.toFixed(2);


            $("#total" + row).val(total);
            $("#totalValue" + row).val(total);


            subAmount();
        } else {
            alert("please select a product first");
            $("#unit_price" + row).val('');
            $("#productName" + row).focus();
        }
    } else {
        alert('no row !! please refresh the page');
    }
}


//for table row Total by Quantity
function getTotal(row = null) {
    if (row) {
        product_id = $("#productName" + row).val();
        var url = $("#url").val();
        if (product_id > 0) {
            var total = Number($("#unit_price" + row).val()) * Number($("#quantity" + row).val());
            total = total.toFixed(2);
            $("#total" + row).val(total);
            $("#totalValue" + row).val(total);
            subAmount();
        } else {
            alert("please select a product first");
            $("#quantity" + row).val('');
            $("#productName" + row).focus();
        }
    } else {
        alert('no row !! please refresh the page');
    }
}
// get quantity by product ID and get the sum of Sub Total
function getTotalInSell(row = null) {
    if (row) {
        product_id = $("#productName" + row).val();
        quantity = $("#quantity" + row).val();
        var url = $("#url").val();
        if (product_id > 0) {
            $.ajax({
                type: "POST",
                data: {
                    product_id: product_id
                },
                url: url + '/' + 'check-product-quantity',
                success: function(data) {
                    if (Number(quantity) > Number(data)) {
                        alert("Your Given Quantity is not bigger than Stock Quantity.");
                        $("#quantity" + row).val('');
                    } else {
                        var total = Number($("#unit_price" + row).val()) * Number($("#quantity" + row).val());
                        total = total.toFixed(2);
                        $("#total" + row).val(total);
                        $("#totalValue" + row).val(total);
                        subAmount();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {}
            });
        } else {
            alert("please select a product first");
            $("#quantity" + row).val('');
            $("#productName" + row).focus();
        }
    } else {
        alert('no row !! please refresh the page');
    }
}

function subAmount() {
    var tableProductLength = $("#productTable tbody tr").length;
    var totalSubAmount = 0;
    var totalSubQuantity = 0;
    var totalPaid = $('#totalPaid').val();
    for (x = 0; x < tableProductLength; x++) {
        var tr = $("#productTable tbody tr")[x];
        var count = $(tr).attr('id');
        count = count.substring(3);
        totalSubAmount = Number(totalSubAmount) + Number($("#total" + count).val());
        totalSubQuantity = Number(totalSubQuantity) + Number($("#quantity" + count).val());
    } // /for
    totalSubAmount = totalSubAmount.toFixed(2);
    // sub total
    $("#subTotal").val(totalSubAmount);
    $("#subTotalValue").val(totalSubAmount);
    // sub total Quantity
    $("#subTotalQuantity").val(totalSubQuantity);
    $("#subTotalQuantityValue").val(totalSubQuantity);
    // Due Amount

    var dueAmount = totalSubAmount - totalPaid;


    if ($('input[name="full_paid"]').is(':checked')) {
        $('#totalPaid').val(totalSubAmount);
        $("#totalDue").val();
        $("#totalDueValue").val();
    } else {
        $("#totalDue").val(dueAmount);
        $("#totalDueValue").val(dueAmount);
    }








}
// if paid Amount set then Calculate the Due
function paidAmount() {
    var subTotal = $("#subTotal").val();
    if (subTotal) {
        var dueAmount = Number($("#subTotal").val()) - Number($("#totalPaid").val());
        dueAmount = dueAmount.toFixed(2);
        $("#totalDue").val(dueAmount);
        $("#totalDueValue").val(dueAmount);
    }
} // /paid amount function

$('input[name="full_paid"]').on('click', function() {
    if ($(this).is(':checked')) {
        var subTotal = $("#subTotal").val();

        $("#totalPaid").val(subTotal);

        $("#totalDue").val(0);

        $("#totalDueValue").val(0);
    } else {
        $("#totalPaid").val(0);
        $("#totalDue").val($("#subTotal").val());
        $("#totalDueValue").val($("#subTotal").val());
    }
});

function removeProductRow(row = null) {
    if (row) {
        $("#row" + row).remove();
        $("#totalPaid").val(0);
        $("#totalPaidValue").val(0);
        subAmount();
    } else {
        alert("Something went Wrong");
    }
}

function deleteReceiveItem(row = null) {
    //var url = $('#url').val();
    if (row) {
        $("#row" + row).remove();
        $("#totalPaid").val('');
        $("#full_paid").prop("checked", false)
        subAmount();
    }
}

function printDiv(divID) {
    //Get the HTML of div
    var divElements = document.getElementById(divID).innerHTML;
    //Get the HTML of whole page
    var oldPage = document.body.innerHTML;
    //Reset the page's HTML with div's HTML only
    document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";

    printButton.style.visibility = 'hidden';
    //Print Page
    window.print();
    printButton.style.visibility = 'visible';
    //Restore orignal HTML
    document.body.innerHTML = oldPage;
}

function checkDue() {
    total_due_value = $("#total_due_value").val();
    total_due = $("#total_due").val();
    if (Number(total_due) > Number(total_due_value)) {
        alert("Payment amount Should not bigger than Due Amount");
        $("#total_due").val('');
        $("#total_due").focus();
    }
}

function delete_receive_payments(id) {
    var r = confirm("Are You Sure To delete This Payment ?");
    if (r == true) {
        event.preventDefault();
        var url = $("#url").val();
        var receive_payment_id = id;
        $.ajax({
            type: "POST",
            data: {
                receive_payment_id: receive_payment_id
            },
            url: url + '/' + 'delete-receive-payment',
            success: function(data) {
                //console.log(data);
                window.location.href = url + "/" + 'item-receive-list';
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("div.successMessage").hide('slide');
                $("div.errorMsg").hide('slide');
                $("div.errorMsg").show('slide');
            }
        });
    } else {
        return false;
    }
}


// delete sell payments
function delete_sell_payments(id) {
    var r = confirm("Are You Sure To delete This Payment ?");
    if (r == true) {
        event.preventDefault();
        var url = $("#url").val();
        var sell_payment_id = id;
        $.ajax({
            type: "POST",
            data: {
                sell_payment_id: sell_payment_id
            },
            url: url + '/' + 'delete-sell-payment',
            success: function(data) {
                //console.log(data);
                window.location.href = url + "/" + 'item-sell-list';
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $("div.successMessage").hide('slide');
                $("div.errorMsg").hide('slide');
                $("div.errorMsg").show('slide');
            }
        });
    } else {
        return false;
    }
}

// sms gateway submit form clickatell
$('form[id="clickatell_form"]').validate({
    rules: {
        clickatell_username: 'required',
        clickatell_password: 'required',
        clickatell_api_id: 'required',
    },
    messages: {
        clickatell_username: 'This field is required',
        clickatell_password: 'This field is required',
        clickatell_api_id: 'This field is required',

    },
    submitHandler: function(form) {
        // form.submit(event);
        //event.preventDefault();
        form_data = $("#clickatell_form").serialize();
        updateClickatellData = $("#clickatell_form_url").val();
        url = $("#url").val();
        $.ajax({
            type: "POST",
            data: form_data,
            url: url + '/' + updateClickatellData,
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('Clickatell Data has been updated successfully', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
});

// sms gateway submit form twilio
$('form[id="twilio_form"]').validate({
    rules: {
        twilio_account_sid: 'required',
        twilio_authentication_token: 'required',
        twilio_registered_no: 'required',
    },
    messages: {
        twilio_account_sid: 'This field is required',
        twilio_authentication_token: 'This field is required',
        twilio_registered_no: 'This field is required',

    },
    submitHandler: function(form) {
        // form.submit(event);
        //event.preventDefault();
        form_data = $("#twilio_form").serialize();
        updateTwilioData = $("#twilio_form_url").val();
        url = $("#url").val();
        var twilio_account_sid = $("#twilio_account_sid").val();
        $(".invalid-feedback").remove();
        if (twilio_account_sid.length < 1) {
            alert(twilio_account_sid);
            $('#twilio_account_sid').after('<span class="invalid-feedback" role="alert"><strong>This field is Required</strong></span>');
        }
        $.ajax({
            type: "POST",
            data: form_data,
            url: url + '/' + updateTwilioData,
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('Twilio Data has been updated successfully', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
});


// sms gateway submit form msg91
$('form[id="msg91_form"]').validate({
    rules: {
        msg91_authentication_key_sid: 'required',
        msg91_sender_id: 'required',
        msg91_route: 'required',
        msg91_country_code: 'required'
    },
    messages: {
        msg91_authentication_key_sid: 'This field is required',
        msg91_sender_id: 'This field is required',
        msg91_route: 'This field is required',
        msg91_country_code: 'This field is required',

    },
    submitHandler: function(form) {
        // form.submit(event);
        //event.preventDefault();
        form_data = $("#msg91_form").serialize();
        updateMsg91Data = $("#msg91_form_url").val();
        url = $("#url").val();
        $.ajax({
            type: "POST",
            data: form_data,
            url: url + '/' + updateMsg91Data,
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('Msg91 Data has been updated successfully', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
});


// select a service
$("#sms_service").change(function(e) {
    e.preventDefault();
    sms_service = $("#sms_service").val();
    url = $("#url").val();
    $.ajax({
        type: "POST",
        data: {
            sms_service: sms_service
        },
        url: url + '/activeSmsService',
        success: function(data) {
            console.log(data);
            if (data == "success") {
                toastr.success('This Service is Active Now', 'Successful', {
                    timeOut: 5000
                })
            } else {
                toastr.error('You Got Error', 'Inconceivable!', {
                    timeOut: 5000
                })
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });
});
// select staff name from selecting role name
$(document).ready(function() {
    $("#staffsByRoleCommunication").on('change', function() {
        $("#checkbox").prop("checked", false);
        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };
        //alert(formData.id);
        // for remove all values from multiple select after select role
        $('#selectStaffss').select2('val', '');
        // get section for student
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'studStaffByRole',
            success: function(data) {
                console.log(data);
                var a = '';
                if (formData.id == 2) {
                    $.each(data, function(i, item) {

                        if (item.length) {
                            $('#selectStaffss').find('option').remove();
                            $('#selectStaffsDiv ul').find('li').not(':first').remove();

                            $.each(item, function(i, students) {
                                $('#selectStaffss').append($('<option>', {
                                    value: students.full_name + '-' + students.email + '-' + students.mobile,
                                    text: students.full_name
                                }));
                            });
                        } else {
                            $('#selectStaffsDiv .current').html('SELECT *');
                            $('#selectStaffss').find('option').not(':first').remove();
                            $('#selectStaffsDiv ul').find('li').not(':first').remove();
                        }
                    });

                }

                if (formData.id == 3) {
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $('#selectStaffss').find('option').remove();
                            $('#selectStaffsDiv ul').find('li').not(':first').remove();

                            $.each(item, function(i, parents) {
                                $('#selectStaffss').append($('<option>', {
                                    value: parents.fathers_name + '-' + parents.guardians_email + '-' + parents.fathers_mobile,
                                    text: parents.fathers_name
                                }));
                            });
                        } else {
                            $('#selectStaffsDiv .current').html('SELECT *');
                            $('#selectStaffss').find('option').not(':first').remove();
                            $('#selectStaffsDiv ul').find('li').not(':first').remove();
                        }
                    });
                }
                if (formData.id != 2 && formData.id != 3) {
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $('#selectStaffss').find('option').remove();
                            $('#selectStaffsDiv ul').find('li').not(':first').remove();

                            $.each(item, function(i, staffs) {
                                $('#selectStaffss').append($('<option>', {
                                    value: staffs.full_name + '-' + staffs.email + '-' + staffs.mobile,
                                    text: staffs.full_name
                                }));
                            });
                        } else {
                            $('#selectStaffsDiv .current').html('SELECT *');
                            $('#selectStaffss').find('option').not(':first').remove();
                            $('#selectStaffsDiv ul').find('li').not(':first').remove();
                        }
                    });

                }

                console.log(a);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });
});


$(document).ready(function() {
    // in communication send To tab selected

    var selectTab = $('.nav-link').attr('selectTab');
    $("#selectTab").val(selectTab);
    $("#initialselectTab").val();
    console.log($("#selectTab").val());



    $(".nav-link").click(function() {
        var selectTab = $(this).attr('selectTab');
        $("#selectTab").val(selectTab);
        $("#initialselectTab").val();
        console.log($("#selectTab").val());
    });
});
// get all section by class_id selection in email sms part
$(document).ready(function() {
    $("#class_id_email_sms").on('change', function() {
        $("#checkbox_section").prop("checked", false);
        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };
        $('#selectSectionss').select2('val', '');
        // get section for student
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'ajaxStudentPromoteSection',
            success: function(data) {
                var a = '';
                $.each(data, function(i, item) {
                    if (item.length) {
                        $('#selectSectionss').find('option').remove();
                        $('#selectSectionsDiv ul').find('li').not(':first').remove();
                        $.each(item, function(i, section) {
                            $('#selectSectionss').append($('<option>', {
                                value: section.id,
                                text: section.section_name
                            }));
                            // $("#selectSectionsDiv ul").append("<li data-value='"+section.id+"' class='option'>"+section.section_name+"</li>");
                        });
                    } else {
                        $('#selectSectionsDiv .current').html('SELECT SECTION *');
                        $('#selectSectionss').find('option').not(':first').remove();
                        $('#selectSectionsDiv ul').find('li').not(':first').remove();
                    }
                });
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });
});
// for upload resume  in Staff Add Module
var resumefileInput = document.getElementById('resume');
if (resumefileInput) {
    //alert("staffs photo");
    resumefileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var resumefileInput = event.srcElement;
        var fileName = resumefileInput.files[0].name;
        document.getElementById('placeholderResume').placeholder = fileName;
    }
}

// for upload joining_letter  in Staff Add Module
var joining_letterfileInput = document.getElementById('joining_letter');
if (joining_letterfileInput) {
    joining_letterfileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var joining_letterfileInput = event.srcElement;
        var fileName = joining_letterfileInput.files[0].name;
        document.getElementById('placeholderJoiningLetter').placeholder = fileName;
    }
}

// for upload other Document  in Staff Add Module
var other_documentfileInput = document.getElementById('other_document');
if (other_documentfileInput) {
    other_documentfileInput.addEventListener('change', showFileName);

    function showFileName(event) {
        var other_documentfileInput = event.srcElement;
        var fileName = other_documentfileInput.files[0].name;
        document.getElementById('placeholderOthersDocument').placeholder = fileName;
    }
}



// for upload main School logo in General Settings
//var upload_logo = document.getElementById('upload_logo');
var upload_logo = document.getElementById('logo_wrapper');
if (upload_logo) {

    upload_logo.addEventListener('change', showFileName);

    function showFileName(event) {
        var upload_logo = event.srcElement;
        var fileName = upload_logo.files[0].name;

    }
}

// for document upload in profile View
var staff_upload_document = document.getElementById('staff_upload_document');
if (staff_upload_document) {
    alert("asdas");
    staff_upload_document.addEventListener('change', showFileName);

    function showFileName(event) {
        var staff_upload_document = event.srcElement;
        var fileName = staff_upload_document.files[0].name;

    }
}

$("#email_engine_type").on('change', function() {
    email_engine_type = $("#email_engine_type").val();
    if (email_engine_type == 'email') {
        $(".smtp_inner_wrapper").slideUp();
    } else {
        $(".smtp_wrapper").slideDown();
        $(".smtp_wrapper_block").slideDown();
        $(".smtp_inner_wrapper").slideDown();
    }
});


// payment gateway submit form paypal
$('form[id="paypal_settings_form"]').validate({
    rules: {
        paypal_username: 'required',
        paypal_password: 'required',
        paypal_signature: 'required',
        paypal_client_id: 'required',
        paypal_secret_id: 'required',
    },
    messages: {
        paypal_username: 'This field is required',
        paypal_password: 'This field is required',
        paypal_signature: 'This field is required',
        paypal_client_id: 'This field is required',
        paypal_secret_id: 'This field is required',

    },
    submitHandler: function(form) {
        // form.submit(event);
        //event.preventDefault();
        form_data = $("#paypal_settings_form").serialize();
        paypal_form_url = $("#paypal_form_url").val();
        url = $("#url").val();
        $.ajax({
            type: "POST",
            data: form_data,
            url: url + '/' + paypal_form_url,
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('Paypal Data has been updated successfully', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
});


// payment gateway submit form Stripe
$('form[id="stripe_form"]').validate({
    rules: {
        stripe_api_secret_key: 'required',
        stripe_publisher_key: 'required'
    },
    messages: {
        stripe_api_secret_key: 'This field is required',
        stripe_publisher_key: 'This field is required'

    },

    submitHandler: function(form) {
        // form.submit(event);
        //event.preventDefault();
        form_data = $("#stripe_form").serialize();
        stripe_form_url = $("#stripe_form_url").val();
        url = $("#url").val();
        $.ajax({
            type: "POST",
            data: form_data,
            url: url + '/' + stripe_form_url,
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('Stripe Data has been updated successfully', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
});

// payment gateway submit form PayUMoney
$('form[id="payumoney_form"]').validate({
    rules: {
        pay_u_money_key: 'required',
        pay_u_money_salt: 'required'
    },
    messages: {
        pay_u_money_key: 'This field is required',
        pay_u_money_salt: 'This field is required'

    },

    submitHandler: function(form) {
        // form.submit(event);
        //event.preventDefault();
        form_data = $("#payumoney_form").serialize();
        payumoney_form_url = $("#payumoney_form_url").val();
        url = $("#url").val();
        $.ajax({
            type: "POST",
            data: form_data,
            url: url + '/' + payumoney_form_url,
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('PayUMoney Data has been updated successfully', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
});

// active payment gateway
function active_payment_gateway(gateway_id = null) {
    if (gateway_id) {
        url = $("#url").val();
        $.ajax({
            type: "POST",
            data: {
                gateway_id: gateway_id
            },
            url: url + '/active-payment-gateway',
            success: function(data) {
                console.log(data);
                if (data == "success") {
                    toastr.success('This Payment Gateway has been activated', 'Successful', {
                        timeOut: 5000
                    })
                } else {
                    toastr.error('You Got Error', 'Inconceivable!', {
                        timeOut: 5000
                    })
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    }
}

// javascript for stripe payment gateway
$(function() {
    var $form = $(".require-validation");
    $('form.require-validation').bind('submit', function(e) {
        var $form = $(".require-validation"),
            inputSelector = ['input[type=email]', 'input[type=password]',
                'input[type=text]', 'input[type=file]',
                'textarea'
            ].join(', '),
            $inputs = $form.find('.required').find(inputSelector),
            $errorMessage = $form.find('div.error'),
            valid = true;
        $errorMessage.addClass('hide');

        $('.has-error').removeClass('has-error');
        $inputs.each(function(i, el) {
            var $input = $(el);
            if ($input.val() === '') {
                $input.parent().addClass('has-error');
                $errorMessage.removeClass('hide');
                e.preventDefault();
            }
        });

        if (!$form.data('cc-on-file')) {
            e.preventDefault();
            Stripe.setPublishableKey($form.data('stripe-publishable-key'));
            Stripe.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: $('.card-expiry-month').val(),
                exp_year: $('.card-expiry-year').val()
            }, stripeResponseHandler);
        }

    });

    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            // token contains id, last4, and card type
            var token = response['id'];
            // insert the token into the form so it gets submitted to the server
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }

});


// add new row for marks distribution
function addRowMark() {
    $("#addRowBtn").button("loading");
    var tableLength = $("#productTable tbody tr").length;
    var url = $('#url').val();
    var tableRow;
    var arrayNumber;
    var count;
    if (tableLength > 0) {
        tableRow = $("#productTable tbody tr:last").attr('id');
        arrayNumber = $("#productTable tbody tr:last").attr('class');
        count = tableRow.substring(3);
        count = Number(count) + 1;
        arrayNumber = Number(arrayNumber) + 1;
    } else {
        // no table row
        count = 1;
        arrayNumber = 0;
    }

    $("#addRowBtn").button("reset");
    var newRow = "<tr id='row1' class='0'>";
    newRow += "<td class='border-top-0'>";
    newRow += "<div class='input-effect'>";
    newRow += "<input class='primary-input form-control' type='text' id='exam_title' name='exam_title[]' autocomplete='off'>";
    newRow += "<label>CT/AT/EXAM</label>";

    newRow += "</div>";
    newRow += "</td>";
    newRow += "<td class='border-top-0'>";
    newRow += "<div class='input-effect'>";
    newRow += "<input class='primary-input form-control exam_mark' type='text' id='exam_mark' name='exam_mark[]' autocomplete='off'>";
    newRow += "</div>";
    newRow += "</td> ";
    newRow += "<td>";
    newRow += "<button class='primary-btn icon-only fix-gr-bg' type='button' id='removeMark'>";
    newRow += "<span class='ti-trash'></span>";
    newRow += "</button>";

    newRow += "</td>";
    newRow += "</tr>";


    if (tableLength > 0) {
        $("#productTable tbody tr:last").after(newRow);
    } else {
        $("#productTable tbody").append(newRow);
    }
    $('.common-select').addClass("new_select_css");

}


// Assign class routine get subject
$(document).on("click", "#removeMark", function(event) {
    $(this).closest("tr").remove();
    var totalMarks = 0;
    $('tr#row1 input[name^="exam_mark"]').each(function() {
        if ($(this).val() != "") {
            totalMarks += parseInt($(this).val());
        }
    });

    $('th#totalMark input').val(totalMarks);
});

$(document).on("keyup", ".exam_mark", function(event) {
    var totalMarks = 0;
    $('tr#row1 input[name^="exam_mark"]').each(function() {
        if ($(this).val() != "") {
            totalMarks += parseInt($(this).val());
        }
    });

    if (totalMarks > parseInt($('#exam_mark_main').val())) {
        alert('you have distributed marks more than exam mark');
        $(this).val(0);
        var totalMarks = 0;
        $('tr#row1 input[name^="exam_mark"]').each(function() {
            if ($(this).val() != "") {
                totalMarks += parseInt($(this).val());
            }
        });
        $('th#totalMark input').val(totalMarks);
        return false;
    }

    $('th#totalMark input').val(totalMarks);
});






$(document).ready(function() {
    //Once add button is clicked
    var countClick = 1;
    $('.custom_checkbox_addR').on('click', function() {

        $(this).css('background-color', '#ddd');
        $(".common-checkbox").removeClass("read-only-input");
        console.log($(".common-checkbox"));

    });

});



$(document).on("click", "#add_competitor_select", function(event) {
    var abc = $(this).closest("div").find("input[id='abc']").val();
    var url = $('#url').val();
    $.ajax({
        url: url + '/' + 'get-enlisted-suppliers',
        type: 'GET',
        dataType: 'json',
        context: this,
        success: function(response) {

                console.log(response);

                var tr = "<tr>";
                tr += "<td>";
                tr += "<div class='input-effect'>";
                tr += "<select class='niceSelect w-100 bb form-control' name='suppliers[]' id='suppliers' style='display:none' required>";
                tr += "<option data-display='Select Supplier *' value='none'>Select Supplier *</option>";

                $.each(response, function(index, value) {
                    tr += '<option value="' + value.id + '">' + value.name + '</option>';
                });

                tr += '</select>';

                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";

                tr += "<span class='current'>Select Supplier *</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select Supplier' class='option selected'>Select Supplier</li>";


                $.each(response, function(key, value) {
                    tr += "<li data-value=" + value.id + " class='option'>" + value.name + "</li>";
                });

                tr += "</ul>";
                tr += '</div>';
                tr += '</div>';
                tr += '</td>';

                tr += '<td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="bid_amount" name="bid_amount[]" autocomplete="off" required="">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="text" id="remarks" name="remarks[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <a href="javascript:void(0);" class="primary-btn icon-only fix-gr-bg remove_button"><span class="ti-close"></span></a>\
                            </td>';


                tr += '</tr>';




                $("#add_competitor_table" + abc + " tbody tr:first").before(tr);
                // $(this).closest('table  tbody tr:first').before(tr);
                // $(this).parent().siblings(".archive-meta-slide")  
            } // /success
    }); // get the product data

});





$(document).ready(function() {
    var maxField = 10; //Input fields increment limitation

    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper

    var radioCount = $('input[name="lowest_bid"]').length;



    var x = 1; //Initial field counter is 1




    $(addButton).on('click', function() {
        //Check maximum number of input fields
        if (x < maxField) {
            x++; //Increment field counter
            var radioCount = $('input[name="lowest_bid"]').length;
            var fieldHTML = '<div class="row  mt-40" id="custom"> <div class="col-lg-4"> <div class="input-effect"> <input class="primary-input form-control" type="text" name="company_name[]" autocomplete="off" placeholder="Company Name"><span class="focus-border"></span> </div> </div> <div class="col-lg-3"> <div class="input-effect"> <input class="primary-input form-control" type="number" step="any" name="bid_amount[]" autocomplete="off" placeholder="Bid Amount">            <span class="focus-border"></span>   </div>  </div> <div class="col-lg-4">  <div class="input-effect"> <input class="primary-input form-control" type="text" name="remarks[]" autocomplete="off" placeholder="Remark"> <span class="focus-border"></span>            </div> </div> <a href="javascript:void(0);" class="primary-btn icon-only fix-gr-bg remove_button"><span class="ti-close"></span></a></div>';
            // $("#sample-div1").prepend('<div class="row  mt-40"><div class="col-lg-4"><div class="input-effect"><input class="primary-input form-control" type="text" name="company_name[]" autocomplete="off" placeholder="Company Name"><span class="focus-border"></span></div></div><div class="col-lg-4"><div class="input-effect"><input class="primary-input form-control" type="number" step="any" name="bid_amount[]" autocomplete="off" placeholder="Bid Amount"><span class="focus-border"></span> </div></div><a href="javascript:void(0);" class="primary-btn icon-only fix-gr-bg remove_button"><span class="ti-close"></span></a></div>');
            $(wrapper).prepend(fieldHTML); //Add field html


            console.log($("#sample-div1"));
            console.log($(wrapper));
            // alert(324);
        }
    });

    //Once remove button is clicked
    // $(wrapper).on('click', '.remove_button', function(e){
    //     e.preventDefault();
    //     $(this).parent('tr').remove(); //Remove field html
    //     x--; //Decrement field counter
    // });
});


$(document).on("click", ".remove_button", function(event) {
    $(this).closest("tr").remove();
});


$('.classRadio').change(function() {
    alert('sdcs');
    if (this.checked) {
        $(this).find('input[type=radio]').not(this)
            .prop('checked', false);
    }
});



$(document).on("keyup mouseup", "form#tender-create-form #quantity", function(event) {

    var product_quantity = parseFloat($(this).closest("tr").find('#product_quantity').val());
    var quantity = parseInt($(this).val());


    if ($(this).val() != "") {
        if (product_quantity >= quantity) {

            if ($(this).val() != "") {
                var quantity = parseFloat($(this).val());

                var unit_price = parseFloat($(this).closest("tr").find("input[id=unit_price]").val());


                if (isNaN(unit_price)) {
                    unit_price = 0;
                }

                var total_price = quantity * unit_price;
                $(this).closest("tr").find("input[id=total_price]").val(total_price);

                var total = 0;


                $('input[id=total_price]').each(function() {
                    total = total + parseInt($(this).val());
                });

                var bid_amount = 0;


                bid_amount = total;
                if ($('input[name=discount_type]').is(':checked')) {
                    if ($('input[name=discount_type]:checked').val() == "P") {
                        var percentage = total / 100 * parseInt($('#discount').val());
                        bid_amount = total - percentage;
                    }

                    if ($('input[name=discount_type]:checked').val() == "A") {
                        bid_amount = total - parseInt($('#discount').val());
                    }
                }



                $('#total').val(total);
                $('#bid_amount').val(bid_amount);


            }
        } else {
            $(this).val('');
            $(this).closest("tr").find('#total_price').val('');
            alert('No Product available')

        }

    }

});


$(document).on("keyup mouseup", "form#tender-create-form #Equantity", function(event) {

    // var product_quantity = parseFloat($('#Eproduct_quantity').val());
    // var product_quantity = parseFloat($(this).closest("tr").find($('#Eproduct_quantity').val()));

    var product_quantity = parseFloat($(this).closest("tr").find('#Eproduct_quantity').val());




    var quantity = parseFloat($(this).val());


    if ($(this).val() != "") {
        if (product_quantity >= quantity) {

            if ($(this).val() != "") {
                var quantity = parseFloat($(this).val());

                var unit_price = parseFloat($(this).closest("tr").find("input[id=Eunit_price]").val());


                if (isNaN(unit_price)) {
                    unit_price = 0;
                }

                var total_price = quantity * unit_price;
                $(this).closest("tr").find("input[id=Etotal_price]").val(total_price);

                var total = 0;


                $('input[id=Etotal_price]').each(function() {
                    total = total + parseFloat($(this).val());
                });

                var bid_amount = 0;


                bid_amount = total;
                if ($('input[name=Ediscount_type]').is(':checked')) {
                    if ($('input[name=Ediscount_type]:checked').val() == "P") {
                        var percentage = total / 100 * parseFloat($('#Ediscount').val());
                        bid_amount = total - percentage;
                    }

                    if ($('input[name=Ediscount_type]:checked').val() == "A") {
                        bid_amount = total - parseFloat($('#Ediscount').val());
                    }
                }


                $('#Etotal').val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
                $('#Ebid_amount').val(parseFloat(Math.round(bid_amount * 100) / 100).toFixed(2));


                // $('#Etotal').val(total);
                // $('#Ebid_amount').val(bid_amount);




            }
        } else {
            $(this).val('');
            $(this).closest("tr").find('#Etotal_price').val('');
            alert('No Product available')

        }

    }

});





$(document).on("keyup mouseup", "#unit_price", function(event) {
    var unit_price = parseFloat($(this).val());
    var quantity = parseFloat($(this).closest("tr").find("input[id=quantity]").val());

    if (isNaN(quantity)) {
        quantity = 0;
    }

    var total_price = quantity * unit_price;
    $(this).closest("tr").find("input[id=total_price]").val(total_price);


    var total = 0;


    $('input[id=total_price]').each(function() {
        total = total + parseInt($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if ($('input[name=discount_type]').is(':checked')) {
        if ($('input[name=discount_type]:checked').val() == "P") {
            var percentage = total / 100 * parseInt($('#discount').val());
            bid_amount = total - percentage;
        }

        if ($('input[name=discount_type]:checked').val() == "A") {
            bid_amount = total - parseInt($('#discount').val());
        }
    }



    $('#total').val(total);
    $('#bid_amount').val(bid_amount);


});


$(document).on("keyup mouseup", "#discount", function(event) {
    var discount = parseFloat($(this).val());

    if (isNaN(discount)) {
        discount = 0;
    }


    var total = 0;


    $('input[id=total_price]').each(function() {
        total = total + parseFloat($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if ($('input[name=discount_type]').is(':checked')) {
        if ($('input[name=discount_type]:checked').val() == "P") {
            var percentage = total / 100 * parseFloat($('#discount').val());
            bid_amount = total - percentage;
        }

        if ($('input[name=discount_type]:checked').val() == "A") {
            bid_amount = total - parseFloat($('#discount').val());
        }
    }



    $('#total').val(total);
    $('#bid_amount').val(bid_amount);


});


$(document).on("keyup mouseup", "#Ediscount", function(event) {
    var discount = parseFloat($(this).val());

    if (isNaN(discount)) {
        discount = 0;
    }


    var total = 0;


    $('input[id=Etotal_price]').each(function() {
        total = total + parseFloat($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if ($('input[name=Ediscount_type]').is(':checked')) {
        if ($('input[name=Ediscount_type]:checked').val() == "P") {
            var percentage = total / 100 * parseFloat($('#Ediscount').val());
            bid_amount = total - percentage;
        }

        if ($('input[name=Ediscount_type]:checked').val() == "A") {
            bid_amount = total - parseFloat($('#Ediscount').val());
        }
    }


    $('#Etotal').val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
    $('#Ebid_amount').val(parseFloat(Math.round(bid_amount * 100) / 100).toFixed(2));

    // $('#Etotal').val(total);
    // $('#Ebid_amount').val(bid_amount);


});


$(document).on("click", "#relationFather, #relationMother", function(event) {

    var total = 0;


    $('input[id=total_price]').each(function() {
        total = total + parseFloat($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if ($(this).is(':checked')) {
        if ($(this).val() == "P") {
            var percentage = total / 100 * parseFloat($('#discount').val());
            bid_amount = total - percentage;
        } else {
            bid_amount = total - parseFloat($('#discount').val());
        }
    }


    $('#total').val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
    $('#bid_amount').val(parseFloat(Math.round(bid_amount * 100) / 100).toFixed(2));

    // $('#total').val(total);
    // $('#bid_amount').val(bid_amount);
});



$(document).on("click", "#ErelationFather, #ErelationMother", function(event) {

    var total = 0;


    $('input[id=Etotal_price]').each(function() {
        total = total + parseFloat($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if ($(this).is(':checked')) {
        if ($(this).val() == "P") {
            var percentage = total / 100 * parseFloat($('#Ediscount').val());
            bid_amount = total - percentage;
        } else {
            bid_amount = total - parseFloat($('#Ediscount').val());
        }
    }




    $('#Etotal').val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
    $('#Ebid_amount').val(parseFloat(Math.round(bid_amount * 100) / 100).toFixed(2));

    console.log(total);
});










$(document).on("click", "#delete-receive-item", function(event) {

    $(this).closest("tr").remove();
});





$(document).on("click", "#addRowProduct", function(event) {


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr>";
                tr += "<td>";
                tr += "<div class='input-effect'>";
                tr += "<select class='niceSelect w-100 bb form-control' name='products[]' id='received_product' style='display:none'>";
                tr += "<option data-display='Select product *' value='none'>Select *</option>";

                $.each(response, function(index, value) {
                    tr += '<option value="' + value.id + '">' + value.name + '</option>';
                });

                tr += '</select>';

                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";

                tr += "<span class='current'>Select product *</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select product' class='option selected'>Select product</li>";


                $.each(response, function(key, value) {
                    tr += "<li data-value=" + value.id + " class='option'>" + value.name + "</li>";
                });

                tr += "</ul>";
                tr += '</div>';
                tr += '</div>';
                tr += '</td>';

                tr += '<td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="text" id="part_number" name="part_number[]" autocomplete="off" readonly="">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="text" id="new_part_number" name="new_part_number[]" autocomplete="off" readonly="">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="text" id="denomination" name="denomination[]" autocomplete="off" readonly="">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="quantity" name="quantity[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="unit_price" name="unit_price[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <input type="hidden" name="product_quantity" id="product_quantity">\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="total_price" name="total_price[]" autocomplete="off" readonly="">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <button class="primary-btn icon-only fix-gr-bg" type="button" id="delete-tender-product">\
                                     <span class="ti-trash"></span>\
                                </button>\
                            </td>';


                tr += '</tr>';




                $("#product-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});




$(document).on("click", "#addRowEquipment", function(event) {


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr>";
                tr += "<td>";
                tr += "<div class='input-effect'>";
                tr += "<select class='niceSelect w-100 bb form-control' name='part_number[]' id='part_number' style='display:none'>";
                tr += "<option data-display='Part No *' value='none'>Part No *</option>";

                $.each(response, function(index, value) {
                    tr += '<option value="' + value.id + '">' + value.part_number + ' / ' + value.description + '</option>';
                });

                tr += '</select>';

                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";

                tr += "<span class='current'>Part No *</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select product' class='option selected'>Part No</li>";


                $.each(response, function(key, value) {
                    tr += "<li data-value=" + value.id + " class='option'>" + value.part_number + ' | ' + value.description + "</li>";
                });

                tr += "</ul>";
                tr += '</div>';
                tr += '</div>';
                tr += '</td>';

                tr += '<td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="text" id="discount" name="discount[]" autocomplete="off" >\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" id="fright_charge" name="fright_charge[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="custom_charge" name="custom_charge[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="taxable_amount" name="taxable_amount[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <input type="hidden" name="Eproduct_quantity" id="Eproduct_quantity">\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="vat" name="vat[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="vat_amount" name="vat_amount[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <div class="input-effect">\
                                    <input class="primary-input form-control" type="number" step="any" id="serial_number" name="serial_number[]" autocomplete="off">\
                                    <span class="focus-border"></span>\
                                </div>\
                            </td>\
                            <td>\
                                <button class="primary-btn icon-only fix-gr-bg" type="button" id="Edelete-tender-product">\
                                     <span class="ti-trash"></span>\
                                </button>\
                            </td>';


                tr += '</tr>';




                $("#equipment-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});







$(document).on("click", "#addRowQT", function(event) {

    var i = $('#qt-row-count').val();
    var net_vat = $('#net_vat').val();


    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

                console.log(response);

                var tr = "<tr id='rowone" + i + "'  onclick='fn_addRow(" + i + ")'>";
                tr += "<td>";
                // tr += "<div class='input-effect'>";
                // tr += "<select class='niceSelect w-100 bb form-control' name='part_number[]' id='part_number' style='display:none'>";
                // tr += "<option data-display='Part No *' value='none'>Part No *</option>";

                // $.each(response, function(index, value) {
                //     tr += '<option value="' + value.id + '">' + value.part_number + ' / ' + value.description + '</option>';
                // });

                // tr += '</select>';
                tr += "<select class='w-100 sstxtbx' name='part_number[]' id='part_number_" + i + "' onchange='ddl_part_change(" + i + ")'>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.part_number + "</option>";
                });
                tr += "</select>";
                tr += '</td>';
                tr += '<td>';

                tr += "<select class='w-100 sstxtbx' name='part_number_txt[]' id='part_number_txt_" + i + "' readonly='true' hidden>";
                tr += "<option value='none'></option>";
                $.each(response, function(key, value) {
                    tr += "<option value=" + value.id + ">" + value.description + "</option>";
                });
                tr += "</select>";


                tr += '<input class="w-100 sstxtbx" type="text" id="description_' + i + '" name="description[]" autocomplete="off" readonly="true"></td>';

                
                tr += '<td><select class="w-100 sstxtbx" name="tax[]" id="tax_' + i + '" readonly="true" onchange="calc_change(' + i + ')">\
                                                            <option value="' + 5 + '">VAT ' + 5 + '%</option>\
                                                            <option value="0">None</option>\
                                                        </select></td>';

                tr += '<td><input class="w-100 sstxtbx" type="number" id="qty_' + i + '" name="qty[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="unitprice_' + i + '" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="value_' + i + '" name="value[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="discount_' + i + '" name="discount[]" autocomplete="off" min="0" onchange="calc_change(' + i + ')"></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="taxableamount_' + i + '" name="taxableamount[]" autocomplete="off" min="0" readonly></td>\
                        <td><input class="w-100 sstxtbx" type="number" id="vatamount_' + i + '" name="vatamount[]" autocomplete="off" min="0" readonly></td>\
                            ';
                tr += '</tr>';




                $("#po-table tbody tr:last").after(tr);
            } // /success
    }); // get the product data

});

$(document).on("change", "#received_product", function(event) {

    var url = $('#url').val();

    if ($(this).val() == 'none' || $(this).val() == null) {
        $(this).closest("tr").find('#part_number').val('');
        $(this).closest("tr").find('#new_part_number').val('');
        $(this).closest("tr").find('#denomination').val('');
        $(this).closest("tr").find('#quantity').val('');
        $(this).closest("tr").find('#unit_price').val('');
        $(this).closest("tr").find('#product_quantity').val('');
        return false;
    }


    var selected_id = $(this).val();




    var count = 0;
    $('select[id=received_product]').each(function() {

        if ($(this).val() == selected_id) {
            count++;
        }

    });

    if (count > 1) {

        $(this).closest("tr").find('span.current').html('SELECT PRODUCT *');

        $(this).closest("tr").find('#part_number').val('');
        $(this).closest("tr").find('#new_part_number').val('');
        $(this).closest("tr").find('#denomination').val('');
        $(this).closest("tr").find('#quantity').val('');

        $(this).closest("tr").find('#unit_price').val('');
        $(this).closest("tr").find('#product_quantity').val('');
        alert('Alreday selected the product');
        return false;
    }






    var formData = {
        id: $(this).val()
    };

    console.log(formData);

    $.ajax({
        type: "GET",
        data: formData,
        context: this,
        dataType: 'json',
        url: url + '/' + 'get-receive-item-details',
        success: function(data) {


            if (data[1] == 0 || $(this).val() == 'none') {
                alert('no product in stock');
                $(this).closest("tr").find('#part_number').val('');
                $(this).closest("tr").find('#new_part_number').val('');
                $(this).closest("tr").find('#denomination').val('');
                $(this).closest("tr").find('#quantity').val('');

                $(this).closest("tr").find('#unit_price').val('');
                $(this).closest("tr").find('#product_quantity').val(0);
                $(this).closest("tr").find('#total_price').val('');
            } else {
                $(this).closest("tr").find('#part_number').val(data[0].part_number);
                $(this).closest("tr").find('#new_part_number').val(data[0].new_part_number);
                $(this).closest("tr").find('#denomination').val(data[0].denomination);

                $(this).closest("tr").find('#unit_price').val(data[0].sale_price);
                $(this).closest("tr").find('#product_quantity').val(data[1]);
            }




        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});




$(document).on("change", "#Ereceived_product", function(event) {

    var url = $('#url').val();

    if ($(this).val() == 'none' || $(this).val() == null) {
        $(this).closest("tr").find('#Eproduct_model').val('');
        $(this).closest("tr").find('#Edenomination').val('');
        $(this).closest("tr").find('#Equantity').val('');
        $(this).closest("tr").find('#Eunit_price').val('');
        $(this).closest("tr").find('#Eproduct_quantity').val('');
        return false;
    }


    var selected_id = $(this).val();




    var count = 0;
    $('select[id=Ereceived_product]').each(function() {

        if ($(this).val() == selected_id) {
            count++;
        }

    });

    if (count > 1) {

        $(this).closest("tr").find('span.current').html('SELECT PRODUCT *');

        $(this).closest("tr").find('#Eproduct_model').val('');
        $(this).closest("tr").find('#Edenomination').val('');
        $(this).closest("tr").find('#Equantity').val('');

        $(this).closest("tr").find('#Eunit_price').val('');
        $(this).closest("tr").find('#Eproduct_quantity').val('');
        alert('Alreday selected the product');
        return false;
    }


    var formData = {
        id: $(this).val(),
        product_id: $('#id').val()
    };

    console.log(formData);

    $.ajax({
        type: "GET",
        data: formData,
        context: this,
        dataType: 'json',
        url: url + '/' + 'get-receive-item-details',
        success: function(data) {


            if (data[1] == 0 || $(this).val() == 'none') {
                alert('no product in stock');
                $(this).closest("tr").find('#Eproduct_model').val();
                $(this).closest("tr").find('#Edenomination').val();
                $(this).closest("tr").find('#Equantity').val();

                $(this).closest("tr").find('#Eunit_price').val();
                $(this).closest("tr").find('#Eproduct_quantity').val();
            }

            $(this).closest("tr").find('#Eproduct_model').val('');
            $(this).closest("tr").find('#Edenomination').val(data[0].denomination);

            $(this).closest("tr").find('#Eunit_price').val(data[0].sale_price);
            $(this).closest("tr").find('#Eproduct_quantity').val(data[1]);


        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

$(document).on("click", "#delete-tender-product", function(event) {
    $(this).closest("tr").remove();
    var total = 0;

    $('input[id=total_price]').each(function() {
        total = total + parseInt($(this).val());
    });
    var bid_amount = 0;
    bid_amount = total;
    if ($('input[name=discount_type]').is(':checked')) {
        if ($('input[name=discount_type]:checked').val() == "P") {
            var percentage = total / 100 * parseInt($('#discount').val());
            bid_amount = total - percentage;
        }

        if ($('input[name=discount_type]:checked').val() == "A") {
            bid_amount = total - parseInt($('#discount').val());
        }
    }

    $('#total').val(total);
    $('#bid_amount').val(bid_amount);


});






$(document).on("click", "#Edelete-tender-product", function(event) {
    $(this).closest("tr").remove();
    var total = 0;

    $('input[id=Etotal_price]').each(function() {
        total = total + parseFloat($(this).val());
    });
    var bid_amount = 0;
    bid_amount = total;
    if ($('input[name=Ediscount_type]').is(':checked')) {
        if ($('input[name=Ediscount_type]:checked').val() == "P") {
            var percentage = total / 100 * parseFloat($('#Ediscount').val());
            bid_amount = total - percentage;
        }

        if ($('input[name=Ediscount_type]:checked').val() == "A") {
            bid_amount = total - parseFloat($('#Ediscount').val());
        }
    }

    $('#Etotal').val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
    $('#Ebid_amount').val(parseFloat(Math.round(bid_amount * 100) / 100).toFixed(2));

    // $('#Etotal').val(total);
    // $('#Ebid_amount').val(bid_amount);


});









// $(document).on("keyup mouseup", "form#item-receive-form #quantity", function (event) {

//         $(this).closest('tr').next().remove();

//     var number_of_option =  parseInt($(this).val());

//     var tr = '';
//     tr += "<tr class='part-number'>";
//     tr += "<td colspan='6'>";
//     tr += "<div class='row'>";
//     for (var i = 1; i <= number_of_option; i++) {



//          tr += '<div class="col-md-2">\
//             <div class="input-effect">\
//                <input class="primary-input form-control" type="text" step="any" id="part_number" name="part_number[]" autocomplete="off" placeholder="Enter part number">\
//                 <span class="focus-border"></span>\
//               </div>\
//               </div>';

//     }



//     tr += "</div>";
//     tr += "</td>";

//     tr += "</tr>";

//     console.log(tr);

//     $(this).closest("tr").after(tr);



// });



$(document).on("keyup", "form#item-receive-form #quantity", function(event) {

    var number_of_option = parseInt($(this).val());

    var tr = '';
    tr += "<div class='row'>";
    for (var i = 1; i <= number_of_option; i++) {

        tr += "<div class='col-md-12'>";
        tr += "<div class='input-effect'>";
        tr += "<input class='primary-input form-control' type='number' step='any' id='quantity' name='quantity[]' autocomplete='off'>";

        tr += "</div>";
        tr += "</div>";

    }


    tr += "</div>";



    var $modalDialog = $(

            tr

        )
        .appendTo('body')
        .dialog({
            resizable: false,
            autoOpen: false,
            width: 350,
            show: 'fold',
            title: 'Enter Product part number',
            buttons: [{
                    text: "Cancel",
                    id: "btnCancel",
                    click: function() {
                        $(this).dialog("close");
                    },
                },
                {
                    text: "Save Data",
                    id: "btnCreateRev",
                    click: function() {
                        //code for creating a revision
                    }
                }
            ],
            modal: true
        });


    $modalDialog.dialog("open");


});


// $('#dialogueForm').live("dialogclose", function(){
//    //your code to run on dialog close
// });



$('#type').on('change', function(e) {
    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;
    if (valueSelected == 'I') {
        $(".default_expense_head").css("display", "none");
    } else {
        $(".default_expense_head").css("display", "block");
    }
});





$('#work_order_mode').on('change', function(e) {

    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;

    // $("#product-table tbody tr:gt(0)").remove();
    // $("#equipment-table tbody tr:gt(0)").remove();


    if (valueSelected == 'spareparts') {
        $(".comon-status").css("display", "none");
        $(".spareparts").css("display", "block");
    } else {
        $(".comon-status").css("display", "none");
        $(".equipment").css("display", "block");
    }
    console.log(valueSelected);
});




$('#status_mode').on('change', function(e) {

    var optionSelected = $("option:selected", this);
    var valueSelected = this.value;

    if (valueSelected == 'Running') {
        $(".comon-status").css("display", "none");
        $(".Running").css("display", "block");
    } else if (valueSelected == 'Shipment') {
        $(".comon-status").css("display", "none");
        $(".Shipment").css("display", "block");
    } else if (valueSelected == 'Delivered') {
        $(".comon-status").css("display", "none");
        $(".Delivered").css("display", "block");
    } else if (valueSelected == 'InspectionComplete') {
        $(".comon-status").css("display", "none");
        $(".InspectionComplete").css("display", "block");
    } else if (valueSelected == 'Completed') {
        $(".comon-status").css("display", "none");
        $(".Completed").css("display", "block");
    }

    console.log(valueSelected);
});





$('#printButton').on('click', function() {
    if ($('.modal').is(':visible')) {
        var modalId = $(event.target).closest('.modal').attr('id');
        $('body').css('visibility', 'hidden');
        $("#" + modalId).css('visibility', 'visible');
        $('#' + modalId).removeClass('modal');
        window.print();
        $('body').css('visibility', 'visible');
        $('#' + modalId).addClass('modal');
    } else {
        window.print();
    }
});



// select item name from selecting item category name
$(document).ready(function() {
    $("#infix_theme_rtl").on('change', function() {
        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };
        console.log(formData);
        $.ajax({
            type: "GET",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'theme-style-rtl',
            success: function(data) {
                location.reload();
                console.log(data);
            }
        });
    });
});