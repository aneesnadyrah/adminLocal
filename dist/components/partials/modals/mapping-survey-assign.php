<?php
$option = '';
foreach (Survey::surveyTeamModal() as $team) {
    if ($team['profile_picture'] == null) {
        $img = "blank";
    } else {
        $img = $team['profile_picture'];
    };
    $name = $team['survey_team'];
    $id = $team['id'];

    $option .= '<option value="' . $id . '" data-profile-picture="' . General::getProfile($img) . '.jpg">Kumpulan ' . $name . '</option>';
}
//new-task table
foreach ($data as $item) {
    echo '<!--begin::Modal-->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Persiapan Kerja Lapangan</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-' . $item->color . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>

                <!--begin::Modal body-->
                <div class="modal-body px-10 px-lg-10 pt-0 pb-5">

                    <!--begin::Stepper-->
                    <div class="stepper stepper-pills stepper-column " id="stepper-' . $item->id . '">

                        <!--begin::Nav-->
                        <div class="d-flex flex-row-auto w-100 w-250px  py-lg-5">
                            <div class="stepper-nav mb-5">
                                
                                <!--begin::Wrapper for Steps-->
                                <div class="d-flex">
                                    
                                    <!--begin::Step 1-->
                                    <div class="stepper-item mx-10 my-4 current" data-kt-stepper-element="nav">
                                        <!--begin::Wrapper-->
                                        <div class="stepper-wrapper d-flex align-items-center">
                                            <!--begin::Icon-->
                                            <div class="stepper-icon w-40px h-40px">
                                                <i class="stepper-check fas fa-check"></i>
                                                <span class="stepper-number">1</span>
                                            </div>
                                            <!--end::Icon-->
                                            <!--begin::Label-->
                                            <div class="stepper-label">
                                                <h3 class="stepper-title">
                                                    Jenis
                                                </h3>
                                                <div class="stepper-desc" style="white-space: nowrap;">
                                                    Kerja Ukur
                                                </div>
                                            </div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Step 1-->

                                    <!--begin::Step 2-->
                                    <div class="stepper-item mx-10 my-4" data-kt-stepper-element="nav">
                                        <!--begin::Wrapper-->
                                        <div class="stepper-wrapper d-flex align-items-center">
                                            <!--begin::Icon-->
                                            <div class="stepper-icon w-40px h-40px">
                                                <i class="stepper-check fas fa-check"></i>
                                                <span class="stepper-number">2</span>
                                            </div>
                                            <!--begin::Icon-->
                                            <!--begin::Label-->
                                            <div class="stepper-label">
                                                <h3 class="stepper-title">
                                                    Lantik
                                                </h3>
                                                <div class="stepper-desc" style="white-space: nowrap;">
                                                    Kumpulan Ukur
                                                </div>
                                            </div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Step 2-->
                                    
                                </div>
                                <!--end::Wrapper for Steps-->
                                
                            </div>
                        </div>
                        <!--end::Nav-->

                        <form class="form" novalidate="novalidate" data-form="form-' . $item->id . '">
                            
                        <!--begin::Group-->
                        <div>

                            <!--begin::Step 1-->
                            <div class="flex-column current" data-kt-stepper-element="content">
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <select id="selection3-' . $item->id . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3" name="selection-survey">    
                                        <option></option>
                                        <option value="1">Inhouse</option>
                                        <option value="2" disabled>Outsource</option>
                                    </select>
                                    <label for="selection3-' . $item->id . '" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Jenis Kerja Ukur</label>
                                </div>
                            </div>
                            <!--end::Step 1-->

                            <!--begin::Step 2-->
                            <div class="flex-column" data-kt-stepper-element="content">

                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <select id="selection-' . $item->id . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3" name="survey-team-assign">Kumpulan ' . $option . '</select>
                                    <label for="selection-' . $item->id . '" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Kumpulan Ukur</label>
                                </div>
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <select id="selection2-' . $item->id . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3" name="survey-leader-assign"></select>
                                    <label for="selection2-' . $item->id . '" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Ketua Kumpulan Ukur</label>
                                </div>
                                <!--begin::Input group-->
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <input class="form-control form-control-transparent rounded rounded-end-0 pt-10" id="modal-date-range-' . $item->id . '" name="modal-date-range"/>
                                    <label for="floatingInput" class="fs-5 fw-semibold form-label mb-0">Julat Tarikh Jangkaan Mula - Tamat</label>
                                </div>
                                <!--end::Input group-->   
                                <!--<div class="form-check mb-10 fv-row">
                                    <input class="form-check-input" type="checkbox" value="1" name="trigger-spku" id="trigger-spku" />
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Tanda Jika Perlu Surat Pengesahan Kedudukan Utiliti
                                    </label>
                                </div>--> 
                            </div>
                            <!--end::Step 2-->    

                        </div>
                        <!--end::Group-->

                        <!--begin::Actions-->
                        <div class="d-flex flex-stack">
                            <!--begin::Wrapper-->
                            <div class="d-flex justify-content-start">
                                <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                                    Kembali
                                </button>
                            </div>
                            <!--end::Wrapper-->

                            <!--begin::Submit button-->
                            <div  class="d-flex justify-content-end">
                                <button type="submit" data-submit="submit-' . $item->id . '" class="d-none btn btn-' . $item->color . '" data-kt-stepper-action="submit">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label"><i class="fad fa-user-pen"></i> Lantik</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Sila Tunggu...
                                        <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
                                    </span>
                                    <!--end::Indicator progress-->
                                </button>

                                <button type="button" class="btn btn-light-primary btn-active-primary" data-kt-stepper-action="next">
                                    Seterusnya
                                </button>
                            </div>
                            <!--end::Submit button-->
                        </div>
                        <!--end::Actions-->
                        </form>

                    </div>
                    <!--end::Stepper-->

                </div>
                <!--end::Modal body-->

            </div>
        </div>
    </div>
    <!--end::Modal-->';
    
}
