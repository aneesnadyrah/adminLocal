<?php

foreach ($data as $item) {
    $value = SurveyApi::getUploadData($id->system_id);
    $visible = isset($item->input->upload) ? 'd-none' : ''; 
    echo '<!--begin::Modal-->  
    <div class="modal fade" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Serahan PIU & PPT</h3>

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
                                                    Pelan
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
                                                    Muatnaik
                                                </h3>
                                                <div class="stepper-desc" style="white-space: nowrap;">
                                                    Kerja Pelan
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
                                    <div class="mb-10 fv-row ms-10">
                                        <label class="fs-5 fw-semibold form-label mb-0">Sila Pilih Jenis Pelan</label>
                                        <div class="form-check mt-5">
                                            <input class="form-check-input" type="checkbox" name="selection-plan" id="udm-checkbox" value="1" ' . ($value === 1 ? 'checked disabled' : '') . '>
                                            <label class="form-check-label" for="checkbox-1-' . $item->id . '">Pelan Infrastruktur Utiliti</label>
                                        </div>
                                        <div class="form-check mt-5">
                                            <input class="form-check-input" type="checkbox" name="selection-plan" id="tmp-checkbox" value="2" ' . ($value === 0 || $value === 2 ? 'disabled' : '') . '>
                                            <label class="form-check-label" for="checkbox-2-' . $item->id . '">Pelan Kawalan Trafik</label>
                                        </div>
                                    </div>
                                        <input type="text" id="sid-' . $item->id . '" name="system-id" value="'. $id->system_id .'" hidden/>
                                        <input type="text" id="check-' . $item->id . '" name="check" value="'. $value .'" hidden/>
                                        <input type="hidden" id="selected-value" name="selected-value" value="">
                                </div>
                                <!--end::Step 1-->

                                <!--begin::Step 2-->
                                ';  
                                if ($value === 0 || $value === 2) {  
                                    echo '
                                    <div class="flex-column" data-kt-stepper-element="content">
                                        <!--begin::Dropzone-->
                                        <div class="dropzone border-primary bg-light-primary mb-7 pdf-dropzone">
                                            <!--begin::Message-->
                                            <div class= "dz-message needsclick">
                                                <!--begin::Icon-->
                                                <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                                <!--end::Icon-->
                                                <!--begin::Info-->
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf sahaja</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <input type="text" id="sid-' . $item->id . '" name="system-id" value="'. $id->system_id .'" hidden/>
                                            <input type="text" id="f-' . $item->id . '" name="folder" value="PIU" hidden>
                                            <input type="text" class="form-control form-control-solid" id="udm" name="udm" value="udm" hidden/>
                                            <input type="text" id="upload-' . $item->id . '" name="upload-status" value="1" hidden/>
                                        </div>
                                        <!--end::Dropzone-->
                                        <!--begin::Dropzone-->
                                        <div class="dropzone border-success bg-light-success dwg-dropzone">
                                            <!--begin::Message-->
                                            <div class= "dz-message needsclick">
                                                <!--begin::Icon-->
                                                <i class="fa-duotone fa-compass-drafting text-success fs-3x"></i>
                                                <!--end::Icon-->
                                                <!--begin::Info-->
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .dwg sahaja</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                        </div>
                                        <!--end::Dropzone-->
                                    </div>
                                '; 
                            } else if ($value === 1) { 
                                echo '
                                    <div class="flex-column" data-kt-stepper-element="content">
                                        <!--begin::Dropzone-->
                                        <div class="dropzone border-primary bg-light-primary mb-7 pdf-dropzone">
                                            <!--begin::Message-->
                                            <div class= "dz-message needsclick">
                                                <!--begin::Icon-->
                                                <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                                <!--end::Icon-->
                                                <!--begin::Info-->
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf sahaja</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <input type="text" id="sid-' . $item->id . '" name="system-id" value="'. $id->system_id .'" hidden/>
                                            <input type="text" id="f-' . $item->id . '" name="folder" value="PPT" hidden>
                                            <input type="text" class="form-control form-control-solid" id="tmp" name="tmp" value="tmp" hidden/>
                                            <input type="text" id="upload-' . $item->id . '" name="upload-status" value="2" hidden/>
                                        </div>
                                        <!--end::Dropzone-->
                                        <!--begin::Dropzone-->
                                        <div class="dropzone border-success bg-light-success dwg-dropzone">
                                            <!--begin::Message-->
                                            <div class= "dz-message needsclick">
                                                <!--begin::Icon-->
                                                <i class="fa-duotone fa-compass-drafting text-success fs-3x"></i>
                                                <!--end::Icon-->
                                                <!--begin::Info-->
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .dwg sahaja</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                        </div>
                                        <!--end::Dropzone-->
                                    </div>
                                ';
                            } else {
                            } 
                            echo '
                            <!--end::Step 2--> 

                            </div>
                            <!--end::Group-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-stack">
                                <!--begin::Wrapper-->
                                <div class="d-flex justify-content-start my-5">
                                    <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                                        Kembali
                                    </button>
                                </div>
                                <!--end::Wrapper-->

                                <!--begin::Submit button-->
                                <div class="d-flex justify-content-end my-5">
                                    <button type="submit" data-submit="submit-' . $item->id . '" class="w-100 btn btn-' . $item->color . ' ' . $visible . '"  data-kt-stepper-action="submit">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label"><i class="fad fa-arrow-up-from-bracket"></i> Muatnaik</span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">Sila Tunggu...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
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

?>