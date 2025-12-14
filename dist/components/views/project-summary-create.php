<?php
$systemId = $_GET['sid'];
// $authorityId = $_GET['aid'];

$Id = Permitting::dataMasterView($systemId);
$AIDRow = reset($Id);

$task = Permitting::taskId($systemId);
$taskRow = reset($task);

?>
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid mb-10 mb-lg-0">

        <div class="d-flex flex-column flex-lg-row">
            <div class="col-3 ">
                <?php include "components/partials/widgets/kronologi-lampiran.php" ?>
            </div>

            <div class="col-9">
                <div class=" flex-row-fluid">

                    <!--begin::Stepper-->
                    <div class="stepper stepper-pills " id="kt_stepper_project_summary">
                        <!--begin::Nav-->



                        <!-- TODO: nav-->
                        <div class="stepper-nav flex-center flex-wrap align-items-start mb-5" data-rp-stepper="nav">
                            <div class="stepper-item mx-8 my-4 current" data-kt-stepper-element="nav">
                                <!--begin::Wrapper-->
                                <div class="stepper-wrapper d-flex align-items-start">
                                    <!--begin::Icon-->
                                    <div class="stepper-icon w-40px h-40px">
                                        <i class="stepper-check fas fa-check"></i>
                                        <span class="stepper-number">1</span>
                                    </div>
                                    <!--end::Icon-->

                                    <!--begin::Label-->
                                    <div class="stepper-label w-175px">
                                        <h3 class="stepper-title">
                                            Maklumat Projek
                                        </h3>

                                        <div class="stepper-desc">
                                            Ringkasan Projek
                                        </div>
                                    </div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Wrapper-->

                                <!--begin::Line-->
                                <div class="stepper-line h-40px"></div>
                                <!--end::Line-->
                            </div>

                        </div>




                        <div class="card mb-5">
                        <!--begin::Card body-->
                            <div class="card-body py-12 px-20">

                                <form class="form" novalidate="novalidate"
                                    id="form-project-summary"
                                    action="components/views/project-summary-review.php" method="POST">

                                    <!--begin::Group-->
                                    <!-- TODO: content -->

                                    <div class="mb-10" data-rp-stepper="content">

                                        <div class="flex-column current" data-kt-stepper-element="content">

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <span class="col-3">No Rujukan</span>
                                                <span class="col-1">:</span>
                                                <div class="col-8">
                                                    <span id="ref-no" name="ref-no" type="text"
                                                        class="fw-bold fs-6 text-gray-800"><?php echo $AIDRow['reference_no']; ?></span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <span class="col-3">Penyedia Utiliti</span>
                                                <span class="col-1">:</span>
                                                <div class="col-8">
                                                    <span id="provider-name" name="provider-name" type="text"
                                                        class="fw-bold fs-6 text-gray-800"><?php echo $AIDRow['provider']; ?></span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <span class="col-3">Daerah</span>
                                                <span class="col-1">:</span>
                                                <div class="col-8">
                                                    <span id="district" name="district" type="text"
                                                        class="fw-bold fs-6 text-gray-800"><?php echo $AIDRow['district']; ?></span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <span class="col-3">Tajuk Projek</span>
                                                <span class="col-1">:</span>
                                                <div class="col-8">
                                                    <span id="project-title" name="project-title" type="text"
                                                        class="fw-bold fs-6 text-gray-800"><?php echo $AIDRow['project_title']; ?></span>
                                                </div>
                                            </div>


                                            <input type="text" name="system-id"
                                                id="system-id"
                                                value="<?php echo $systemId ?>" hidden />
                                                <input type="text" name="reference-no"
                                                id="reference-no"
                                                value="<?php echo $AIDRow['reference_no']; ?>" hidden />
                                            <input type="text" name="provider-id" value="<?php echo $AIDRow['provider']; ?>"
                                                hidden />
                                            <input type="text" name="add-project-summary" value="add" hidden />



                                        </div>

                                    </div>

                                    <!--begin::Actions-->
                                    <div class="d-flex flex-stack">
                                        <!--begin::Wrapper-->
                                        <div class="me-2">
                                            <button type="button" class="btn btn-light btn-active-light-primary"
                                                data-kt-stepper-action="previous">
                                                Kembali
                                            </button>
                                        </div>
                                        <!--end::Wrapper-->

                                        <!--begin::Wrapper-->
                                        <div>
                                            <button type="submit" class="btn btn-light-primary btn-active-primary"
                                                id="submit-project-summary"
                                                data-kt-stepper-action="submit">
                                                <!--begin::Indicator label-->
                                                <span class="indicator-label">Jana</span>
                                                <!--end::Indicator label-->
                                                <!--begin::Indicator progress-->
                                                <span class="indicator-progress">Sila Tunggu...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                                <!--end::Indicator progress-->
                                            </button>

                                            <button type="button" class="btn btn-light-primary btn-active-primary"
                                                data-kt-stepper-action="next">
                                                Seterusnya
                                            </button>
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Actions-->

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Layout-->
</div>