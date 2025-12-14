<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/tenant.php";
include "config/autoload.php";

$e = ApplicationEntry::viewEntry($systemId, 2)[0];
$s = ApplicationEntry::viewStatus($systemId)[0];
$c = ApplicationEntry::contactDetails($systemId);
$detail = ProjectDetails::projectDetails($systemId, 1, '')[0];
$costs = number_format(isset($e['project_costs']) ? $e['project_costs'] : 0, 2, '.', ',');
?>

<!DOCTYPE html>
<html lang="ms">

<!--begin::Head-->

<head>
    <base href="../../../" />
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

    <!--end::Theme mode setup on page load-->
    <!--begin::Main-->
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

                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Container-->
                    <div class="container" id="kt_content_container">
                        <div id="hidden_key" class="w-100"></div>
                        <!--begin::Card-->
                        <!-- <div class="card card-flush mb-5 mb-xxl-8" data-kt-sticky="true"
                            data-kt-sticky-name="sticky-summary" data-kt-sticky-width="{target: '#hidden_key'}"
                            data-kt-sticky-offset="{default: false, xl: '200px'}" data-kt-sticky-top="100px"
                            data-kt-sticky-animation="true" data-kt-sticky-zindex="95"> -->
                        <div class="card card-flush mb-5 mb-xxl-8">
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
                                        if (("/projects/record/check/" . $systemId == $_SERVER['REQUEST_URI']) && ($s['ProjectStatus'] == 1)) {

                                            //Array for this Nested
                                            $colors = ['light-dark', 'dark'];
                                            $modals = ['#modal-amend', '#modal-approve'];
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
                                        
                                        } else {
                                        
                                            //Array for this Nested
                                            $colors = ['light-dark', 'dark'];
                                            $href = ['/projects/prints/BKIL/' . $systemId, '/dashboard'];
                                            $icons = ['fa-print', 'fa-arrow-left'];
                                            $items = ['Cetak', 'Kembali'];
                                        
                                            $array = array_map(function ($color, $href, $icon, $item) {
                                                return ['color' => $color, 'href' => $href, 'icon' => $icon, 'item' => $item];
                                            }, $colors, $href, $icons, $items);
                                        
                                            foreach ($array as $button) {
                                                echo <<<Action
                                                <a href="{$button['href']}" class="btn btn-flex flex-center btn-{$button['color']} me-2">
                                                    <i class="fad {$button['icon']} fs-4"></i>
                                                    <span class="d-none d-md-inline">{$button['item']}</span>
                                                </a>
                                                Action;
                                            }
                                        
                                        }                                        
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--begin::Permohonan-->
                        <div class="card card-flush pt-3 mb-5 mb-xl-10">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="text-justify text-gray-900 fs-4">
                                        <?php echo $e['project_title'] ? $e['project_title'] : '-'; ?>
                                    </h3>
                                </div>
                            </div>
                            <!--end::Card header-->
                            
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
                        <!--end::Permohonan-->

                        <!--begin::Pegawai Utiliti-->
                        <div data-items="utility-officer" class="card card-flush py-4 mb-5">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title required">
                                    <h2>Maklumat Pegawai Utiliti</h2>
                                </div>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="mb-5 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nama Pegawai Bertanggungjawab</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="cont-full-name" class="form-control mb-2"
                                        placeholder="Nama Penuh Pegawai" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10 row">
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Jawatan</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-position" class="form-control mb-2"
                                            placeholder="Jawatan Pegawai" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">No Telefon Bimbit</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-phone-no" class="form-control mb-2"
                                            placeholder="No Telefon Pegawai" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">E-mel</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="email" name="cont-email" class="form-control mb-2"
                                            placeholder="Alamat E-mel" />
                                        <!--end::Input-->
                                    </div>

                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-5 row">
                                    <div class="col-12 col-lg-12 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Nama Syarikat</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-company-name" class="form-control mb-2"
                                            placeholder="Nama Syarikat" />
                                        <!--end::Input-->
                                    </div>

                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-5">
                                    <div class="fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Alamat Syarikat</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-unit-no" class="form-control mb-2"
                                            placeholder="No Unit, Bangunan" />
                                        <!--end::Input-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-street-name" class="form-control mb-2"
                                            placeholder="Jalan, Taman" />
                                        <!--end::Input-->
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="cont-postcode" class="form-control mb-2"
                                                placeholder="Poskod" />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-6 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="cont-city" class="form-control form-control-solid"
                                                placeholder="Bandar" readonly />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-6 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="cont-state" class="form-control form-control-solid"
                                                placeholder="Negeri" readonly />
                                            <!--end::Input-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->


                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Pegawai Utiliti-->
                        
                        <!--begin::Kontraktor Utiliti-->
                        <div data-items="main-contractor" class="card card-flush py-4 mb-5">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title required">
                                    <h2>Maklumat Kontraktor Utiliti</h2>
                                </div>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="mb-5 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nama Pegawai Bertanggungjawab</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="cont-full-name" class="form-control mb-2"
                                        placeholder="Nama Penuh Pegawai" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10 row">
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Jawatan</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-position" class="form-control mb-2"
                                            placeholder="Jawatan Pegawai" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">No Telefon Bimbit</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-phone-no" class="form-control mb-2"
                                            placeholder="No Telefon Pegawai" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">E-mel</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="email" name="cont-email" class="form-control mb-2"
                                            placeholder="Alamat E-mel" />
                                        <!--end::Input-->
                                    </div>

                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-5 row">
                                    <div class="col-12 col-lg-12 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Nama Syarikat</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-company-name" class="form-control mb-2"
                                            placeholder="Nama Syarikat" />
                                        <!--end::Input-->
                                    </div>

                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-5">
                                    <div class="fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Alamat Syarikat</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-unit-no" class="form-control mb-2"
                                            placeholder="No Unit, Bangunan" />
                                        <!--end::Input-->
                                        <!--begin::Input-->
                                        <input type="text" name="cont-street-name" class="form-control mb-2"
                                            placeholder="Jalan, Taman" />
                                        <!--end::Input-->
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="cont-postcode" class="form-control mb-2"
                                                placeholder="Poskod" />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-6 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="cont-city" class="form-control form-control-solid"
                                                placeholder="Bandar" readonly />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-6 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="cont-state" class="form-control form-control-solid"
                                                placeholder="Negeri" readonly />
                                            <!--end::Input-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->


                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Kontraktor Utiliti-->
                        
                        <!--begin::Kontraktor Sivil-->
                        <div data-items="civil-contractor" class="card card-flush py-4 mb-5">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <div class="card-title required">
                                    <h2>Maklumat Kontraktor Kerja Sivil</h2>
                                </div>
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Input group-->
                                <div class="mb-5 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nama Pegawai Bertanggungjawab</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="civil-full-name" class="form-control mb-2"
                                        placeholder="Nama Penuh Pegawai" />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-10 row">
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Jawatan</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="civil-position" class="form-control mb-2"
                                            placeholder="Jawatan Pegawai" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">No Telefon Bimbit</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="civil-phone-no" class="form-control mb-2"
                                            placeholder="No Telefon Pegawai" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col-12 col-lg-4 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">E-mel</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="email" name="civil-email" class="form-control mb-2"
                                            placeholder="Alamat E-mel" />
                                        <!--end::Input-->
                                    </div>

                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-5 row">
                                    <div class="col-12 col-lg-12 fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Nama
                                            Syarikat</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="civil-company-name" class="form-control mb-2"
                                            placeholder="Nama Syarikat" />
                                        <!--end::Input-->
                                    </div>

                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="mb-5">
                                    <div class="fv-row">
                                        <!--begin::Label-->
                                        <label class="required form-label">Alamat Syarikat</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" name="civil-unit-no" class="form-control mb-2"
                                            placeholder="No Unit, Bangunan" />
                                        <!--end::Input-->
                                        <!--begin::Input-->
                                        <input type="text" name="civil-street-name" class="form-control mb-2"
                                            placeholder="Jalan, Taman" />
                                        <!--end::Input-->
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="civil-postcode" class="form-control mb-2"
                                                placeholder="Poskod" />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-6 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="civil-city" class="form-control form-control-solid"
                                                placeholder="Bandar" readonly />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-6 col-lg-4 fv-row">
                                            <!--begin::Input-->
                                            <input type="text" name="civil-state" class="form-control form-control-solid"
                                                placeholder="Negeri" readonly />
                                            <!--end::Input-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->


                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Kontraktor Sivil-->

                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Content-->

                <!--begin::Footer-->
                <?php include "components/layouts/footer.php"; ?>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
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
    <script src="assets/js/custom/utilities/modals/approve-app.js"></script>
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