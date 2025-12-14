<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/tenant.php";
include "config/autoload.php";

$e = ApplicationEntry::viewEntry($systemId,1)[0];
$r = ApplicationEntry::viewRoad($systemId,'1');
$s = ApplicationEntry::viewStatus($systemId)[0];
$c = ApplicationEntry::contactDetails($systemId);
$detail = ProjectDetails::projectDetails($systemId, 1, '')[0];
?>

<!DOCTYPE html>
<html lang="ms">

<!--begin::Head-->

<head>
    <base href="../../../" />
    <title><?php echo $appsTitle ?></title>
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
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Aside-->
            <?php include "components/layouts/sidebar/mainLayout.php"?>
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Header-->
                <?php include "components/layouts/header.php"?>
                <!--end::Header-->

                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Container-->
                    <div class="container-xxl" id="kt_content_container">
                        <!--begin::Layout-->
                        <div class="d-flex flex-column flex-lg-row">
                            <!--begin::Content-->
                            <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
                                <!--begin::Card-->
                                <div class="card card-flush pt-3 mb-5 mb-xl-10">
                                    <!--begin::permohonan-->
                                    <div>
                                        <!--begin::Card header-->
                                        <div class="card-header">
                                            <!--begin::Card title-->
                                            <div class="card-title">
                                                <h2 class="fw-bold">Butiran Permohonan</h2>
                                            </div>
                                            <?php
                                                if ("/projects/record/review/" . $systemId  == $_SERVER['REQUEST_URI']) {
                                            ?>

                                                <div class="card-toolbar">
                                                    <button  onclick="window.location.href='/projects/record/entry/<?php echo $systemId ?>'" class="btn btn-flex flex-center btn-primary me-2">
                                                    <i class="fad fa-circle-plus fs-4"></i>
                                                        <span class="d-none d-md-inline">Kemaskini</span>
                                                    </button>
                                                    <button onclick="window.location.href='/dashboard';" type="button"
                                                        class="btn btn-flex text-center btn-secondary">
                                                        <i class="fad fa-file-circle-check fs-4"></i>
                                                        <span class="d-none d-md-inline">Kembali</span>
                                                    </button>
                                                </div>
                                                
                                            <?php
                                                }
                                            ?>
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-3">
                                            <!--begin::Section-->
                                            <div>
                                                <!--begin::Title-->
                                                <h5 class="mb-4">Maklumat Permohonan:</h5>
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
                                                            <!--begin::Row-->
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-gray-400 min-w-175px w-175px">Tajuk
                                                                        Permohonan:</td>
                                                                    <td class="text-gray-800 min-w-200px">
                                                                        <?php echo $e['project_title'] ? $e['project_title'] : '-'; ?></td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Nama Tapak A:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $e['site_start'] ? $e['site_start'] : '-'; ?></td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Link ID / No Projek:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $e['link_id'] ? $e['link_id'] : '-'; ?></td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Jenis Permohonan:</td>
                                                                    <td class="text-gray-800"><?php echo $jenis ? $jenis : '-' ?></td>
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
                                                            <!--begin::Row-->
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-gray-400">No Rujukan:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $e["reference_no"] ? $e["reference_no"] : '-';?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Nama Tapak B:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $e['site_end'] ? $e['site_end'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Kategori Permohonan:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $kategori ? $kategori : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                            </tbody>
                                                        </table>
                                                        <!--end::Details-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Row-->
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::permohonan-->

                                    <!--begin::pegawai-->
                                    <div>
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
                                            <!--begin::Section pemohon-->
                                            <div class="mb-10">
                                                <!--begin::Title-->
                                                <h5 class="mb-4">Maklumat Pemohon:</h5>
                                                <!--end::Title-->
                                                <!--begin::Details-->
                                                <div class="d-flex flex-wrap py-5">
                                                    <!--begin::Row-->
                                                    <div class="flex-equal me-5">
                                                        <!--begin::Details-->
                                                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                            <!--begin::Row-->
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-gray-400 min-w-175px w-175px">Nama
                                                                        Syarikat:</td>
                                                                    <td class="text-gray-800 min-w-200px">
                                                                        <?php echo $c[0]['CompanyName']; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Alamat Syarikat:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[0]['Address1']; ?>
                                                                        <?php echo $c[0]['Address2']; ?>,
                                                                        <?php echo $c[0]['Postcode']; ?>,
                                                                        <?php echo $c[0]['City']; ?>,
                                                                        <?php echo $c[0]['State']; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Jawatan:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[0]['Position']; ?>
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
                                                            $typeApplicant = '';

                                                            if ($c[0]['Type'] == 1) {
                                                                $typeApplicant = '<span class="badge badge-light-primary me-auto">Persendirian</span>';
                                                            } elseif ($c[0]['Type'] == 2) {
                                                                $typeApplicant = '<span class="badge badge-light-success me-auto">Kontraktor</span>';
                                                            } elseif ($c[0]['Type'] == 3) {
                                                                $typeApplicant = '<span class="badge badge-light-warning me-auto">Konsultan</span>';
                                                            } elseif ($c[0]['Type'] == 4) {
                                                                $typeApplicant = '<span class="badge badge-light-danger me-auto">Pemaju</span>';
                                                            } elseif ($c[0]['Type'] == 5) {
                                                                $typeApplicant = '<span class="badge badge-light-info me-auto">Penyedia Utiliti</span>';
                                                            } elseif ($c[0]['Type'] == 9) {
                                                                $typeApplicant = '<span class="badge badge-light-dark me-auto">Tidak Ketahui</span>';
                                                            }
                                                        ?>
                                                        <!--begin::Details-->
                                                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                            <tbody>
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Nama Pemohon:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[0]['FullName'] ? $c[0]['FullName'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">No Telefon:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[0]['PhoneNo'] ? $c[0]['PhoneNo'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Emel:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[0]['Email'] ? $c[0]['Email'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Jenis Pemohon</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $typeApplicant ? $typeApplicant : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                            </tbody>
                                                        </table>
                                                        <!--end::Details-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Row-->
                                            </div>
                                            <!--end::Section pemohon-->

                                            <!--begin::Section utiliti-->
                                            <div class="mb-10">
                                                <!--begin::Title-->
                                                <h5 class="mb-4">Maklumat Pegawai Utiliti:</h5>
                                                <!--end::Title-->
                                                <!--begin::Details-->
                                                <div class="d-flex flex-wrap py-5">
                                                    <!--begin::Row-->
                                                    <div class="flex-equal me-5">
                                                        <!--begin::Details-->
                                                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                            <!--begin::Row-->
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-gray-400 min-w-175px w-175px">Nama
                                                                        Pegawai:</td>
                                                                    <td class="text-gray-800 min-w-200px">
                                                                        <?php echo $c[1]['FullName'] ? $c[1]['FullName'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">No Telefon:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[1]['PhoneNo'] ? $c[1]['PhoneNo'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Alamat Syarikat:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[1]['Address1']; ?>
                                                                        <?php echo $c[1]['Address2']; ?>,
                                                                        <?php echo $c[1]['Postcode']; ?>,
                                                                        <?php echo $c[1]['City']; ?>,
                                                                        <?php echo $c[1]['State']; ?>
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
                                                        <!--begin::Details-->
                                                        <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                                            <!--begin::Row-->
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-gray-400">Jawatan:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[1]['Position'] ? $c[1]['Position'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                                <!--begin::Row-->
                                                                <tr>
                                                                    <td class="text-gray-400">Emel:</td>
                                                                    <td class="text-gray-800">
                                                                        <?php echo $c[1]['Email'] ? $c[1]['Email'] : '-'; ?>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Row-->
                                                            </tbody>
                                                        </table>
                                                        <!--end::Details-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Row-->
                                            </div>
                                            <!--end::Section utiliti-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::pegawai-->

                                    <!--begin::jalan-->
                                    <div>
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
                                                <h5 class="mb-4">Maklumat Jalan Terlibat:</h5>
                                                <!--end::Title-->
                                                <!--begin::Product table-->
                                                <div class="table-responsive">
                                                    <!--begin::Table-->
                                                    <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                                        <!--begin::Table head-->
                                                        <thead>
                                                            <!--begin::Table row-->
                                                            <tr
                                                                class="border-bottom border-gray-200 text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
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
                                                                    <label
                                                                        class="w-150px"><?php echo $road['road_name']; ?></label>
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
                                                                <td><?php echo $road['road_length']; ?></td>
                                                            </tr>
                                                            <?php }?>
                                                        </tbody>
                                                        <!--end::Table body-->
                                                    </table>
                                                    <!--end::Table-->
                                                </div>
                                                <!--end::Product table-->
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::jalan-->

                                </div>
                                <!--end::Card-->
                            </div>
                            <!--end::Content-->

                            <!--begin::Sidebar-->
                            <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
                                <!--begin::Card-->
                                <div class="card card-flush mb-0" data-kt-sticky="true"
                                    data-kt-sticky-name="subscription-summary"
                                    data-kt-sticky-offset="{default: false, lg: '200px'}"
                                    data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto"
                                    data-kt-sticky-top="150px" data-kt-sticky-animation="false"
                                    data-kt-sticky-zindex="95">
                                    <!--begin::Card header-->
                                    <div class="card-header">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <h2>Penyedia Utiliti</h2>
                                        </div>
                                        <!--end::Card title-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0 fs-6">
                                        <!--begin::Section-->
                                        <div class="mb-7">
                                            <!--begin::Details-->
                                            <div class="text-center">
                                                <!--begin::Avatar-->
                                                <div class="symbol symbol-100px">
                                                    <img src="assets/media/provider/<?php echo $e['utility_provider'] ?>.webp"
                                                        alt="image" />
                                                </div>
                                                <!--end::Avatar-->
                                                <!--begin::Info-->
                                                <div class="d-flex flex-column">
                                                    <!--begin::Name-->
                                                    <?php
                                                        foreach (General::selection('ls_provider') as $row) {
                                                            $provider = '';
                                                            // $selected = $row['method'] == $road['method'] ? $row['name'] : '';
                                                            if ($row['id'] == $e['utility_provider']) {
                                                                $provider = $row['name'];
                                                                break;
                                                            }
                                                        }
                                                    ?>

                                                    <!--end::Name-->
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Section-->

                                        <!--begin::Seperator-->
                                        <div class="separator separator-dashed mb-7"></div>
                                        <!--end::Seperator-->

                                        <!--begin::Section-->
                                        <div class="mb-7">
                                            <!--begin::Title-->
                                            <h5 class="mb-4">Status Permohonan</h5>
                                            <!--end::Title-->
                                            <!--begin::status-->
                                            <div class="mb-0">
                                                <!--begin::status-->
                                                <span
                                                    class="badge badge-light-<?php echo $detail['status_color'] ?> me-auto"><?php echo $detail['project_status'] ?></span>
                                                <!--end::status-->
                                            </div>
                                            <!--end::status-->
                                        </div>
                                        <!--end::Section-->

                                        <!--begin::Seperator-->
                                        <div class="separator separator-dashed mb-7"></div>
                                        <!--end::Seperator-->

                                        <!--begin::Section-->
                                        <div class="mb-7">
                                            <!--begin::Title-->
                                            <h5 class="mb-4">Tarikh Permohonan</h5>
                                            <!--end::Title-->
                                            <!--begin::Details-->
                                            <div class="mb-0">
                                                <!--begin::date-->
                                                <span
                                                    class="fw-semibold text-gray-600"><?php echo General::convertDate($e['application_date']) ?></span>
                                                <!--end::date-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Section-->

                                        <!--begin::Seperator-->
                                        <div class="separator separator-dashed mb-7"></div>
                                        <!--end::Seperator-->

                                        <!--begin::Section-->
                                        <div class="mb-7">
                                            <!--begin::Title-->
                                            <h5 class="mb-4">Jarak Permohonan</h5>
                                            <!--end::Title-->
                                            <!--begin::Details-->
                                            <div class="mb-0">
                                                <!--begin::jarak-->
                                                <div class="d-flex align-items-center">
                                                    <div class="fw-semibold text-gray-600" data-kt-countup="true"
                                                        data-kt-countup-value="<?php echo $e['application_length'] ?>">0
                                                    </div>
                                                    <span class="fw-semibold text-gray-600"> m</span>
                                                </div>
                                                <!--end::jarak-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Section-->

                                        <!--begin::Seperator-->
                                        <div class="separator separator-dashed mb-7"></div>
                                        <!--end::Seperator-->

                                        <!--begin::Section-->
                                        <div class="mb-7">
                                            <!--begin::Title-->
                                            <h5 class="mb-4">Daerah Terlibat</h5>
                                            <!--end::Title-->
                                            <!--begin::Details-->
                                            <div class="mb-0">
                                                <!--begin::daerah-->
                                                <?php $dist = explode(",", $detail['district']);
                                                    $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                                    shuffle($colors);
                                                    foreach ($dist as $row) {
                                                        echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                                    }
                                                ?>
                                                <!--end::daerah-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Section-->

                                        <!--begin::Seperator-->
                                        <div class="separator separator-dashed mb-7"></div>
                                        <!--end::Seperator-->

                                        <!--begin::Section-->
                                        <div>
                                            <!--begin::Title-->
                                            <h5 class="mb-2">Lampiran</h5>
                                            <!--end::Title-->
                                            <!--begin::lampiran-->
                                            <div>
                                                <!--begin::file-->
                                                <!--begin::Card body-->
                                                <div
                                                    class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="d-block overlay" data-fslightbox="lightbox" data-class="fslightbox-source" href="#pdf-bkil">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-60px mb-5 mt-7">
                                                            <img src="assets/media/files/pdf.svg"
                                                                class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg"
                                                                class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->

                                                        <!--begin::Action-->
                                                        <div
                                                            class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow min-h-170px">
                                                            <i class="fad fa-eye text-white fs-1"></i>
                                                        </div>
                                                        <!--end::Action-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->

                                                <div style="display: none;">
                                                    <div id="pdf-bkil">
                                                        <iframe class="scroll h-700px w-900px px-5"
                                                            src="/components/partials/widgets/print.php?f=<?php echo $e['url']?>&t=<?php echo $e['attachment_type']?> ">
                                                        </iframe>
                                                    </div>
                                                </div>
                                                <!--end::file-->
                                            </div>
                                            <!--end::lampiran-->
                                        </div>
                                        <!--end::Section-->

                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card-->
                            </div>
                            <!--end::Sidebar-->
                        </div>
                        <!--end::Layout-->
                    </div>
                    <!--end::Container-->
                </div>

                <!--begin::Footer-->
                <?php include "components/layouts/footer.php";?>
                <!--end::Footer-->
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
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>