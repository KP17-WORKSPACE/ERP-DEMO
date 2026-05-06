{{-- resources/views/backEnd/company/companyEdit.blade.php --}}
@extends('backEnd.newmasterpage')

@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
<div id="companyApp">
  @include('backEnd.company._form') {{-- yahi same form jo create me use ho raha --}}
</div>

<script>
  const IS_EDIT = true;
  const EDIT_ID = {{ $company->id }};
  const SEED    = @json($seed); // controller se bheja hua payload
</script>

{{-- yahi same JS file/inline script jise aap create page me use kar rahe ho --}}
@include('backEnd.company._company_js') 
@endsection
