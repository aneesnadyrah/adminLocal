<?php

foreach (Tasking::taskModal() as $id) {
    echo '<div class="modal fade" id="modal-'. $id['StatusID'].$id['ID'] .'" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Tambah Catatan Permohonan</h2>
                <!--end::Modal title-->

                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fad fa-close fs-3"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body py-lg-10 px-lg-10">
                <div class="form-floating border rounded">
                    <select class="form-select form-select-lg form-select-solid" placeholder="Pilih No Rujukan" id="kt_docs_select2_country">
                        <option></option>
                        <option value="AF" data-kt-select2-country="assets/media/flags/afghanistan.svg">Afghanistan</option>
                        <option value="AX" data-kt-select2-country="assets/media/flags/aland-islands.svg">Aland Islands</option>
                    </select>
                    <label for="kt_docs_select2_country">Pilih No Rujukan</label>
                </div>

            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>';
}

?>


