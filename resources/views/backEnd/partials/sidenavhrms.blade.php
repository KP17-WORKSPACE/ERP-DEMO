<nav class="main-nav sidebar-new">
    <div class="toggle-nav"></div>
    <ul class="nav-list">
        <li class="nav-item {{ @App\SysHelper::isMenuOpen(['crm-dashboard'], 'active show-subnav') }}">
            <a href="{{ url('/crm-dashboard') }}" class="nav-link">
                <!-- <i class="ico icon-outline-widget-6"></i> -->
                <img src="{{ asset('public/design') }}/assets/images/icons/dashboard.png" height="24px" title="Dashboard">
                <span class="nav-text">Dashboard</span>
            </a>
        </li>




          {{-- HRMS --}}
        <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        @if (count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item {{ @App\SysHelper::isMenuOpen(['company/policy', 'staff-directory', 'approvals', 'employee/leaves/', 'crm-reimbursement-request'], 'active show-subnav') }}">
                <div class="sub-menu-nav" data-subnav="subnavHrms">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">HRMS</span>
                </div>
                <div class="subnav-menu" id="subnavHrms">
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('company/policy') }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 66)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('company/policy') }}"  class="sub-nav-link">Company Policy
                            </a>
                        @endif
                    </div>
                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('staff-directory') }}">

                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 67)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('staff-directory') }}" class="sub-nav-link">Employee Management</a>
                        @endif

                    </div>

                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('approvals') }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 68)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('approvals/inbox') }}"  class="sub-nav-link">Leave
                                Management</a>
                        @endif
                    </div>

                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee/leaves') }}">

                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0)
                            <a href="{{ url('employee/leaves/') }}"  class="sub-nav-link">Leaves </a>
                        @endif

                    </div>


                      <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('attendance.index') }}">

                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                            <a href="{{  route('attendance.index') }}"  class="sub-nav-link">Attendance </a>
                        {{-- @endif --}}

                    </div>

                       <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee.loans.index') }}">

                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                            <a href="{{  route('employee.loans.index') }}"  class="sub-nav-link">Loans &amp; Advance </a>
                        {{-- @endif --}}

                    </div>

                     <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee.loans.index') }}">
                        {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                            <a href="{{  route('staff.compensation.create') }}"  class="sub-nav-link">Compensation & Roles Changes </a>
                        {{-- @endif --}}
                    </div>


                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('employee.loans.index') }}">
                    {{-- @if (count($hrms->where('is_read', 1)->where('module_link_id', 69)) > 0) --}}
                    <a href="{{  route('staff.resignation.add') }}"  class="sub-nav-link">End of Service </a>
                    {{-- @endif --}}
                    </div>


                    <div class="sub-nav-item {{ @App\SysHelper::isActiveRoute('crm-reimbursement-request') }}">
                        @if (count($hrms->where('is_read', 1)->where('module_link_id', 70)) > 0 || Auth::user()->role_id == 1)
                            <a href="{{ url('crm-reimbursement-request') }}" class="sub-nav-link">Reimbursement
                                Request</a>
                        @endif
                    </div>


                   

                    

                </div>
            </li>
        @endif



    </ul>
</nav>
