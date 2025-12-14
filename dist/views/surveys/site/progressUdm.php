<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "config/autoload.php";
require_once "config/functions/survey.php";

// // set the base apps path
// $dir = __DIR__; // Get the current directory

// while (true) {
//     $parentDir = dirname($dir); // Get the parent directory

//     if ($parentDir === $dir) {
//         // We have reached the root directory
//         echo "The 'apps' directory was not found.";
//         break;
//     }

//     $appsDir = $parentDir . '\apps\dist'; // Construct the path to 'apps\dist\' directory

//     if (is_dir($appsDir)) {
//         // 'apps' directory found!
//         set_include_path($appsDir);
//         break;
//     }

//     $dir = $parentDir; // Move up to the next parent directory
// }

$e = Survey::getDataEntry($systemId);
$r = Survey::getDataRoad($systemId);

// $title = $e['project_title'];
$p = Survey::listProvider($systemId);
$c = Survey::currentProgressUdm($systemId);
$data = Survey::timeAttend($systemId);
$start = '';
$end = '';


if (!empty($data['clock_in'])) {
    $startDateTime = $data['clock_in'];
    $start = !empty($startDateTime) ? date('H:i:s', strtotime($startDateTime)) : '';
} else {
    $start = date('H:i:s');
}

if (!empty($data['clock_out'])) {
    $endDateTime = $data['clock_out'];
    $end = !empty($endDateTime) ? date('H:i:s', strtotime($endDateTime)) : '';
} else {
    $end = date('H:i:s');
}

$option = '<option value="" data-profile-picture="">Kumpulan</option>';
foreach (Survey::surveyTeamModal() as $team) {
    if ($team['profile_picture'] == null) {
        $img = "blank";
    } else {
        $img = $team['profile_picture'];
    };
    $name = $team['survey_team'];
    $id = $team['id'];

    $option .= '<option value="' . $id . '" data-profile-picture="' . General::getProfile($img) . '.jpg">' . $name . '</option>';
}


$surveyWork = '';
foreach (Survey::listSurveyWork() as $work) {
    $id = $work['id'];
    $name = $work['name'];

    $surveyWork .= '<option value="' . $id . '">' . $name . '</option>';
}

$surveyWorkDesc = '';
foreach (Survey::listSurveyWorkDescription() as $work) {
    $id = $work['id'];
    $name = $work['name'];

    $surveyWorkDesc .= '<option value="' . $id . '">' . $name . '</option>';
}

?>

<!DOCTYPE html>
<html lang="ms">
<!--begin::Head-->

