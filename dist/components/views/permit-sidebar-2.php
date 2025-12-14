<!--begin::Sidebar-->
<div class="flex-lg-row-fluid w-100 mw-lg-300px mw-xxl-350px">
<!-- <div class="flex-lg-auto min-w-lg-300px"> -->
    <!--begin::Card-->
    <div class="card">
        <!--begin::Header-->
        <div class="card-header d-flex align-items-center">
            <h1 class="card-title fw-bold text-dark fs-2">Tindakan</h1>
        </div>
        <!--end::Header-->

        <!--begin::Card body-->
        <div class="card-body p-10">
            <!--begin::Form-->
            <form class="modal-body" novalidate="novalidate" id="form_action_permit_checklist">
                <!--begin::Actions-->
                <div class="mb-0">
                    <!--begin::Input group-->
                    <div class="fv-row d-flex justify-content-between">
                        <label class="form-label fs-6 fw-semibold" >
                            Adakah semua dokumen telah lengkap?
                        </label>
                        <!-- <input class="form-check-input toggle-checkbox" type="checkbox" value="1" name="complete_permit" id="complete_permit"/> -->
                    </div>
                    <div class="d-flex align-items-center me-2 mb-5">
                        <!--begin::Radio-->
                        <div class="form-check form-check-custom flex-shrink-0 me-6">
                            <input class="form-check-input" type="radio" id="complete_permit" name="complete_permit" value="1"/>
                            <label class="form-check-label" for="complete_permit">
                                Lengkap
                            </label>
                        </div>
                        <!--end::Radio-->
                        <!--begin::Radio-->
                        <div class="form-check form-check-custom flex-shrink-0 me-6">
                            <input class="form-check-input" type="radio" id="incomplete_permit" name="complete_permit" value="0"/>
                            <label class="form-check-label" for="incomplete_permit">
                                Tidak Lengkap
                            </label>
                        </div>
                        <!--end::Radio-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="fv-row mb-15">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-semibold mb-2">Catatan</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <textarea class="form-control form-control-solid" rows="4" name="notes" placeholder="Catatan"></textarea>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->

                    <input type="text" name="system-id" value=<?php echo $_GET['sid'] ?> hidden/>

                    <!--begin::Submit Button-->
                    <button type="submit" id="submit_action_permit_checklist" class="btn btn-primary">
                        <!--begin::Indicator label-->
                        <span class="indicator-label"><i class="fad fa-user-pen"></i> Hantar</span>
                        <!--end::Indicator label-->
                        <!--begin::Indicator progress-->
                        <span class="indicator-progress">Sila Tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                        <!--end::Indicator progress-->
                    </button>
                    <!--end::Submit Button-->
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
<!--end::Sidebar-->