<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
$system = new System;
$user = new User();

?>
<div id="kt_aside" id="kt_aside" class="aside aside-extended " data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="auto" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Primary-->
    <div class="aside-primary d-flex flex-column align-items-lg-center flex-row-auto">
        <!--begin::Logo-->
        <div class="aside-logo d-none d-lg-flex flex-column align-items-center flex-column-auto py-10" id="kt_aside_logo">
            <a href="/dashboard/">
                <img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title) ?>-small.svg" class="h-40px" />
            </a>
        </div>
        <!--end::Logo-->
        <!-- <?php //include "components/layouts/sidebar/primarySidebar.php" ?> -->

        <!--begin::primarySidebar-->
            <?php $user->primary($username); ?>
        <!--end::primarySidebar-->
    </div>
    <!--end::Primary-->
    <!--begin::Secondary-->
    <div class="aside-secondary d-flex flex-row-fluid">
        <?php include "components/layouts/sidebar/secondarySidebar.php" ?>
    </div>
    <!--end::Secondary-->
    <!--begin::Aside Toggle-->
    <button id="kt_aside_toggle" class="aside-toggle btn btn-sm btn-icon bg-body btn-color-gray-700 btn-active-primary position-absolute translate-middle start-100 end-0 bottom-0 shadow-sm d-none d-lg-flex mb-5  active " data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr063.svg-->
        <span class="svg-icon svg-icon-2 rotate-180">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1" fill="currentColor" />
                <path d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z" fill="currentColor" />
            </svg>
        </span>
        <!--end::Svg Icon-->
    </button>
    <!--end::Aside Toggle-->
</div>