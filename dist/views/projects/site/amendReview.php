<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/tenant.php";
include "config/autoload.php";

$e = ApplicationEntry::viewEntry($systemId, 2)[0];
$r = ApplicationEntry::viewRoad($systemId, '1');
$s = ApplicationEntry::viewStatus($systemId)[0];
$c = ApplicationEntry::contactDetails($systemId);
$detail = ProjectDetails::projectDetails($systemId, 1, '')[0];
$docs = ApplicationEntry::getDocs($systemId);
$costs = number_format(isset($e['project_costs']) ? $e['project_costs'] : 0, 2, '.', ',');
?>

<!DOCTYPE html>
<html lang="ms">

<!--begin::Head-->

<head>
    <base href="../../../../" />
    <title>
        <?php echo $appsTitle ?>
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/<?php echo $appsTitle ?>-small.svg" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <style>
        /* Default style for the card */
        .sticky-header {
            width: 82.6vw;
            /* Set default width to 82.6% of the viewport width */
        }

        /* Media query for larger screens (e.g., xl) */
        @media (min-width: 1600px) {
            .sticky-header {
                width: 100%;
                /* Set width to 80% of the viewport width for screens wider than 1200px */
            }
        }

        /* Media query for larger screens (e.g., xl) */
        @media (min-width: 1200px) {
            .sticky-header {
                width: 100%;
                /* Set width to 80% of the viewport width for screens wider than 1200px */
            }
        }

        /* Media query for smaller screens (e.g., xs, sm, md) */
        @media (max-width: 992px) {
            .sticky-header {
                width: 100%;
                /* Set width to 90% of the viewport width for screens up to 992px wide */
            }
        }

        /* Add more media queries as needed for other screen sizes */
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on"
    class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
    <!--begin::loader-->
    <div class="page-loader flex-column">
        <img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?php echo $appsTitle ?>-default.svg" />
        <img alt="Logo" class="theme-dark-show h-40px" src="assets/media/logos/<?php echo $appsTitle ?>-dark.svg" />
        <div class="d-flex align-items-center mt-5">
            <span class="spinner-border text-primary" role="status"></span>
        </div>
    </div>
    <!--end::Loader-->
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <script>
        // Function to set the sticky width based on screen size
        function setStickyWidth() {
            const card = document.querySelector(".sticky-header");
            const screenWidth = window.innerWidth;

            // Define your desired breakpoints and corresponding widths here
            if (screenWidth >= 1600) {
                card.setAttribute("data-kt-sticky-width", "{default:'80%'}");
            } else if (screenWidth >= 1200) {
                card.setAttribute("data-kt-sticky-width", "{default:'50%'}");
            } else if (screenWidth >= 992) {
                card.setAttribute("data-kt-sticky-width", "{default:'100%'}");
            } else {
                card.setAttribute("data-kt-sticky-width", "{default:'50%'}");
            }
        }

        // Call the function on page load and whenever the window is resized
        window.addEventListener("load", setStickyWidth);
        window.addEventListener("resize", setStickyWidth);
    </script>

    <!--end::Theme mode setup on page load-->
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Aside-->
            <?php include "components/layouts/sidebar/mainLayout.php" ?>
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Header-->
                <?php include "components/layouts/header.php" ?>
                <!--end::Header-->

                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Container-->
                    <div class="container" id="kt_content_container">
                        <div id="hidden_key" class="w-100"></div>
                        <!--begin::Card-->
                        <div class="card card-flush mb-5 mb-xxl-8" data-kt-sticky="true"
                            data-kt-sticky-name="sticky-summary" data-kt-sticky-width="{target: '#hidden_key'}"
                            data-kt-sticky-offset="{default: false, xl: '200px'}" data-kt-sticky-top="100px"
                            data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
                            <div class="card-header">
                                <div class="d-flex flex-stack">
                                    <div class="symbol symbol-100px me-5">
                                        <img src="assets/media/provider/<?php echo $e['utility_provider'] ? $e['utility_provider'] : 99 ?>.webp"
                                            alt="image" />
                                    </div>
                                    <h3 class="card-title d-none d-md-block">
                                        <?php echo $e['name'] ? $e['name'] : 'Tidak Diketahui' ?>
                                    </h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex flex-stack">
                                        <?php

                                            //Array for this Nested
                                            $colors = ['light-dark', 'dark'];
                                            $modals = ['#modal-amend-sv-review', '#modal-approve-sv-review'];
                                            $icons = ['fa-file-circle-xmark', 'fa-file-circle-check'];
                                            $items = ['Pinda', 'Sahkan'];
                                        
                                            $array = array_map(function ($color, $modal, $icon, $item) {
                                                return ['color' => $color, 'modal' => $modal, 'icon' => $icon, 'item' => $item];
                                            }, $colors, $modals, $icons, $items);
                                        
                                            foreach ($array as $button) {
                                                echo <<<Action
                                                <button type="button" class="btn btn-flex flex-center btn-{$button['color']} me-2" data-bs-toggle="modal" data-bs-target="{$button['modal']}">
                                                    <i class="fad {$button['icon']} fs-4"></i>
                                                    <span class="d-none d-md-inline">{$button['item']}</span>
                                                </button>
                                                Action;
                                            }                                     
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--begin::Card-->
                        <div class="card card-flush pt-3 mb-5 mb-xl-10">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="text-justify text-gray-900 fs-4">
                                        <?php echo $e['project_title'] ? $e['project_title'] : '-'; ?>
                                    </h3>
                                </div>
                            </div>
                            <!--begin::Card body-->
                            <div class="card-body pt-3">
                                <!--begin::Title-->
                                <h5 class="mb-4">Maklumat Permohonan :</h5>
                                <!--end::Title-->
                                <!--begin::Details-->
                                <div class="d-flex flex-wrap py-5">
                                    <!--begin::Row-->
                                    <div class="flex-equal me-5">
                                        <?php
                                        $jenis = '';

                                        if ($e['type_application'] == 'BF') {
                                            $jenis = '<span class="badge badge-light-primary me-auto">Brownfield</span>';
                                        } elseif ($e['type_application'] == 'GF') {
                                            $jenis = '<span class="badge badge-light-success me-auto">Greenfield</span>';
                                        } elseif ($e['type_application'] == 'EG') {
                                            $jenis = '<span class="badge badge-light-danger me-auto">Kecemasan</span>';
                                        }
                                        ?>
                                        <!--begin::Details-->
                                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                            <tbody>
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Nama Tapak A :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo $e['site_start'] ? $e['site_start'] : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Jenis Permohonan :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo ($jenis) ? $jenis : '-'; ?>
                                                    </td>

                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Kos Projek :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo 'RM ' . number_format($costs, 2, '.', ',') ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Link ID / No Projek :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo $e['link_id'] ? $e['link_id'] : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Status Permohonan :</td>
                                                    <td class="text-gray-800">
                                                        <span
                                                            class="badge badge-light-<?php echo $detail['status_color'] ?> me-auto"><?php echo $detail['project_status'] ?></span>
                                                    </td>

                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Jarak Permohonan :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo $e['application_length'] ? $e['application_length'] . 'm' : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                            </tbody>
                                        </table>
                                        <!--end::Details-->
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Row-->
                                    <div class="flex-equal">
                                        <?php
                                        $kategori = '';

                                        if ($e['application_code'] === 'KT') {
                                            $kategori = '<span class="badge badge-light-info me-auto">Kerja Terancang</span>';
                                        } elseif ($e['application_code'] === 'KP') {
                                            $kategori = '<span class="badge badge-light-warning me-auto">Kerja Pengalihan</span>';
                                        }
                                        ?>
                                        <!--begin::Details-->
                                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                            <tbody>
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Nama Tapak B :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo $e['site_end'] ? $e['site_end'] : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Kategori Permohonan :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo ($kategori) ? $kategori : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Tag Permohonan :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo $e['tags'] ? $e['tags'] : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">No Rujukan :</td>
                                                    <td class="text-gray-800">
                                                        <?php echo $e['reference_no'] ? $e['reference_no'] : '-'; ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Daerah Terlibat :</td>
                                                    <td class="text-gray-800">
                                                        <?php
                                                        $dist = explode(",", $detail['district']);
                                                        $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                                        shuffle($colors);
                                                        foreach ($dist as $row) {
                                                            echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                                        }
                                                        if (empty($detail['district'])) {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                                <!--begin::Row-->
                                                <tr>
                                                    <td class="text-gray-500">Tarikh Permohonan :</td>
                                                    <td class="text-gray-800">
                                                        <span>
                                                            <?php echo General::convertDate($e['application_date']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <!--end::Row-->
                                            </tbody>
                                        </table>
                                        <!--end::Details-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::permohonan-->
                        <div class="card card-flush pt-3 mb-5 mb-xl-10">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Butiran Pegawai</h2>
                                </div>
                                <!--begin::Card title-->
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-3">
                                <!--begin::Section Pegawai-->
                                <div class="mb-0">
                                    <!--begin::Product table-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr
                                                    class="border-bottom border-gray-200 text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="">Nama</th>
                                                    <th class="">Email</th>
                                                    <th class="">No Telefon</th>
                                                    <th class="">Nama Syarikat</th>
                                                    <th class="">Alamat</th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="fw-semibold text-gray-800">
                                                <?php
                                                foreach ($c as $row) {
                                                    $template = <<<HTML
                                                        <tr>
                                                            <td>
                                                                <label class="w-150px">$row[FullName]</label>
                                                                <div class="fw-normal text-gray-600">$row[Position]</div>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-light-danger">$row[Email]</span>
                                                            </td>
                                                            <td>$row[PhoneNo]</td>
                                                            <td>$row[CompanyName]</td>
                                                            <td>
                                                            $row[Address1], 
                                                            $row[Address2], <br/>
                                                            $row[Postcode], 
                                                            $row[City], 
                                                            $row[State]
                                                            </td>
                                                        </tr>
                                                        HTML;

                                                    if (!empty($row['FullName'])) {
                                                        echo $template;
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Product table-->
                                </div>
                                <!--end::Section Pegawai-->
                            </div>
                            <!--end::pegawai-->
                        </div>

                        <!--begin::Jalan-->
                        <div class="card card-flush pt-3 mb-5 mb-xl-10">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Butiran Jalan</h2>
                                </div>
                                <!--begin::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-3">
                                <!--begin::Section-->
                                <div class="mb-0">
                                    <!--begin::Title-->
                                    <h5 class="mb-4">Maklumat Jalan Terlibat :</h5>
                                    <!--end::Title-->
                                    <!--begin::Product table-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr
                                                    class="border-bottom border-gray-200 text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-150px">Nama Jalan</th>
                                                    <th class="min-w-125px">Kaedah Jalan</th>
                                                    <th class="min-w-125px">Jarak Jalan (m)</th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="fw-semibold text-gray-800">
                                                <?php
                                                foreach ($r as $road) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <label class="w-150px">
                                                                <?php echo $road['road_name']; ?>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $method = explode(",", $road['methods']);
                                                            $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                                            shuffle($colors);
                                                            foreach ($method as $row) {
                                                                echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $road['road_length']; ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <!--end::Table body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Product table-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::jalan-->
                        </div>
                        <!--end::Layout-->

                        <!--begin::Card Jalan-->
                        <div class="card card-flush pt-3 mb-5 mb-xl-10">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Lampiran</h2>
                                </div>
                                <!--begin::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-3">
                                <!--begin::Section-->
                                <div class="mb-0">
                                    <!--begin::Table-->
                                    <table id="kt_file_manager_list" data-kt-filemanager-table="files"
                                        class="table align-middle table-row-dashed fs-6 gy-5">
                                        <?php
                                        if (empty($docs)) {
                                            ?>
                                            <div class="d-flex flex-column flex-center">
                                                <!-- <img src="assets/media/files/pdf.svg" class="mw-400px" /> -->
                                                <img src="assets/media/illustrations/sketchy-1/5.png" class="mw-250px" />
                                                <div class="fs-2 fw-bolder text-dark">Tiada dokumen ditemui.</div>
                                                <div class="fs-6">Sila semak semula butiran bagi permohonan ini.</div>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <!--begin::Table head-->
                                            <thead>
                                                <!--begin::Table row-->
                                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="min-w-250px">Nama</th>
                                                    <th class="min-w-10px">Saiz</th>
                                                    <th class="min-w-125px">Tarikh Diubahsuai</th>
                                                    <th class="w-125px"></th>
                                                </tr>
                                                <!--end::Table row-->
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="fw-semibold text-gray-600">
                                                <?php
                                                if (!empty($docs[0])) {
                                                    ?>
                                                    <tr>
                                                        <!--begin::Name=-->
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Image-->
                                                                <div class="symbol symbol-35px me-5">
                                                                    <img src="assets/media/files/pdf.svg"
                                                                        class="theme-light-show" alt="" />
                                                                    <img src="assets/media/files/pdf-dark.svg"
                                                                        class="theme-dark-show" alt="" />
                                                                </div>
                                                                <!--end::Image-->
                                                                <span class="text-gray-800">
                                                                    <?php echo $docs[0]['details'] ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <!--end::Name=-->
                                                        <!--begin::Size-->
                                                        <td>
                                                            <?php
                                                            $bytes = $docs[0]['size'];

                                                            if ($bytes >= 1024 * 1024) {
                                                                $size = round($bytes / (1024 * 1024), 2) . ' MB';
                                                            } else {
                                                                $size = round($bytes / 1024, 2) . ' KB';
                                                            }

                                                            echo $size;
                                                            ?>
                                                        </td>
                                                        <!--end::Size-->
                                                        <!--begin::Last modified-->
                                                        <td>
                                                            <?php echo General::convertDate($docs[0]['attachment_date']) ?>
                                                        </td>
                                                        <!--end::Last modified-->
                                                        <!--begin::Actions-->
                                                        <td class="text-end">
                                                            <a href="#pdf-bkil" data-fslightbox="lightbox"
                                                                data-class="fslightbox-source"
                                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                                <span class="fad fa-eye svg-icon svg-icon-5 m-0"></span>
                                                            </a>
                                                        </td>
                                                        <!--end::Actions-->
                                                    </tr>

                                                    <?php
                                                }

                                                if (!empty($docs[1])) {
                                                    ?>
                                                    <tr>
                                                        <!--begin::Name=-->
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Image-->
                                                                <div class="symbol symbol-35px me-5">
                                                                    <img src="assets/media/files/pdf.svg"
                                                                        class="theme-light-show" alt="" />
                                                                    <img src="assets/media/files/pdf-dark.svg"
                                                                        class="theme-dark-show" alt="" />
                                                                </div>
                                                                <!--end::Image-->
                                                                <span class="text-gray-800">
                                                                    <?php echo $docs[1]['details'] ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <!--end::Name=-->
                                                        <!--begin::Size-->
                                                        <td>
                                                            <?php
                                                            $bytes = $docs[1]['size'];

                                                            if ($bytes >= 1024 * 1024) {
                                                                $size = round($bytes / (1024 * 1024), 2) . ' MB';
                                                            } else {
                                                                $size = round($bytes / 1024, 2) . ' KB';
                                                            }

                                                            echo $size;
                                                            ?>
                                                        </td>
                                                        <!--end::Size-->
                                                        <!--begin::Last modified-->
                                                        <td>
                                                            <?php echo General::convertDate($docs[1]['attachment_date']) ?>
                                                        </td>
                                                        <!--end::Last modified-->
                                                        <!--begin::Actions-->
                                                        <td class="text-end">
                                                            <a href="#pdf-perakuan" data-fslightbox="lightbox"
                                                                data-class="fslightbox-source"
                                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                                <span class="fad fa-eye svg-icon svg-icon-5 m-0"></span>
                                                            </a>
                                                        </td>
                                                        <!--end::Actions-->
                                                    </tr>
                                                    <?php
                                                }

                                                if (!empty($docs[2])) {
                                                    ?>
                                                    <tr>
                                                        <!--begin::Name=-->
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Image-->
                                                                <div class="symbol symbol-35px me-5">
                                                                    <img src="assets/media/files/pdf.svg"
                                                                        class="theme-light-show" alt="" />
                                                                    <img src="assets/media/files/pdf-dark.svg"
                                                                        class="theme-dark-show" alt="" />
                                                                </div>
                                                                <!--end::Image-->
                                                                <span class="text-gray-800">
                                                                    <?php echo $docs[2]['details'] ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <!--end::Name=-->
                                                        <!--begin::Size-->
                                                        <td>
                                                            <?php
                                                            $bytes = $docs[2]['size'];

                                                            if ($bytes >= 1024 * 1024) {
                                                                $size = round($bytes / (1024 * 1024), 2) . ' MB';
                                                            } else {
                                                                $size = round($bytes / 1024, 2) . ' KB';
                                                            }

                                                            echo $size;
                                                            // echo $docs[2]['size'];
                                                            ?>
                                                        </td>
                                                        <!--end::Size-->
                                                        <!--begin::Last modified-->
                                                        <td>
                                                            <?php echo General::convertDate($docs[2]['attachment_date']) ?>
                                                        </td>
                                                        <!--end::Last modified-->
                                                        <!--begin::Actions-->
                                                        <td class="text-end">
                                                            <a href="#pdf-pelan-teknikal" data-fslightbox="lightbox"
                                                                data-class="fslightbox-source"
                                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                                <span class="fad fa-eye svg-icon svg-icon-5 m-0"></span>
                                                            </a>
                                                        </td>
                                                        <!--end::Actions-->
                                                    </tr>
                                                    <?php
                                                }

                                                if (!empty($docs[3])) {
                                                    ?>
                                                    <tr>
                                                        <!--begin::Name=-->
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Image-->
                                                                <div class="symbol symbol-35px me-5">
                                                                    <img src="assets/media/files/pdf.svg"
                                                                        class="theme-light-show" alt="" />
                                                                    <img src="assets/media/files/pdf-dark.svg"
                                                                        class="theme-dark-show" alt="" />
                                                                </div>
                                                                <!--end::Image-->
                                                                <span class="text-gray-800">
                                                                    <?php echo $docs[3]['details'] ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <!--end::Name=-->
                                                        <!--begin::Size-->
                                                        <td>
                                                            <?php
                                                            $bytes = $docs[3]['size'];

                                                            if ($bytes >= 1024 * 1024) {
                                                                $size = round($bytes / (1024 * 1024), 2) . ' MB';
                                                            } else {
                                                                $size = round($bytes / 1024, 2) . ' KB';
                                                            }

                                                            echo $size;
                                                            // echo $docs[3]['size'];
                                                            ?>
                                                        </td>
                                                        <!--end::Size-->
                                                        <!--begin::Last modified-->
                                                        <td>
                                                            <?php echo General::convertDate($docs[3]['attachment_date']) ?>
                                                        </td>
                                                        <!--end::Last modified-->
                                                        <!--begin::Actions-->
                                                        <td class="text-end">
                                                            <a href="#pdf-gambar-lokasi" data-fslightbox="lightbox"
                                                                data-class="fslightbox-source"
                                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                                <span class="fad fa-eye svg-icon svg-icon-5 m-0"></span>
                                                            </a>
                                                        </td>
                                                        <!--end::Actions-->
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                            <!--end::Table body-->
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Section-->

                                <!--begin::file-->
                                <div style="display: none;">
                                    <div id="pdf-bkil">
                                        <iframe class="scroll h-700px w-900px px-5"
                                            src="/components/partials/widgets/print.php?f=<?php echo $docs[0]['url'] ?>&t=<?php echo $docs[0]['attachment_type'] ?> ">
                                        </iframe>
                                    </div>
                                </div>
                                <!--end::file-->

                                <!--begin::file-->
                                <div style="display: none;">
                                    <div id="pdf-perakuan">
                                        <iframe class="scroll h-700px w-900px px-5"
                                            src="/components/partials/widgets/print.php?f=<?php echo $docs[1]['url'] ?>&t=<?php echo $docs[1]['attachment_type'] ?> ">
                                        </iframe>
                                    </div>
                                </div>
                                <!--end::file-->

                                <!--begin::file-->
                                <div style="display: none;">
                                    <div id="pdf-pelan-teknikal">
                                        <iframe class="scroll h-700px w-900px px-5"
                                            src="/components/partials/widgets/print.php?f=<?php echo $docs[2]['url'] ?>&t=<?php echo $docs[2]['attachment_type'] ?> ">
                                        </iframe>
                                    </div>
                                </div>
                                <!--end::file-->

                                <!--begin::file-->
                                <div style="display: none;">
                                    <div id="pdf-gambar-lokasi">
                                        <iframe class="scroll h-700px w-900px px-5"
                                            src="/components/partials/widgets/print.php?f=<?php echo $docs[3]['url'] ?>&t=<?php echo $docs[3]['attachment_type'] ?> ">
                                        </iframe>
                                    </div>
                                </div>
                                <!--end::file-->
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card Jalan-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Container-->
        </div>
        <!--begin::Footer-->
        <?php include "components/layouts/footer.php"; ?>
        <!--end::Footer-->
    </div>
    </div>
    <!--end::Wrapper-->
    </div>
    <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
    <!--begin::Scrolltop-->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
        <span class="svg-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)"
                    fill="currentColor" />
                <path
                    d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                    fill="currentColor" />
            </svg>
        </span>
        <!--end::Svg Icon-->
    </div>
    <!--end::Scrolltop-->
    <!--begin::Modals-->
    <?php
    foreach ($taskModals as $modal) {
        include $modal;
    }

    foreach ($generalModals as $general) {
        include $general;
    }
    ?>
    <!--end::Modals-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/widgets.bundle.js"></script>
    <script src="assets/js/custom/widgets.js"></script>
    <script src="assets/js/custom/apps/chat/chat.js"></script>
    <script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
    <script src="assets/js/custom/utilities/modals/create-app.js"></script>
    <script src="assets/js/custom/utilities/modals/users-search.js"></script>
    <script src="assets/js/custom/utilities/modals/approve-sv-amendReview.js"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->

    <!-- <script>
        var modal = document.getElementById("myModal");

        // Open the modal
        function openModal() {
            modal.style.display = "block";
        }

        // Close the modal
        function closeModal() {
            modal.style.display = "none";
        }

        // Submit form data
        document.getElementById("amendForm").addEventListener("submit", function(e) {
            e.preventDefault();

            // Collect form data
            var formData = {
                "steps": 1,
                "items": {
                    "title": document.getElementById("title").checked,
                    "provider": document.getElementById("provider").checked,
                    "info": document.getElementById("info").checked,
                    "notes": document.getElementById("notes").value,
                    "attachment": [parseInt(document.getElementById("attachment").value)]
                }
            };

            // Send form data to the server
            // Replace the following code with your AJAX request or API call
            console.log(formData);
            closeModal();
        });
    </script> -->
</body>
<!--end::Body-->

</html>