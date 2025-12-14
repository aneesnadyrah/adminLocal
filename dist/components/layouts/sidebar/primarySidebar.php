<!--begin::Nav-->
<div class="aside-nav d-flex flex-column align-items-center flex-column-fluid w-100 pt-5 pt-lg-0" id="kt_aside_nav">
    <!--begin::Wrapper-->
    <div class="px-5">
        <!--begin::Nav-->
        <ul class="nav flex-column w-100" id="kt_aside_nav_tabs">
            <!--begin::Nav item-->
            <li class="nav-item mb-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" data-bs-dismiss="click" title="Menu">
                <!--begin::Nav link-->
                <a class="nav-link btn btn-icon btn-active-color-primary btn-color-gray-400 btn-active-light active" data-bs-toggle="tab" href="#tab_menu">
                    <i class="fad fa-grid-2 fs-2"></i>
                </a>
                <!--end::Nav link-->
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mb-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" data-bs-dismiss="click" title="Peta Digital">
                <!--begin::Nav link-->
                <a class="nav-link btn btn-icon btn-active-color-primary btn-color-gray-400 btn-active-light" data-bs-toggle="tab" href="#tab_map">
                    <i class="fad fa-map fs-2"></i>
                </a>
                <!--end::Nav link-->
            </li>
            <li class="nav-item mb-2" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-dismiss="click" title="Storan Awan">
                <a href="https://cloud.kutt.my" class="btn btn-icon btn-active-color-primary btn-color-gray-400 btn-active-light" >
                    <i class="fad fa-hard-drive fs-2"></i>
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <!-- <li class="nav-item mb-2" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" data-bs-dismiss="click" title="Sumber Manusia"> -->
                <!--begin::Nav link-->
                <!-- <a class="nav-link btn btn-icon btn-active-color-primary btn-color-gray-400 btn-active-light" data-bs-toggle="tab" href="#kt_aside_nav_tab_tasks">
                    <i class="fad fa-id-card-clip fs-2"></i>
                </a> -->
                <!--end::Nav link-->
            <!-- </li> -->
            <!--end::Nav item-->
        </ul>
        <!--end::Tabs-->
        <div class="separator"></div>
        <!--begin::Navbar item-->
        <div class="app-navbar-item flex-center py-4" id="kt_aside_pin">
            <!--begin::Navbar link-->
            <a href="#" class="btn btn-icon btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-pin-project" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="right" data-bs-dismiss="click" title="Tambah Projek Kegemaran">
                <i class="fad fa-plus fs-2"></i>
            </a>
            <!--end::Navbar link-->
        </div>
        <!--end::Navbar item-->
    </div>
    <div class="scroll-y" data-kt-scroll="true" data-kt-scroll-activate="{default: true, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_aside_nav" data-kt-scroll-dependencies="#kt_aside_nav_tabs, #kt_aside_logo, #kt_aside_footer, #kt_aside_pin" data-kt-scroll-offset="0px">

        <div id="pin-project">
            <!--begin::Pinned Projects-->
            <div class="app-navbar-item-container"></div>
            <!--end::Pinned Projects-->
        </div>


    </div>
    <!--end::Nav-->
</div>
<!--end::Nav-->
<!--begin::Footer-->
<div class="aside-footer d-flex flex-column align-items-center flex-column-auto" id="kt_aside_footer">
    <!--begin::Activities-->
    <!-- <div class="d-flex align-items-center mb-3"> -->
        <!--begin::Drawer toggle-->
        <!-- <div class="btn btn-icon btn-active-color-primary btn-color-gray-400 btn-active-light" data-kt-menu-trigger="click" data-kt-menu-overflow="true" data-kt-menu-placement="top-start" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-dismiss="click" title="Log Aktiviti" id="kt_activities_toggle">
            <i class="fad fa-wave-pulse fs-2"></i>
        </div> -->
        <!--end::drawer toggle-->
    <!-- </div> -->
    <!--end::Activities-->
    <!--begin::Cloud Storage-->
    <!-- <div class="d-flex align-items-center mb-2"> -->
        <!--begin::Menu wrapper-->
        <!-- <a href="javascript:void()" class="btn btn-icon btn-active-color-primary btn-color-gray-400 btn-active-light" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-dismiss="click" title="Bantuan Teknikal">
            <i class="fad fa-life-ring fs-2"></i>
        </a> -->
        <!--end::Menu wrapper-->
    <!-- </div> -->
    <!--end::Cloud Storage-->
    <!--begin::User-->
    <div class="d-flex align-items-center mb-10" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="cursor-pointer symbol symbol-40px" data-kt-menu-trigger="click" data-kt-menu-overflow="true" data-kt-menu-placement="top-start" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-dismiss="click" title="Profil Pengguna">
            <img src="assets/media/avatars/blank.jpg" alt="image" />
        </div>
        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-50px me-5">
                        <img alt="Logo" src="assets/media/avatars/blank.jpg" />
                    </div>
                    <!--end::Avatar-->
                    <!--begin::Username-->
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5">
                            <!-- <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Pro</span> -->
							<?php echo $data->first_name ? ucfirst($data->first_name) : ''; ?>
                        </div>
                        <div class="fw-semibold text-gray-400 d-flex align-items-center fs-6">
                            <!-- <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Pro</span> -->
							<?php echo $data->position ? ucfirst($data->position) : ''; ?>
                        </div>
                    </div>
                    <!--end::Username-->
                </div>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu separator-->
            <div class="separator my-2"></div>
            <!--end::Menu separator-->
            <!--begin::Menu item-->
            <div class="menu-item px-5">
                <a href="account/profile" class="menu-link px-5">Profil Saya</a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-5 my-1">
                <a href="dashboard" class="menu-link px-5">Tetapan</a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu separator-->
            <div class="separator my-2"></div>
            <!--end::Menu separator-->
            <!--begin::Menu item-->
            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-end" data-kt-menu-offset="-15px, 0">
                <a href="#" class="menu-link px-5">
                    <span class="menu-title position-relative">Bahasa
                        <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">Bahasa Melayu
                            <img class="w-15px h-15px rounded-1 ms-2" src="assets/media/flags/malaysia.svg" alt="" /></span></span>
                </a>
                <!--begin::Menu sub-->
                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="javascript:void()" class="menu-link d-flex px-5 active">
                            <span class="symbol symbol-20px me-4">
                                <img class="rounded-1" src="assets/media/flags/malaysia.svg" alt="" />
                            </span>Bahasa Melayu</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="javascript:void()" class="menu-link d-flex px-5">
                            <span class="symbol symbol-20px me-4">
                                <img class="rounded-1" src="assets/media/flags/united-kingdom.svg" alt="" />
                            </span>English</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu sub-->
            </div>
            <!--end::Menu item-->

            <!--begin::Menu item-->
            <div class="menu-item px-5">
                <a href="/auth/signout" class="menu-link px-5">Log Keluar</a>
            </div>
            <!--end::Menu item-->
        </div>
        <!--end::User account menu-->
        <!--end::Menu wrapper-->
    </div>
    <!--end::User-->
</div>
<!--end::Footer-->