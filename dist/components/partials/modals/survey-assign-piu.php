<?php
require_once 'config/functions/survey.php';

function modal1($item)
{
        echo '<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-custom-' . $item->id . '" data-system-id="' . $item->system_id . '"  data-id="' . $item->id . '" data-modal="piu-modal">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h3 class="modal-title">Lantikan Kumpulan Ukur PIU</h3>';
        echo '<div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">';
        echo '<i class="fad fa-xmark fs-2"></i>';
        echo '</div>';
        echo '</div>'; // Close modal-header
        echo '<form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '">';
        echo '<div class="w-100">';
        echo '<div class="row">';
        echo '<div class="mb-10 col-md-12 fv-row">';
        echo '<label class="form-label">Sila Pilih Kategori</label>';
        echo '<select class="form-select" data-control="select2" name="survey-provider" data-placeholder="Sila pilih kategori">';
        echo '<option></option>';
        echo '<option value="inhouse">Inhouse</option>';
        echo '<option value="outsource">Outsource</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>'; // Close row
        echo '<div class="d-flex justify-content-end">';
        echo '<button data-modal-action="continue-piu" data-next-modal="modal-custom-' . $item->id . '" class="btn btn-primary">';
        echo '<span class="indicator-label"><i class="fad fa-paper-plane"></i> Seterusnya</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>'; // Close modal-body
        echo '</form>';
        echo '</div>'; // Close modal-content
        echo '</div>'; // Close modal-dialog
        echo '</div>'; // Close modal
}

function modal2($item)
{
        $option = Survey::selectUsers('team_survey') ? Survey::selectUsers('team_survey') : null;

        echo '<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-custom-' . $item->id . '-2" data-inhouse="modal-inhouse-' . $item->id . '" data-system-id="' . $item->system_id . '" data-id="' . $item->id . '">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h3 class="modal-title">Lantikan Kumpulan Ukur</h3>';
        echo '<div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">';
        echo '<i class="fad fa-xmark fs-2"></i>';
        echo '</div>';
        echo '</div>'; // Close modal-header
        echo '<form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '-2" id="form-inhouse-' . $item->id . '">';
        echo '<div class="w-100">';
        echo '<div class="row">';
        echo '<div class="mb-5 col-md-12 fv-row">';
        echo '<label class="form-label">Sila Pilih Kumpulan Ukur</label>';
        echo '<select class="form-select" data-control="select2" data-placeholder="Pilih Kumpulan" name="survey-group" data-dropdown-parent="#modal-custom-' . $item->id . '-2">';
        echo '<option></option>';
        // Check if $option is not null and iterate through it to generate options
        if ($option !== null) {
                foreach ($option as $value) {
                        // echo '<option value="' . $value->value . '">' . $value->name . '</option>';
                        $images = $value->url === NULL ? '' : 'data-select2-images="' . $value->url . '"';
                        echo '<option value="' . $value->value . '" ' . $images . '>' . $value->name . '</option>';
                }
        }
        echo '</select>';
        echo '</div>';
        echo '</div>'; // Close row
        echo '<div class="row"><div class="mb-5 col-md-12 fv-row"><label class="form-label "> Tarikh Jangkaan Mula - Tamat</label>';
        echo '<input type="text" class="form-control flatpickr-input active" name="modal-date-range" placeholder="Sila Pilih Tarikh" data-control="flatpickr" data-flatpickr-type="daterange" readonly="readonly"></div></div>';
        echo '<div class="mb-10 col-md-12 fv-row">';
        echo '<label class="form-label">Catatan</label>';
        echo '<textarea class="form-control" name="notes" rows="3"></textarea>';
        echo '</div>';
        echo '</div>';
        echo '<div class="d-flex justify-content-end">';
        echo '<button type="submit" id="submit-confirm-inhouse-' . $item->id . '" class="btn btn-primary">';
        echo '<span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>';
        echo '<span class="indicator-progress">Sila Tunggu...';
        echo '<span class="spinner-border sp inner-border-sm align-middle ms-2"></span>';
        echo '</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>'; // Close modal-body
        echo '</form>';
        echo '</div>'; // Close modal-content
        echo '</div>'; // Close modal-dialog
        echo '</div>'; // Close modal        
}
function modal3($item)
{
        $option = Survey::selectUsers('surveyor') ? Survey::selectUsers('surveyor') : null;
        echo '<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-custom-' . $item->id . '-3" data-outsource="modal-outsource-' . $item->id . '" data-system-id="' . $item->system_id . '" data-id="' . $item->id . '">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h3 class="modal-title">Outsource</h3>';
        echo '<div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">';
        echo '<i class="fad fa-xmark fs-2"></i>';
        echo '</div>';
        echo '</div>'; // Close modal-header
        echo '<form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '-3" id="form-outsource-' . $item->id . '">';
        echo '<div class="w-100">';
        echo '<div class="row">';
        echo '<div class="mb-5 col-md-12 fv-row">';
        echo '<label class="form-label">Sila Pilih Nama Juru Ukur</label>';
        echo '<select class="form-select" data-control="select2" data-placeholder="Pilih Juru Ukur" name="survey-outsource" data-dropdown-parent="#modal-custom-' . $item->id . '-3">';
        echo '<option></option>';
        if ($option !== null) {
                foreach ($option as $value) {
                        // echo '<option value="' . $value->value . '">' . $value->name . '</option>';
                        $images = $value->url === NULL ? '' : 'data-select2-images="' . $value->url . '"';
                        echo '<option value="' . $value->value . '" ' . $images . '>' . $value->name . '</option>';
                }
        }
        echo '</select>';
        echo '</div>';
        echo '</div>'; // Close row
        echo '<div class="row"><div class="mb-5 col-md-12 fv-row"><label class="form-label "> Tarikh Jangkaan Mula - Tamat</label>';
        echo '<input type="text" class="form-control flatpickr-input active" name="modal-date-range2" placeholder="Sila Pilih Tarikh" data-control="flatpickr" data-flatpickr-type="daterange" readonly="readonly"></div></div>';
        echo '<div class="mb-10 col-md-12 fv-row">';
        echo '<label class="form-label">Catatan</label>';
        echo '<textarea class="form-control" name="notes" rows="3"></textarea>';
        echo '</div>';
        echo '</div>';
        echo '<div class="d-flex justify-content-end">';
        echo '<button type="submit" id="submit-confirm-outsource-' . $item->id . '" class="btn btn-primary">';
        echo '<span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>';
        echo '<span class="indicator-progress">Sila Tunggu...';
        echo '<span class="spinner-border sp inner-border-sm align-middle ms-2"></span>';
        echo '</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>'; // Close modal-body
        echo '</form>';
        echo '</div>'; // Close modal-content
        echo '</div>'; // Close modal-dialog
        echo '</div>'; // Close modal
}

