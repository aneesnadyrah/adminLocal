<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/functions/general.php";
require_once "config/autoload.php";

$view = new SurveyReport($username);
$reportDetail = $view->viewData($_GET['sid'] ?? null);
$systemId = "";
$surveyWorkList = $view->getSurveyWorkDesc();
$surveyEquipmentList = $view->getSurveyEquipmentDesc();
$idRepeat = "{";
foreach ($reportDetail as $field) : 
    if($systemId === "" || $systemId !== $field->system_id) {
        $systemId = $field->system_id;
        $survey_date = isset($field->survey_date) ? new DateTime($field->survey_date) : '';
        $start_date = $survey_date !== null ? Survey::formatMalayDate($survey_date->format('d M Y')) : '';
        $i = 0;
    }
    $reference_no = $field->reference_no;
    $survey_team = $field->team;
    $title = $field->project_title;
    $provider = $field->provider;
    $application_length = $field->application_length;
    $survey_length = $field->current_progress;
    $survey_date2 = isset($field->survey_date) ? new DateTime($field->survey_date) : '';
    $biggest = ($survey_date > $survey_date2) ? Survey::formatMalayDate($survey_date->format('d M Y')) : Survey::formatMalayDate($survey_date2->format('d M Y'));
    $lowest  = ($survey_date < $survey_date2) ? Survey::formatMalayDate($survey_date->format('d M Y')) : Survey::formatMalayDate($survey_date2->format('d M Y'));
    $workDone = $field->survey_work;
    $equipementUsed = $field->equipment;
    $kordinatMula = $field->latitude_start.','.$field->longitude_start;
    $kordinatAkhir = $field->latitude_end.','.$field->longitude_end;
    $member = $field->team_members;
    $idRepeat .= (!isset($field->id_repeat)||$i==0)? $field->id_repeat:','.$field->id_repeat;
    $i++;
    $createdBy = $field->created_id;
endforeach;
$idRepeat .= '}';
// $createdByName = $view->getFullname($createdBy);
$memberList = $view->getSurveyTeamMembersName($member);
$displayImage = $view->getSurveyImage($systemId,$idRepeat);
$imgcat01 = [];
$imgcat02 = [];
$imgcat03 = [];
foreach ($displayImage as $image) : 
    if($image->category === 1) {
        $imgcat01[] = $image;
        $countImgCat01 = count($imgcat01);
    }elseif($image->category === 2) {
        $imgcat02[] = $image;
        $countImgCat02 = count($imgcat02);
    }elseif($image->category === 3) {
        $imgcat03[] = $image;  
        $countImgCat03 = count($imgcat03);
    }
endforeach;
    $roadlist = $view->getPKDRoadList($systemId);
    $roadNames = array();
    foreach ($roadlist as $item) {
        $roadNames[] = $item['road_name'];
    }
    // Remove duplicates and implode the unique road names
    $uniqueRoadNames = implode(" - ", array_unique($roadNames));
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
                        <?php 
                            include "components/views/survey-udm-report-view.php";
                        ?>
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
    <?php 
    // foreach ($drawers as $drawer) {
    //     include $drawer;
    // } ?>
    <!--end::Drawers-->
    <!--end::Main-->
    <!--begin::Modals-->
    <!--begin::Modal - Create App-->
    <?php 
    // foreach ($generalModals as $modal) {
    //     include $modal;
    // } ?>
    <?php 
    // foreach ($actionModals as $modal) {
    //     include $modal;
    // } ?>
    <?php 
    // foreach ($taskModals as $modal) {
    //     include $modal;
    // } ?>
    <?php include "components/partials/modals/amend-survey-report-udm.php"; ?>
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
    // echo SurveyApi::getProjectStatus($_GET['sid'])[0]['project_status']; ?>;
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/utilities/sidebar/pin-projects.js"></script>
    <!-- <script async src="assets/js/custom/pages/projects/tasks/new-task.js"></script> -->
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>