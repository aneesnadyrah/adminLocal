<?php
$options = TeamPPKD::selectPPKDAdd('project');
$state = $system->App->state;
$optionsDistrict = TeamPPKD::selectDistrictAdd($state);

echo '<!--begin::Modal-->
<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="ppkd-add-team">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Kumpulan PPKD</h3>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fad fa-xmark fs-2"></i>
                </div>
                <!--end::Close-->
            </div>
            <form class="modal-body" novalidate="novalidate" id="form-ppkd-add-team" data-form="form-ppkd-add-team" >
                <!--begin::Input group-->
                <div class="form-floating mb-10 fv-row">
                    <input type="team-name" class="form-control form-control-solid rounded rounded-end-0 mt-3 mb-2" id="floatingInput1" name="ppkd-team-name" />
                    <label for="floatingInput1" class="fs-5 fw-semibold form-label mb-0">Nama Kumpulan</label>
                </div>
                <!--end::Input group-->
                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                    <select id="selection-ppkd-add-zone" class="form-select form-select-transparent rounded rounded-end-0 mt-3 mb-2" multiple="multiple"  data-control="select2" name="ppkd-add-zone">';
                    foreach ($optionsDistrict as $option) {
                        echo '<option value="' . $option->district_code . '">' . $option->district_name . '</option>';
                    }
                    echo '</select>
                    <label for="selection-ppkd-add-zone" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Daerah</label>
                </div>  
                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                    <select id="selection-ppkd-add-team" class="form-select form-select-transparent rounded rounded-end-0 mt-3 mb-2" multiple="multiple"  data-control="select2" name="ppkd-add-team">';
                    foreach ($options as $option) {
                        $description = !isset($option->description) ? '' : 'data-select2-desc="' . $option->description . '"';
                        $images = $option->url === NULL ? '' : 'data-select2-images="' . $option->url . '"';
                        echo '<option value="' . $option->value . '" ' . $description . ' ' . $images . '>' . $option->name . '</option>';
                    }
                    echo '</select>
                    <label for="selection-ppkd-add-team" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Ahli Kumpulan</label>
                   
                </div>   
                <input type="hidden" id="state" name="state" value="' . $state . '">
                <!--begin::Submit button-->
                <div class="d-flex justify-content-end">
                    <button type="submit" id="submit-ppkd-add-team" class="d-none btn btn-primary">
                        <!--begin::Indicator label-->
                        <span class="indicator-label"><i class="fad fa-circle-check"></i>Tambah</span>
                        <!--end::Indicator label-->
                        <!--begin::Indicator progress-->
                        <span class="indicator-progress">Sila Tunggu...
                            <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
                        </span>
                        <!--end::Indicator progress-->
                    </button>
                </div>
                <!--end::Submit button-->
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->';
