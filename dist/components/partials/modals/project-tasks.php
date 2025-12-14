<?php

foreach ($data as $item) {
    $size = isset($item->aside) ? "modal-lg" : "";
    $visible = isset($item->input->upload) ? 'd-none' : '';

    echo '<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '">';
    echo '<div class="modal-dialog modal-dialog-centered ' . $size . '">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h3 class="modal-title">' . $item->title . '</h3>';
    echo '<div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">';
    echo '<i class="fad fa-xmark fs-2"></i>';
    echo '</div>';
    echo '</div>';
    echo '<form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '">';
    echo '<div class="d-flex flex-row">';
    if (isset($item->aside)) {
        $attachment = $item->aside;
        if ($attachment->url !== NULL && $attachment->url !== "") {
            $url = $attachment->type === 'attachment' ? "/attachments/view?url=$attachment->url&mime=$attachment->mime " : "$attachment->url";
            echo '<div class="min-w-450px h-500px me-5">';
            echo '<iframe title="' . $item->aside->type . '" src="' . $url . '" class="w-100 h-100 rounded"></iframe>';
            echo '</div>';
        } else {
            echo '<div class="d-flex flex-column align-items-center justify-content-center w-100 h-400px me-5">';
            echo '<img class="w-100" src="/assets/media/illustrations/empty/Files.svg" />';
            echo '<h5 class="fw-semibold text-gray-600">Lampiran Tidak Dijumpai...</h5>';
            echo '</div>';
        }
    }

    echo '<div class="w-100">';
    if (isset($item->input->upload)) {
        echo '<div class="fv-row mb-5">';
        foreach ($item->input->upload as $upload) {
            echo '<div data-upload="' . $upload->folder . '-' . $item->id . '" class="dropzone border-' . $item->color . ' bg-light-' . $item->color . '" data-type="' . $upload->type . '">';
            echo '<div class="dz-message needsclick">';
            echo '<i class="fad fa-file-arrow-up text-' . $item->color . ' fs-3x"></i>';
            echo '<div class="ms-4">';
            echo '<h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muatnaik dokumen</h3>';
            echo '<span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen ' . $upload->label . '</span>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="text-muted fs-7 required mb-3"><em>Pastikan dokumen telah disatukan dalam bentuk format ' . $upload->type . '</em></div>';
        }
        echo '</div>';
    }

    if (isset($item->input->checkbox)) {
        echo '<div class="row">';
        if (isset($item->input->checkbox_title)) {
            echo '<label class="form-label">' . $item->input->checkbox_title . '</label>';
        }
        foreach ($item->input->checkbox as $index => $checkbox) {
            $required = isset($checkbox->required) ? 'required' : '';
            echo $item->input->checkbox->$index->type === "switch" ? '<div class="mb-5 col-md-12 fv-row">' : '<div class="mb-5 col-md-4 fv-row">';
            if ($item->input->checkbox->$index->type === "checkbox" || $item->input->checkbox->$index->type === "radio") {
                echo '<div class="d-flex justify-content-evenly">';
                echo '<div class="form-check form-check-custom form-check-solid me-5">';
                echo '<input class="form-check-input" type="' . $checkbox->type . '" value="' . $checkbox->value . '" name="' . $checkbox->name . '" id="checkbox-' . $item->id . '"/>';
                echo '<label class="form-check-label  text-gray-700" for="checkbox-' . $item->id . '">' . $checkbox->label . '</label>';
                echo '</div>';
                echo '</div>';
            }

            if ($item->input->checkbox->$index->type === "switch") {
                echo '<div class="d-flex justify-content-between form-check form-switch form-check-custom form-check-solid">';
                echo '<label class="form-check-label fw-semibold text-gray-800" for="switch-' . $item->id . '">' . $checkbox->label . '</label>';
                echo '<input class="form-check-input" type="checkbox" value="' . $checkbox->value . '" name="' . $checkbox->name . '" id="switch-' . $item->id . '"/>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>'; // Close the row
    }

    if (isset($item->input->select)) {
        echo '<div class="mb-3">';

        foreach ($item->input->select as $index => $select) {
            if ($item->input->select->$index->type === 'images') {
                $required = isset($select->required) ? 'required' : '';
                echo '<div class="mb-5 fv-row">';
                echo '<label for="select-' . $index . '-' . $item->id . '" class="form-label ' . $required . '">' . $select->label . '</label>';
                echo '<select class="form-select form-select-solid" data-placeholder="' . $select->placeholder . '" data-control="select2" id="select-' . $index . '-' . $item->id . '" name="' . $select->name . '" data-show-images="true" ' . $required . '>';
                echo '<option></option>';
                foreach ($select->option as $option) {
                    $description = !isset($option->description) ? '' : 'data-select2-desc="' . $option->description . '"';
                    $images = $option->url === NULL ? '' : 'data-select2-images="' . $option->url . '"';
                    echo '<option value="' . $option->value . '" ' . $description . ' ' . $images . '>' . $option->name . '</option>';
                }
                echo '</select>';
                echo '</div>';
            } else {
                echo '<div class="mb-5 fv-row">';
                echo '<label for="select-' . $index . '-' . $item->id . '" class="form-label ' . $required . '">' . $select->label . '</label>';
                echo '<select id="select-' . $index . '-' . $item->id . '" class="form-select form-select-solid" data-control="select2" name="' . $select->name . '" data-placeholder="' . $select->placeholder . '" ' . $required . '>';
                echo '<option></option>';
                foreach ($select->option as $option) {
                    echo '<option value="' . $option->value . '">' . $option->name . '</option>';
                }
                echo '</select>';
                echo '</div>';
            }
        }
        echo '</div>';
    }

    if (isset($item->input->date)) {
        echo '<div class="row">';
        $count = count(get_object_vars($item->input->date));
        $columns = $count % 2 == 0 ? 6 : 12;
        foreach ($item->input->date as $date) {
            $required = isset($date->required) ? 'required' : '';
            echo '<div class="mb-5 col-md-' . $columns . ' fv-row">';
            echo '<label class="form-label ' . $required . '">' . $date->label . '</label>';
            echo '<input type="date" class="form-control form-control-solid" name="' . $date->name . '" placeholder="Sila Pilih Tarikh" data-control="flatpickr" data-flatpickr-type="' . $date->type . '" ' . $required . '/>';
            echo '</div>';
        }
        echo '</div>'; // Close the row
    }

    if (isset($item->input->text)) {
        echo '<div class="row">';
        $count = count(get_object_vars($item->input->text));
        $columns = $count % 2 == 0 ? 6 : 12;
        foreach ($item->input->text as $text) {
            $required = isset($text->required) ? 'required' : '';
            $readonly = isset($text->readonly) ? 'readonly' : '';
            $style = isset($text->style) ? $text->style : 'solid';
            $attribute = isset($text->attribute) ? $text->attribute : '';
            echo '<div class="mb-5 col-md-' . $columns . ' fv-row">';
            echo '<label class="form-label ' . $required . '">' . $text->label . '</label>';
            echo '<input type="text" class="form-control form-control-' . $style . '" ' . $attribute . ' name="' . $text->name . '" placeholder="' . $text->label . '" ' . $required . ' ' . $readonly . ' />';
            echo '</div>';
        }
        echo '</div>'; // Close the row
    }

    if (isset($item->input->textarea)) {
        echo '<div class="row fv-row">';
        foreach ($item->input->textarea as $textarea) {
            echo '<div class="mb-5 col-md-12">';
            echo '<label class="form-label">' . $textarea->label . '</label>';
            echo '<textarea class="form-control form-control-solid" name="' . $textarea->name . '"></textarea>';
            echo '</div>';
        }
        echo '</div>'; // Close the row
    }

    echo '<div class="d-flex justify-content-end">';
    echo '<button type="submit" data-submit="submit-' . $item->id . '" class="w-100 btn btn-' . $item->color . ' ' . $visible . '">';
    echo '<span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>';
    echo '<span class="indicator-progress">Sila Tunggu...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>