function modal4($item)
{
        echo '<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-custom-' . $item->id . '-4" data-outsource="modal-outsource-' . $item->id . '" data-system-id="' . $item->system_id . '" data-id="' . $item->id . '">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h3 class="modal-title">Outsource</h3>';
        echo '<div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">';
        echo '<i class="fad fa-xmark fs-2"></i>';
        echo '</div>';
        echo '</div>'; // Close modal-header
        echo '<form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '-3" id="form-outsource-' . $item->id . '">';
        echo '<div class="w-100">';
        echo '<div class="mb-10 col-md-12 fv-row">';
        echo '<label class="required form-label">Catatan</label>';
        echo '<textarea class="form-control" name="notes" rows="3"></textarea>';
        echo '</div>';
        echo '</div>';
        echo '<div class="d-flex justify-content-end">';
        echo '<button type="submit" id="submit-confirm-outsource-' . $item->id . '" class="btn btn-primary">';
        echo '<span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>';
        echo '<span class="indicator-progress">Sila Tunggu...';
        echo '<span class="spinner-border sp inner-border-sm align-middle ms-2"></span>';
        echo '</span>';
        echo '</button>';
        echo '</div>';
        echo '</div>'; // Close modal-body
        echo '</form>';
        echo '</div>'; // Close modal-content
        echo '</div>'; // Close modal-dialog
        echo '</div>'; // Close modal
}

foreach ($data as $item) {
        // var_dump($item->id);
        if ($item->status_id === 41) {
                modal1($item);
                modal2($item);
                // modal3($item);
                modal4($item);
        }
}

?>