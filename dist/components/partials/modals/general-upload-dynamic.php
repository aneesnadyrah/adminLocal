<?php

$listModal = OldModal::Task();
foreach ($listModal as $data) {
    echo '<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $data['StatusID'] . $data['ID'] . '">';
    echo '<div class="modal-dialog modal-dialog-centered">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h3 class="modal-title">' . $data['ModalTitle'] . '</h3>';
    echo '<div class="btn btn-icon btn-sm btn-active-light-' . $data['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">';
    echo '<i class="fad fa-xmark fs-2"></i>';
    echo '</div>';
    echo '</div>';
    echo '<form class="modal-body" novalidate="novalidate" id="form-' . $data['StatusID'] . $data['ID'] . '">';
    echo '<div class="fv-row mb-2">';
    foreach ($data['FileID'] as $file) {
        echo '<div class="dropzone border-' . $data['StatusColor'] . ' bg-light-' . $data['StatusColor'] . '">';
        echo '<div class="dz-message needsclick">';
        echo '<i class="fa-duotone fa-file-arrow-up text-' . $data['StatusColor'] . ' fs-3x"></i>';
        echo '<div class="ms-4">';
        echo '<h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muatnaik dokumen</h3>';
        echo '<span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Sebut Harga</span>';
        echo '</div>';
        echo '</div>';
        echo '<input type="text" id="sid-' . $data['StatusID'] . $data['ID'] . '" name="system-id" value="' . $data['SysID'] . '" hidden>';
        echo '<input type="text" id="f-' . $data['StatusID'] . $data['ID'] . '" name="folder" value="' . $file . '" hidden>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen SH telah disatukan dalam bentuk format pdf</em></div>';
    foreach ($data['FileInput'] as $item) {
        $input = explode('_', $item);

        if ($input[1] == 'ltr-created') {
            $label = 'Tarikh Surat Permohonan';
        } else if ($input[1] == 'notes') {
            $label = 'Catatan';
        }

        //Sambung sini untuk

        if ($input[0] == 'dt') {
            echo '<div class="mb-5 fv-row">';
            echo '<label for="" class="form-label">' . $label . '</label>';
            echo '<input type="date" class="form-control" name="' . $item . '" placeholder="Sila Pilih Tarikh" data-control="flatpickr" />';
            echo '</div>';
        } else if ($input[0] == 'txt') {
            echo '<div class="mb-5 fv-row">';
            echo '<label for="" class="form-label">' . $label . '</label>';
            echo '<textarea class="form-control" name="' . $item . '"></textarea>';
            echo '</div>';
        }
    }

    echo '<div class="d-flex justify-content-end">';
    echo '<button type="submit" id="submit-' . $data['StatusID'] . $data['ID'] . '" class="d-none btn btn-' . $data['StatusColor'] . '">';
    echo '<span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>';
    echo '<span class="indicator-progress">Sila Tunggu...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>';
    echo '</button>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>