<head>

    <base href="/" />
    <title><?= $system->App->title ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/<?= strtolower($system->App->title) ?>-small.svg" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <style>
        .progress-bar-container {
            width: 100%;
            height: 6px;
            background-color: #f8f9fa;
            border-radius: 3px;
            margin-bottom: 10px;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background-color: #009ef7;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .progress-value {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #000;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .progress-bar-container:hover .progress-value {
            opacity: 1;
        }

        /* CSS for Dropzone */
        .dropzone1 {
            min-height: auto;
            /* padding: 0.1rem 0.1rem; */
            text-align: center;
            cursor: pointer;
            border: 1px dashed;
            background-color: var(--bs-primary-light);
            border-radius: 0.475rem !important;
            border: 1px solid var(--bs-white);
        }

        .dropzone1 .dz-message {
            margin: 0;
            display: flex;
            text-align: left;
        }

        .dropzone1 .dz-preview {
            border-radius: 2px !important;
            margin: 0.75rem;
        }

        .dropzone1 .dz-preview .dz-image {
            border-radius: 2px !important;
            z-index: 1;
        }

        .dropzone1 .dz-preview.dz-file-preview .dz-image {
            background: #009ef7;
        }

        <?php $size = '40px'; ?>.dropzone1 .dz-success-mark,
        .dropzone1 .dz-error-mark {
            margin-left: 2px / 2 !important;
            margin-top: 2px / 2 !important;
        }

        .dropzone1 .dz-success-mark svg,
        .dropzone1 .dz-error-mark svg {
            height: 100% !important;
            width: 100% !important;
        }

        .dropzone1 .dz-remove {
            size: 1.65rem;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
            font-size: 1rem;
            text-indent: -9999px;
            white-space: nowrap;
            position: absolute;
            z-index: 2;
            background-color: lightgrey !important;
            box-shadow: lightgrey;
            border-radius: 100%;
            top: -<?php echo $size; ?> / 2;
            right: -<?php echo $size; ?> / 2;
        }

        .dropzone1 .dz-remove:after {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            display: block;
            content: "";
            mask-size: 40%;
            -webkit-mask-size: 40%;
        }

        .dropzone1 .dz-error-message {
            color: var(--<?php echo $prefix; ?>danger-inverse);
            background: var(--<?php echo $prefix; ?>danger);
        }

        .dropzone-panel .dropzone-select {
            padding-top: calc(1px + 1.25rem) !important;
            padding-right: 1px !important;
            padding-bottom: calc(1px + 1.25rem) !important;
            padding-left: 1px !important;
        }

        .dropzone-item {
            display: flex;
            align-items: center;
            padding-top: calc(1px + 0.85rem) !important;
            padding-right: 1px !important;
            padding-bottom: calc(1px + 0.85rem) !important;
            padding-left: 10px !important;
        }

        .dropzone-item .row {
            display: flex;
            align-items: center;
        }

        .dropzone-item .dropzone-file {
            flex-grow: 1;
        }

        .dropzone-item .dropzone-toolbar {
            margin-left: auto;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            /* justify-content: flex-end; */
            display: flex;
            flex-wrap: nowrap;
        }

        .dropzone-item .dropzone-progress {
            margin-left: 0;
            margin-right: 0;
            padding-left: 25px !important;
        }

        .dropzone-item .dropzone-progress .progress {
            margin-top: 0.35rem;
            height: 4px;
            transition: <?php echo $transition_link; ?>;
        }

        .hide-progress-bar {
            width: 0 !important;
            opacity: 0 !important;
        }
    </style>

    <style>
    .dropzone-container {
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
        background: #ffffff;
    }

    .upload-status {
        font-size: 0.875rem;
    }

    .upload-area {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-area.dz-drag-hover {
        border-color: #3699ff;
        background: #f8fafc;
    }

    .dropzone-item {
        position: relative;
        background: #f8fafc;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .dropzone-item:hover {
        transform: translateY(-2px);
    }

    .dropzone-content {
        position: relative;
    }


    .dropzone-file {
        /* padding: 0.75rem; */
    }

    .filename {
        font-weight: 500;
        /* white-space: nowrap; */
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropzone-delete {
        position: absolute;
        top: 0.5rem;
        right: 1.5rem;
        background: rgba(255,255,255,0.9);
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .dropzone-delete:hover {
        background: #fff;
        transform: scale(1.1);
    }
    </style>

</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
    <!--begin::loader-->
    <div class="page-loader flex-column">
        <img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default.svg" />
        <img alt="Logo" class="theme-dark-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-dark.svg" />
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
                    <div class="container-xxl" id="kt_content_container">
                        <!--begin::Row-->
                        <?php include "components/forms/add-progress-udm.php" ?>
                        <!--end::Row-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Content-->
                <!--begin::Footer-->
                <?php include "components/layouts/footer.php" ?>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--begin::Drawers-->
    <!--begin::Chat drawer-->
    <?php 
    // include "components/partials/drawers/drawer-1.php" ?>
    <!--end::Chat drawer-->
    <!--end::Drawers-->
    <!--end::Main-->
    <!--begin::Modals-->
    <!--begin::Modal - Create App-->
    <?php
    // foreach ($taskModals as $modal) {
    //     include $modal;
    // }
    ?>
    <?php
    // foreach ($generalModals as $modal) {
    //     include $modal;
    // }
    ?>
    <!--end::Modal - Create App-->
    <!--begin::Modal - Create App-->
    <!--end::Modal - Create App-->
    <!--begin::Modal - Create App-->
    <!--end::Modal - Create App-->
    <!--end::Modals-->
    <!--begin::Javascript-->
    <script>
		var hostUrl = "assets/";
		var tenant = "<?= $system->App->tenant ?>";
    </script>
    <script>
    var role = <?php echo json_encode($_SESSION['roleId']); ?>;
    // var projectStatus = <?php 
    // echo SurveyApi::getProjectStatus($_GET['sid'])[0]['project_status']; 
    ?>;
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/widgets.bundle.js"></script>
    <script src="assets/js/custom/widgets.js"></script>
    <script src="assets/js/custom/apps/chat/chat.js"></script>
    <script src="assets/js/custom/utilities/modals/create-app.js"></script>
    <script src="assets/js/custom/survey/site/progress-udm.js"></script>
    <script src="assets/js/custom/utilities/sidebar/pin-projects.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js"></script>
    <!--end::Custom Javascript-->
    <!--begin::API Create App-->

    <!--end::API Create App-->
</body>
<!--end::Body-->

</html>