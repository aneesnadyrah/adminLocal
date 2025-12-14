<?php
$Role = new Roles();
$List = new Lists();
$username = $_SESSION['username'];
$roleId = $_SESSION['roleId'];
?>
<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <i class="fad fa-search position-absolute ms-4"></i>
                <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14"
                    placeholder="Carian Permohonan..." />
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <!--begin::Flatpickr-->
            <div class="input-group w-250px">
                <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pilih Julat Tarikh"
                    id="table-date-range" />
                <button class="btn btn-icon btn-light" date-range-clear>
                    <i class="fad fa-xmark fs-4"></i>
                </button>
            </div>
            <!--end::Flatpickr-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <table class="table table-row-dashed align-middle fs-6 gy-5" id="sitevisit-table">

            <!--begin::Table head-->
            <thead class="text-gray-400 fw-bold fs-7 text-uppercase">
                <!--begin::Table row-->
                <tr>
                    <th class="text-center">No Rujukan</th>
                    <th class="text-center">Penyedia Utiliti</th>
                    <th class="text-center">Pihak Berkuasa</th>
                    <th class="text-center">Daerah</th>
                    <th class="text-center">Tarikh</th>
                    <th class="text-center">Masa</th>
                    <th class="text-center">Lokasi</th>
                    <th class="text-center">Nota</th>
                </tr>
                <!--end::Table row-->
            </thead>
            <tbody class="text-gray-700 text-center"></tbody>
            <!--end::Table head-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<script>
    var data  = <?= $data ?>;
</script>