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




          <?php $hrms = $permissions->wherein('module_link_id', [66, 67, 68, 69, 70]); ?>
        @if (count($hrms->where('is_read', 1)) > 0 || Auth::user()->role_id == 1)
            <li
                class="nav-item">
                <div class="sub-menu-nav" data-subnav="subnavMarketing">
                    <!-- <i class="ico icon-outline-document-text"></i> -->
                    <img src="{{ asset('public/design') }}/assets/images/icons/management.png" height="24px"
                        title="HRMS">
                    <span class="nav-text">Marketing</span>
                </div>
                <div class="subnav-menu" id="subnavMarketing">
                    <div class="sub-nav-item">
                            <a href="{{ url('#') }}"  class="sub-nav-link">A
                            </a>
                    </div>
                    <div class="sub-nav-item">
                            <a href="{{ url('#') }}"  class="sub-nav-link">B
                            </a>
                    </div>
                    
                    <div class="sub-nav-item">
                            <a href="{{ url('#') }}"  class="sub-nav-link">C
                            </a>
                    </div>
                    
                    

                </div>
            </li>
        @endif



    </ul>
</nav>
