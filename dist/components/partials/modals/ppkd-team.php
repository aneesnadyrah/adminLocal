<?php

$state = $system->App->state;

foreach (TeamPPKD::PPKDTeamModal() as $id) {
    // $options = TeamPPKD::selectPPKDEdit('project', $id['id']);
    $options = TeamPPKD::selectPPKDEdit('project');
    $optionsDistrict = TeamPPKD::selectDistrictEdit($state, $id['id']);
    echo '<!--begin::Modal-->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="ppkd-action-edit-' . $id["id"] . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Kemaskini Zon ' . $id['name'] . '</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form class="modal-body" novalidate="novalidate" id="form-ppkd-team-' . $id["id"] . '" data-form="form-ppkd-team-' . $id["id"] . '">
                    <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                        <select id="selection-ppkd-edit-zone-' . $id["id"] . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3 mb-2" multiple="multiple"  data-control="select2" name="ppkd-edit-zone">';
                            foreach ($optionsDistrict as $option) {
                                echo '<option value="' . $option->district_code . '">' . $option->district_name . '</option>';
                            }
                            echo '</select>
                        <label for="selection-ppkd-edit-zone" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Daerah</label>
                    </div>  
                    <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                        <select id="selection-ppkd-team-' . $id["id"] . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3 mb-2" multiple="multiple"  data-control="select2" name="ppkd-team-edit">';
                        foreach ($options as $option) {
                            $description = !isset($option->description) ? '' : 'data-select2-desc="' . $option->description . '"';
                            $images = $option->url === NULL ? '' : 'data-select2-images="' . $option->url . '"';
                            echo '<option value="' . $option->value . '" ' . $description . ' ' . $images . '>' . $option->name . '</option>';
                        }
                        echo '</select>
                        <label for="selection-ppkd-team" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Ahli Kumpulan</label>
                    </div>  
                    <input type="text" name="row-id"  value="' . $id["id"] . '" hidden>
                    <input type="text" name="zone-id"  value="' . $id["id"] . '" hidden>
                    <!--begin::Submit button-->
                    <div class="d-flex justify-content-end">
                        <button type="submit" id="submit-ppkd-team-' . $id["id"] . '" data-row-id="' . $id["id"] . '" class="btn btn-primary">
                            <!--begin::Indicator label-->
                            <span class="indicator-label"><i class="fad fa-circle-check"></i> Kemaskini</span>
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
}
