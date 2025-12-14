<!--begin::Workspace-->
<div class="aside-workspace my-5 p-5" id="kt_aside_wordspace">
    <div class="d-flex h-100 flex-column">
        <!--begin::Wrapper-->
        <div class="flex-column-fluid" data-kt-scroll="true" data-kt-scroll-activate="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_aside_wordspace"
            data-kt-scroll-dependencies="#kt_aside_secondary_footer" data-kt-scroll-offset="0px">
            
            <!--begin::Tab content-->
            <div class="tab-content">
                <!--begin::Tab pane-->
                <div class="tab-pane fade active show" id="tab_menu" role="tabpanel">
                    <!--begin::Menu-->
                    <div class="menu menu-column menu-fit menu-rounded menu-title-gray-600 menu-icon-gray-400 menu-state-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500 fw-semibold fs-5 px-6 my-5 my-lg-0"
                        id="kt_aside_menu" data-kt-menu="true">
                        <div id="kt_aside_menu_wrapper" class="menu-fit">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link <?php echo General::isMenuActive("/dashboard/"); ?>"
                                    href="/dashboard/">
                                    <i class="menu-icon fad fa-objects-column fs-4 "></i>
                                    <span class="menu-title">Papan Pemuka</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item pt-5">
                                <!--begin:Menu content-->
                                <div class="menu-content">
                                    <span class="menu-heading fw-bold text-uppercase fs-7">Permohonan</span>
                                </div>
                                <!--end:Menu content-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="click"
                                class="menu-item menu-accordion <?php echo General::isAccordianActive("/tasks/new"); ?>">
                                <!--begin:Menu link-->
                                <span
                                    class="menu-link <?php echo General::isMenuActive("/tasks/new"); ?> <?php echo General::isMenuActive("/projects/tasks/pending.php"); ?>">
                                    <span class="menu-icon">
                                        <i class="fad fa-bars-progress fs-4"></i>
                                    </span>
                                    <span class="menu-title">Tugasan</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion">
                                    <!--begin:Menu item-->
                                    <?php
                                    $subDepartment = $Role->$username->sub_department;

                                    switch ($subDepartment) {
                                        case 'mapping':
                                            echo '<div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("mapping/general/tasks/new") . '" href="mapping/general/tasks/new" ">
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Tugas</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div><div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/general/tasks/pending") . '" href="operation/general/tasks/pending">
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tugasan Tertunggak</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div>';
                                            break;
                                        case 'permit':
                                            echo '<div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/general/tasks/new") . '" href="operation/general/tasks/new" ">
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Tugas</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div><div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/authority/tasks/new") . '" href="operation/authority/tasks/new" >
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Tugas Pihak Berkuasa</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div>';
                                            break;
                                        case 'project':
                                            echo '<div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/general/tasks/new") . '" href="operation/general/tasks/new" ">
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Tugas</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div><div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/general/tasks/pending") . '" href="operation/general/tasks/pending">
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tugasan Tertunggak</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div><div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/authority/tasks/new") . '" href="operation/authority/tasks/new" >
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Tugas Pihak Berkuasa</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div>';
                                            break;
                                        default:
                                            echo '<div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("management/general/tasks/new") . '" href="management/general/tasks/new" >
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Tugas</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div><div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("operation/general/tasks/pending") . '" href="operation/general/tasks/pending">
                                            <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tugasan Tertunggak</span>
                                            </a>
                                            <!--end:Menu link-->
                                            </div>';
                                            break;
                                    }

                                    ?>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link <?php echo General::isMenuActive("/letter/in/list"); ?>"
                                            href="/letter/in/list">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Surat Masuk</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>

                                    <!--end:Menu item-->
                                    <?php
                                    if (($_SESSION["roleId"] == 14) || ($_SESSION["roleId"] == 13) || ($_SESSION["roleId"] == 12) || ($_SESSION["roleId"] == 19)) {
                                        echo '<div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/letters/list") . '" href="/letters/list" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Surat Permohonan </span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>';
                                    }

                                    if (($_SESSION["roleId"] == 17) || ($_SESSION["roleId"] == 19)) {
                                        echo '<div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/wayleave/summary/list") . '" href="/wayleave/summary/list" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Ringkasan Projek</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/wayleave/deposit/list") . '" href="/wayleave/deposit/list" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Wang Cagaran</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>';
                                    }

                                    if (($_SESSION["roleId"] == 28) || ($_SESSION["roleId"] == 24) || ($_SESSION["roleId"] == 26)) {
                                        echo '<div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/surveys/priority/new") . '" href="/surveys/priority/new" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tugasan Utama Ukur</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>';
                                    }

                                    if ($_SESSION["roleId"] == 3) {
                                        echo '<div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/surveys/priority/finance") . '" href="/surveys/priority/finance" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permohonan Mula Ukur</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>';
                                    }

                                    if (($_SESSION["roleId"] == 14) || ($_SESSION["roleId"] == 13) || ($_SESSION["roleId"] == 12) || ($_SESSION["roleId"] == 20) || ($_SESSION["roleId"] == 19)) {
                                        echo '
                                        <div class="menu-item">
                                            <!--begin:Menu link-->
                                            <a class="menu-link ' . General::isMenuActive("/projects/wayleave/wyList") . '" href="/projects/wayleave/wyList" ">
                                                <span class="menu-bullet">
                                                    <span class="bullet bullet-dot"></span>
                                                </span>
                                                <span class="menu-title">Senarai Maklum Balas Izin Lalu</span>
                                            </a>
                                            <!--end:Menu link-->
                                        </div>';
                                    }

                                    if (($_SESSION["roleId"] == 1) || ($_SESSION["roleId"] == 2) || ($_SESSION["roleId"] == 12) || ($_SESSION["roleId"] == 13) || ($_SESSION["roleId"] == 15) || ($_SESSION["roleId"] == 16) || ($_SESSION["roleId"] == 17) || ($_SESSION["roleId"] == 18) || ($_SESSION["roleId"] == 19) || ($_SESSION["roleId"] == 20)) {
                                        echo '<div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/projects/active/permit") . '" href="/projects/active/permit" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permit Aktif</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>';
                                    echo '<div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link ' . General::isMenuActive("/projects/active/liability") . '" href="/projects/active/liability" ">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tanggungan Kecacatan Aktif</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>';
                                    }

                                    ?>
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="fad fa-chart-mixed fs-4"></i>
                                    </span>
                                    <span class="menu-title">Penjejak</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="/tracker/overall/application">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Permohonan</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <!-- <div class="menu-item">
                                        <a class="menu-link" href="/tracker/entry">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permohonan</span>
                                        </a>
                                    </div> -->
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item" hidden>
                                        <!-- FIXME - temporarily hidden until fully configured -->
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="/tracker/overall/v1">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Penjejak Keseluruhan</span>
                                            <span class="badge badge-light-primary">V1</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="/tracker/overall/v2">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Penjejak Keseluruhan</span>
                                            <!-- <span class="badge badge-light-primary">V2</span> -->
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="/list/status/client/statusClient">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Status Pending Pemohon</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <!-- <div class="menu-item">
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Daerah</span>
                                        </a>
                                    </div> -->
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <!-- <div class="menu-item">
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Kod Jarak</span>
                                        </a>
                                    </div> -->
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link <?php echo General::isMenuActive("/workflow/status/"); ?>"
                                    href="/calendar">
                                    <i class="menu-icon fad fa-calendars fs-4 "></i>
                                    <span class="menu-title">Kalendar</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <!-- <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="fad fa-chart-pie fs-4"></i>
                                    </span>
                                    <span class="menu-title">Rumusan</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion">
                                    <div class="menu-item">
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Status</span>
                                        </a>
                                    </div>
                                    <div class="menu-item">
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Pencapaian Kerja</span>
                                        </a>
                                    </div>
                                </div>
                            </div> -->
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item pt-5">
                                <!--begin:Menu content-->
                                <div class="menu-content">
                                    <span class="menu-heading fw-bold text-uppercase fs-7">Aliran Kerja</span>
                                </div>
                                <!--end:Menu content-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link <?php echo General::isMenuActive("/workflow/status/"); ?>"
                                    href="/dashboard/">
                                    <i class="menu-icon fad fa-code-pull-request fs-4 "></i>
                                    <span class="menu-title">Status Permohonan</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <?php if (($_SESSION["roleId"] == 28) || ($_SESSION["roleId"] == 24) || ($_SESSION["roleId"] == 23)) {
                                echo '
                                <!--begin:Menu item-->
                                <div class="menu-item pt-5">
                                    <!--begin:Menu content-->
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">Ukur & Pelan</span>
                                    </div>
                                    <!--end:Menu content-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link ' . General::isMenuActive("/workflow/status/") . '" href="surveys/team">
                                        <i class="menu-icon fad fa-people-group fs-4 "></i>
                                        <span class="menu-title">Pengurusan Kumpulan Ukur</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
				<div class="menu-item">
  				      <!--begin:Menu link-->
  				      <a class="menu-link ' . General::isMenuActive("/workflow/status/") . '" href="surveys/reportlist">
      					<i class="menu-icon fad fa-envelope-circle-check fs-4 "></i>
      					<span class="menu-title">Senarai Laporan Ukur</span>
  				     </a>
  				<!--end:Menu link-->
				</div>
                                ';
                            }

                            ?>
                            <!--end:Menu item-->
                            <!--end:Menu item-->
                            <?php if (($_SESSION["roleId"] == 18)) {
                                echo '
                                <!--begin:Menu item-->
                                <div class="menu-item pt-5">
                                    <!--begin:Menu content-->
                                    <div class="menu-content">
                                        <span class="menu-heading fw-bold text-uppercase fs-7">PPKD</span>
                                    </div>
                                    <!--end:Menu content-->
                                </div>
                                <!--end:Menu item-->
                                <!--begin:Menu item-->
                                <div class="menu-item">
                                    <!--begin:Menu link-->
                                    <a class="menu-link ' . General::isMenuActive("/workflow/status/") . '" href="projects/team">
                                        <i class="menu-icon fad fa-people-group fs-4 "></i>
                                        <span class="menu-title">Pengurusan Kumpulan PPKD</span>
                                    </a>
                                    <!--end:Menu link-->
                                </div>
                                ';
                            }

                            ?>
                            <!--end:Menu item-->
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Tab pane-->
                <!--begin::Tab pane-->
                <div class="tab-pane fade" id="tab_map" role="tabpanel">
                    <!--begin::Map Selection-->
                    <div class="mx-2 my-6">
                        <a href="#" class="card hover-elevate-up shadow-sm parent-hover">
                            <div class="">
                                <img class="mw-100 card-rounded-top m-0 p-0" alt=""
                                    src="assets/media/misc/application-map.png" />
                            </div>
                            <div class="py-3 px-6">
                                <h6 class="text-gray-700 parent-hover-primary fs-6 fw-bold">Peta Permohonan</h6>
                            </div>
                        </a>
                    </div>
                    <!--end::Map Seelction-->
                    <!--begin::Map Selection-->
                    <div class="mx-2 my-6">
                        <a href="#" class="card hover-elevate-up shadow-sm parent-hover">
                            <div class="">
                                <img class="mw-100 card-rounded-top m-0 p-0" alt=""
                                    src="assets/media/misc/utility-map.png" />
                            </div>
                            <div class="py-3 px-6">
                                <h6 class="text-gray-700 parent-hover-primary fs-6 fw-bold">Peta Laluan Utiliti</h6>
                            </div>
                        </a>
                    </div>
                    <!--end::Map Seelction-->
                </div>
                <!--end::Tab pane-->
                <!--begin::Tab pane-->
                <div class="tab-pane fade" id="kt_aside_nav_tab_tasks" role="tabpanel">
                    <!--begin::Menu-->
                    <div class="menu menu-column menu-fit menu-rounded menu-title-gray-600 menu-icon-gray-400 menu-state-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500 fw-semibold fs-5 px-6 my-5 my-lg-0"
                        id="kt_aside_menu" data-kt-menu="true">
                        <div id="kt_aside_menu_wrapper" class="menu-fit">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link <?php echo General::isMenuActive("/dashboard/"); ?>"
                                    href="/dashboard/">
                                    <i class="menu-icon fad fa-megaphone fs-4"></i>
                                    <span class="menu-title">Pengumuman</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item pt-5">
                                <!--begin:Menu content-->
                                <div class="menu-content">
                                    <span class="menu-heading fw-bold text-uppercase fs-7">Kakitangan</span>
                                </div>
                                <!--end:Menu content-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="fad fa-clipboard-user fs-4"></i>
                                    </span>
                                    <span class="menu-title">Kehadiran</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Log Harian</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Kehadiran Semasa</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Kelewatan</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Rumusan Kehadiran</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="fad fa-money-from-bracket fs-4"></i>
                                    </span>
                                    <span class="menu-title">Pendapatan</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Senarai Gaji</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Kerja Lebih Masa</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Tuntutan</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-icon">
                                        <i class="fad fa-calendar-days fs-4"></i>
                                    </span>
                                    <span class="menu-title">Kalendar</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <div class="menu-sub menu-sub-accordion">
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Permohonan Cuti</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div class="menu-item">
                                        <!--begin:Menu link-->
                                        <a class="menu-link" href="javascript:void()">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">Baki Cuti</span>
                                        </a>
                                        <!--end:Menu link-->
                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link <?php echo General::isMenuActive("/workflow/status/"); ?>"
                                    href="/dashboard/">
                                    <i class="menu-icon fad fa-sitemap fs-4 "></i>
                                    <span class="menu-title">Carta Organisasi</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Tab pane-->
            </div>
            <!--end::Tab content-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Footer-->
        <div class="flex-column-auto pt-10 px-5" id="kt_aside_secondary_footer">
            <a href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/v1/manual"
                class="btn btn-bg-light btn-color-gray-600 btn-flex btn-active-color-primary flex-center w-100"
                data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-trigger="hover"
                data-bs-offset="0,5" data-bs-dismiss-="click" title="Panduan Pengguna Mengenai Sistem">
                <i class="fad fa-book-bookmark"></i>
                <span class="btn-label me-2">Manual Sistem </span>

            </a>
        </div>
        <!--end::Footer-->
    </div>
</div>
<!--end::Workspace-->