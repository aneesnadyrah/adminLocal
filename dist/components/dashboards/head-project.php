<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
?>
<div class="row g-5 g-xl-10 mb-xl-10">
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <?php
        // widget1
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = 'primary';
            $bgColor = '#c6eaff';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = 'primary';
            $bgColor = '#D9EDDF';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = 'primary';
            $bgColor = '#FFF6DE';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = 'primary';
            $bgColor = '#FCEFF0';
        }
        include General::getWidget("progress-card");
        
        // widget2
        include General::getWidget("project-task-summary");
        ?>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <?php
        // widget1
        $head_title = 'Lawatan Tapak';
        $head_total = 21;
        $count_success = 15;
        $fIcon = 'fa-map-location-dot';
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = '--bs-success';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = '--bs-primary';
        }
        include General::getWidget("project-head-task-overview");
        
        // widget2
        $head_title = 'Permohonan CCC';
        $head_total = 4;
        $count_success = 3;
        $fIcon = 'fa-file-certificate';
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = '--bs-success';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = '--bs-primary';
        }
        include General::getWidget("project-head-task-overview");
        ?>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <?php
        // widget3
        $head_title = 'Mula Kerja';
        $head_total = 8;
        $count_success = 7;
        $fIcon = 'fa-person-digging';
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = '--bs-success';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = '--bs-primary';
        }
        include General::getWidget("project-head-task-overview");
        
        // widget4
        $head_title = 'CMGD';
        $head_total = 7;
        $count_success = 5;
        $fIcon = 'fa-sensor-triangle-exclamation';
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = '--bs-success';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = '--bs-primary';
        }
        include General::getWidget("project-head-task-overview");
        ?>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
        <?php
        // widget5
        $head_title = 'Permohonan CPC';
        $head_total = 5;
        $count_success = 3;
        $fIcon = 'fa-file-certificate';
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = '--bs-success';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = '--bs-primary';
        }
        include General::getWidget("project-head-task-overview");
        
        // widget6
        $head_title = 'Pemulangan WC';
        $head_total = 13;
        $count_success = 11;
        $fIcon = 'fa-money-check-dollar-pen';
        if ($_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech') {
            $progressColor = '--bs-success';
        } elseif ($_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech') {
            $progressColor = '--bs-primary';
        } elseif ($_SERVER['HTTP_HOST'] == 'kuk.test' || $_SERVER['HTTP_HOST'] == 'kuk.asiadebut.tech') {
            $progressColor = '--bs-primary';
        }
        include General::getWidget("project-head-task-overview");
        ?>
    </div>
</div>
<!--begin::Col-->

<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-xxl-7">
        <!--begin::Mixed Widget 12-->
        <?php include General::getWidget("project-map") ?>

        <!--end::Mixed Widget 12-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-5">
        <!--begin::Tables Widget 9-->
        <?php include General::getWidget("list-priority-GIS"); ?>
        <!--end::Tables Widget 9-->
    </div>
    <!--end::Col-->
</div>
