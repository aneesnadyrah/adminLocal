<!--begin::Slider Widget 2-->
<div id="kt_sliders_widget_2_slider" class="card card-flush carousel carousel-custom card-stretch slide mb-10" data-bs-ride="carousel" data-bs-interval="5500">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <h4 class="card-title d-flex align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Aktiviti Hari Ini</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Semak aktiviti staf</span>
        </h4>
        <!--end::Title-->
        <!--begin::Toolbar-->
        <div class="card-toolbar">
            <!--begin::Carousel Indicators-->
            <div class="d-flex justify-content-end">
                <a href="#kt_sliders_widget_2_slider" class="carousel-control-prev position-relative me-5 active" role="button" data-bs-slide="prev">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen058.svg-->
                    <span class="svg-icon svg-icon-2x svg-icon-gray-400">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                            <path d="M12.0657 12.5657L14.463 14.963C14.7733 15.2733 14.8151 15.7619 14.5621 16.1204C14.2384 16.5789 13.5789 16.6334 13.1844 16.2342L9.69464 12.7029C9.30968 12.3134 9.30968 11.6866 9.69464 11.2971L13.1844 7.76582C13.5789 7.3666 14.2384 7.42107 14.5621 7.87962C14.8151 8.23809 14.7733 8.72669 14.463 9.03696L12.0657 11.4343C11.7533 11.7467 11.7533 12.2533 12.0657 12.5657Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </a>
                <a href="#kt_sliders_widget_2_slider" class="carousel-control-next position-relative me-5" role="button" data-bs-slide="next">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen057.svg-->
                    <span class="svg-icon svg-icon-2x svg-icon-gray-400">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                            <path d="M11.9343 12.5657L9.53696 14.963C9.22669 15.2733 9.18488 15.7619 9.43792 16.1204C9.7616 16.5789 10.4211 16.6334 10.8156 16.2342L14.3054 12.7029C14.6903 12.3134 14.6903 11.6866 14.3054 11.2971L10.8156 7.76582C10.4211 7.3666 9.7616 7.42107 9.43792 7.87962C9.18488 8.23809 9.22669 8.72669 9.53696 9.03696L11.9343 11.4343C12.2467 11.7467 12.2467 12.2533 11.9343 12.5657Z" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </a>
                <!-- <a href="surveys/team" style="display: inline-flex; width: 24px; height: 24px; justify-content: center; align-items: center;" class="btn btn-sm btn-light btn-light-primary me-1" > <i class="fas fa-plus"></i>
				</a> -->
            </div>
            <!--end::Carousel Indicators-->
            <!-- <div class="card-toolbar"> -->
			
		<!-- </div> -->
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-10">
        <!--begin::Carousel-->
        <div class="carousel-inner">
            <!-- Iterate through staff overview data and output it in the carousel -->
            <?php
            $staffOverviewData = Survey::getStaffOverview();
            if (!empty($staffOverviewData)) {
                $firstItem = true; // Initialize a variable to track the first item

                foreach ($staffOverviewData as $teamData) {
                    // Set the class based on the $firstItem variable
                    $class = ($firstItem) ? 'carousel-item active show' : 'carousel-item';
                    if ($firstItem) {
                        $firstItem = false; // Update $firstItem after the first item is processed
                    }
                    ?>
            <!--begin::Item-->
            <div class="<?= $class ?>">
                <!--begin::Wrapper-->
                <div class="d-flex align-items-center mb-10">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-70px symbol-circle me-5">
                        <img src="assets/media/avatars/<?= $teamData['profile_picture'] ?>.jpg" class="" alt="" />
                    </div>
                    <!--end::Symbol-->
                    <!--begin::Info-->
                    <div class="m-0">
                        <!--begin::Subtitle-->
                        <h4 class="fw-bold text-gray-800 mb-4">Kumpulan <?= $teamData['team_name'] ?></h4>
                        <!--end::Subtitle-->

                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Wrapper-->
                <!--begin::Item-->
                <div class="d-flex flex-stack ">
                    <!--begin::Symbol-->
                    <span class="symbol symbol-20px me-5">
                        <span class="symbol-label bg-light-dark">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2 svg-icon-gray">
                            <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <!--end::Symbol-->
                    <!--begin::Section-->
                    <div class="d-flex align-items-start flex-row-fluid flex-wrap">
                        <!--begin:Author-->
                        <div class="flex-grow-1 me-2">
                            <span class="text-gray-600 fs-6 fw-semibold">Masa Daftar Masuk</span>
                            <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                        </div>
                        <!--end:Author-->
                        <!--begin::Actions-->
                        <div class="flex-grow-1 me-2">
                            <span class="d-flex align-items-end ms-10"><?= $teamData['masa_masuk'] ?></span>
                        </div>
                        <!--begin::Actions-->
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Item-->
                <!--begin::Separator-->
                <div class="separator separator-dashed my-5"></div>
                <!--end::Separator-->
                <!--begin::Item-->
                <div class="d-flex flex-stack ">
                    <!--begin::Symbol-->
                    <span class="symbol symbol-20px me-5">
                        <span class="symbol-label bg-light-dark">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2 svg-icon-gray">
                            <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <!--end::Symbol-->
                    <!--begin::Section-->
                    <div class="d-flex align-items-start flex-row-fluid flex-wrap">
                        <!--begin:Author-->
                        <div class="flex-grow-1 me-2">
                            <span class="text-gray-600 fs-6 fw-semibold">Masa Daftar Keluar</span>
                            <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                        </div>
                        <!--end:Author-->
                        <!--begin::Actions-->
                        <div class="flex-grow-1 me-2">
                            <span class="d-flex align-items-end ms-10"><?= $teamData['masa_keluar'] ?></span>
                        </div>
                        <!--begin::Actions-->
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Item-->
                <!--begin::Separator-->
                <div class="separator separator-dashed my-5"></div>
                <!--end::Separator-->
                <!--begin::Item-->
                <div class="d-flex flex-stack ">
                    <!--begin::Symbol-->
                    <span class="symbol symbol-20px me-5">
                        <span class="symbol-label bg-light-dark">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2 svg-icon-gray">
                            <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <!--end::Symbol-->
                    <!--begin::Section-->
                    <div class="d-flex align-items-end flex-row-fluid flex-wrap">
                        <!--begin:Author-->
                        <div class="flex-grow-1 me-2">
                            <span class="text-gray-600 fs-6 fw-semibold">Bilangan Ahli</span>
                            <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                        </div>
                        <!--end:Author-->
                        <!--begin::Actions-->
                        <div class="flex-grow-1 me-2">
                            <span class="d-flex fw-bold align-items-end ms-15"><?= $teamData['total_members'] ?></span>
                        </div>
                        <!--begin::Actions-->
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Item-->
                <!--begin::Separator-->
                <div class="separator separator-dashed my-5"></div>
                <!--end::Separator-->
                <!--begin::Item-->
                <div class="d-flex flex-stack ">
                    <!--begin::Symbol-->
                    <span class="symbol symbol-20px me-5">
                        <span class="symbol-label bg-light-dark">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2 svg-icon-gray">
                            <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <!--end::Symbol-->
                    <!--begin::Section-->
                    <div class="d-flex flex-row-fluid flex-wrap">
                        <!--begin:Author-->
                        <div class="flex-grow-1 me-10">
                            <span class="text-gray-600 fs-6 fw-semibold">Status</span>
                            <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                        </div>
                        <!--end:Author-->
                        <!--begin::Actions-->
                        <div class="flex-grow-1 me-0">
                            <?php
                                $badgeClass = '';
                                switch ($teamData['status']) {
                                    case 'Di tapak':
                                        $badgeClass = 'badge-light-primary';
                                        break;
                                    case 'Telah Dilantik':
                                        $badgeClass = 'badge-light-success';
                                        break;
                                    case 'Tidak dilantik':
                                        $badgeClass = 'badge-light-danger';
                                        break;
                                    case 'Di pejabat':
                                        $badgeClass = 'badge-light-info';
                                        break;
                                    default:
                                        $badgeClass = 'badge-light-secondary'; // You can set a default color for unknown statuses
                                        break;
                                }
                            ?>
                            <span class="badge <?= $badgeClass ?> align-items-end ms-20"><?= $teamData['status'] ?></span>
                        </div>
                        <!--begin::Actions-->
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Item-->
            </div>
            <!--end::Item-->
             <?php } ?>
             <?php } 
              else {
                echo'
                <!--begin::Empty-->
                <div class="text-center pt-3 mb-9">
                    <!--begin::Message-->
                    <div class="fw-semibold">
                        <div class="text-gray-600 fs-3 mb-2">Tiada Permohonan</div>
                        <div class="text-muted fs-6 mb-2">Sila isi butiran ini untuk paparkan data...</div>
                    </div>
                    <!--end::Message-->
                    <!--begin::Illustration-->
                    <div class="text-center px-5">
                        <img src="assets/media/illustrations/empty/06.svg" alt="" class="w-50 h-50px" />
                    </div>
                    <!--end::Illustration-->
                </div>
                <!--end::Empty-->';
            }
            ?>
        </div>
        <!--end::Carousel-->
    </div>
    <!--end::Body-->
</div>
<!--end::Slider Widget 2-->