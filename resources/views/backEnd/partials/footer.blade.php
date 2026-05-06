@php
    $setting = App\SmGeneralSettings::find(1);
    if(isset($setting->copyright_text)){ @$copyright_text = @$setting->copyright_text; }else{ @$copyright_text = 'Copyright 2021 All rights reserved by Codethemes'; }

@endphp
</div>
</div>

<div class="has-modal modal fade" id="showDetaildModal">
    <div class="modal-dialog modal-dialog-centered" id="modalSize">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="showDetaildModalTile">@lang('lang.new') @lang('lang.client') @lang('lang.information')</h4>
                <button type="button" class="close icons" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" id="showDetaildModalBody">

            </div>

            <!-- Modal footer -->

        </div>
    </div>
</div>


<!--  Start Modal Area -->
<div class="modal fade invoice-details" id="showDetaildModalInvoice">
    <div class="modal-dialog large-modal modal-dialog-centered" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('lang.add') @lang('lang.invoice')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" id="showDetaildModalBodyInvoice">
            </div>

        </div>
    </div>
</div>
<!-- End                         Modal Area -->


<!-- ================Footer Area ================= -->
<footer class="footer-area">
    <div class="container">
        <div class="row">

            <div class="col-lg-12 text-center">
                <p>{!! @$copyright_text !!} </p>
            </div>
        </div>
    </div>
</footer>
<!-- ================End Footer Area ================= -->

<script src="{{asset('public/backEnd/')}}/vendors/js/jquery-3.2.1.min.js"></script>
<script src="{{asset('public/backEnd/')}}/vendors/js/jquery-ui.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/jquery.data-tables.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/dataTables.buttons.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/buttons.flash.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/jszip.min.js"></script>
<script src="{{asset('public/backEnd/')}}/vendors/js/pdfmake.min.js"></script>
<script src="{{asset('public/backEnd/')}}/vendors/js/vfs_fonts.js"></script>

<script src="{{asset('public/backEnd/')}}/vendors/js/buttons.html5.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/buttons.print.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/dataTables.rowReorder.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/dataTables.responsive.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/buttons.colVis.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/popper.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/bootstrap.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/nice-select.min.js"></script>
<script src="{{asset('public/backEnd/')}}/vendors/js/jquery.magnific-popup.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/fastselect.standalone.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/raphael-min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/morris.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/nice-select.min.js">
</script>
<script src="{{asset('public/backEnd/')}}/vendors/js/ckeditor.js"></script>
<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/toastr.min.js"></script>

<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/moment.min.js"></script>


<script src="{{asset('public/backEnd/')}}/vendors/js/bootstrap-datetimepicker.min.js"></script>


<script src="{{asset('public/backEnd/')}}/vendors/js/printThis.js"></script>
<script src="{{asset('public/backEnd/')}}/vendors/js/bootstrap-datepicker.min.js">
</script>


<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/fullcalendar.min.js"></script>


<script type="text/javascript" src="{{asset('public/backEnd/')}}/js/jquery.validate.min.js"></script>
<script src="{{asset('public/backEnd/')}}/vendors/js/select2/select2.min.js"></script>

<script src="{{asset('public/backEnd/')}}/js/main.js"></script>
<script src="{{asset('public/backEnd/')}}/js/custom.js"></script>
<script src="{{asset('public/backEnd/')}}/js/developer.js"></script>
<script src="{{asset('public/backEnd/')}}/js/erpjs.js"></script>
<script src="{{asset('public/backEnd/')}}/js/form-validation.js"></script>


<script type="text/javascript">

    // for select2 multiple dropdown in send email/Sms in Individual Tab
    $("#selectStaffss").select2();
    $("#checkbox").on('click', function () {
        if ($("#checkbox").is(':checked')) {
            $("#selectStaffss > option").prop("selected", "selected");
            $("#selectStaffss").trigger("change");
        } else {
            $("#selectStaffss > option").removeAttr("selected");
            $("#selectStaffss").trigger("change");
        }
    });


    // for select2 multiple dropdown in send email/Sms in Class tab
    $("#selectSectionss").select2();
    $("#checkbox_section").on('click', function () {
        if ($("#checkbox_section").is(':checked')) {
            $("#selectSectionss > option").prop("selected", "selected");
            $("#selectSectionss").trigger("change");
        } else {
            $("#selectSectionss > option").removeAttr("selected");
            $("#selectSectionss").trigger("change");
        }
    });

</script>


<script>

   $(document).ready(function () {
        $('#languageChange').on('change', function () {
        var str = $('#languageChange').val();
        var url = $('#url').val();
        var formData = {
            id: $(this).val()
        };
      console.log(formData);
        $.ajax({
            type: "POST",
            data: formData,
            dataType: 'json',
            url: url + '/' + 'language-change',
            success: function (data) {
                url= url + '/' + 'locale'+ '/' + data[0].language_universal;
                window.location.href = url;
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
});


$(function () {
  $('[data-toggle="tooltip"]').tooltip({
    container : 'body'
  });
});

</script>

 <script>
    $("a[name=tabs]").on("click", function () {
               var a = $(this).data("id");
                $.ajax({
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    type: 'post',
                    url: "{{ route('admin.ticket_show')}}",
                    data: {
                        id:a
                    },
                    dataType : 'json',
                    success: function(data) {
                        console.log(data);

                       
                    }
                });
       });
   </script>
{!! Toastr::message() !!}

@yield('script')

</body>
</html>
