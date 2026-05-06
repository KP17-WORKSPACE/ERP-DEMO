<div class="purchase-order-content-header sticky-top justify-content-end" style="background-color: #f7f8fd">

    <div class="purchase-order-content-header-right just">





        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">


                <li><a href="{{ url('onboarding-employee-list') }}"
                        class="dropdown-item d-flex align-items-center text-dark"><i
                            class="ico icon-outline-document-text text-success  title-15 me-2"></i> Onboard Employee
                        List</a>
                </li>

                <li><a data-copy-url="{{ url('onboard-employee/' . session('logged_session_data.company_id')) }}"
                        title="Click to copy link"
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"><i
                            class="ico icon-outline-user-plus text-success  title-15 me-2"></i> Onboard Employee
                        Link</a>
                </li>


            </ul>
        </div>


    </div>
</div>
