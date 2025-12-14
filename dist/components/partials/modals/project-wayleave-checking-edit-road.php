<div class="modal fade" id="kt_roads_modal" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-850px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header justify-content-between">
                <h3 class="modal-title">Kemaskini Maklumat Jalan</h3>
                <div class="d-flex">
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-color-gray-500 btn-active-icon-primary" data-bs-toggle="tooltip"
                        title="Tutup Butiran Jalan" data-bs-dismiss="modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                            <span class="far fa-xmark" style="font-size: 18px;"></span>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
            </div>
            <div class="modal-body pt-0 pb-8 px-lg-15 m-3" id="road-modal-body">
                <!--begin::Repeater-->
                <div class="d-flex flex-column flex-root">
                    <div id="road-involved"></div>
                </div>
                <!--end::Repeater-->
            </div>
        </div>
    </div>
</div>