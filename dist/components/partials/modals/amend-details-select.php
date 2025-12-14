<?php // if ($path == '/views/projects/site/summary.php') { ?>
    <!--begin::Modal-->
    <div class="modal fade" tabindex="-1" id="amend-details-select" style="overflow-y: scroll !important; ">
        <div class="modal-dialog modal-dialog-centered mw-700px">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header py-3 d-flex justify-content-between">
                    <!--begin::Modal title-->
                    <h2>Butiran Maklumat Pindaan</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->

                <form id="amendDetailsForm" action="#" method="POST">
                    <!--begin::Modal body-->
                    <div class="modal-body">
                        <div class="onboarding-content mb-0">
                            <!-- <h4 class="onboarding-title text-body">Maklumat Pembetulan </h4> -->
                            <div class="onboarding-info">Sila masukkan butiran pembetulan yang lengkap bagi memudahkan
                                pemohon untuk mengemaskini.</div>

                            <div class="mb-5 mt-5 fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Label-->
                                    <div class="fw-semibold me-5">
                                        <label class="form-label">1. Senarai Ulasan melibatkan Pindaan</label>
                                    </div>
                                    <!--end::Label-->
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-3">
                                            <?php
                                            $reviewMerged = array_merge($reviewCol, $reviewColPic);
                                            foreach ($reviewMerged as $textReview) {
                                                ?>
                                                <label class="form-check form-check-custom form-check-solid mb-2">
                                                    <input class="form-check-input h-20px w-20px" type="checkbox"
                                                        name="amendLists[]" value="<?php echo $textReview["id"]; ?>"
                                                        data-summary="amendLists" />
                                                    <span class="form-check-label truncate-text"
                                                        style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                                        <?php echo $textReview["description"]; ?>
                                                    </span>
                                                </label>

                                            <?php } ?>
                                            <!-- <label class="required form-label">Catatan</label>
                                    <textarea class="form-control" placeholder="Catatan" name="notes" rows="3" required></textarea> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--begin::Input group-->
                            <div class="mb-5 mt-5 fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Label-->
                                    <div class="fw-semibold me-5">
                                        <label class="form-label">2. Maklumat Permohonan</label>
                                    </div>
                                    <!--end::Label-->
                                </div>

                                <!--begin::Checkboxes-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="app_infos[]"
                                            value="provider" />
                                        <span class="form-check-label">Penyedia Utiliti</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="app_infos[]"
                                            value="title" />
                                        <span class="form-check-label">Tajuk</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="app_infos[]"
                                            value="info" />
                                        <span class="form-check-label">Maklumat</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="app_infos[]"
                                            value="appl_letter" />
                                        <span class="form-check-label">Surat Permohonan</span>
                                    </label>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Checkboxes-->

                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-8 mt-3">
                                    <textarea class="form-control" placeholder="Catatan" name="notes_app_infos"></textarea>
                                </div>
                                <!--end::Input group-->
                                <!--end::Wrapper-->
                            </div>

                            <!--begin::Input group-->
                            <div class="mb-5 mt-5 fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Label-->
                                    <div class="fw-semibold me-5">
                                        <label class="form-label">3. Maklumat Pegawai</label>
                                    </div>
                                    <!--end::Label-->
                                </div>

                                <!--begin::Checkboxes-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="officer_infos[]"
                                            value="applicant" />
                                        <span class="form-check-label">Pemohon</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="officer_infos[]"
                                            value="officer" />
                                        <span class="form-check-label">Pegawai</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="officer_infos[]"
                                            value="ack_letter" />
                                        <span class="form-check-label">Surat Perakuan</span>
                                    </label>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Checkboxes-->

                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-8 mt-3">
                                    <textarea class="form-control" placeholder="Catatan"
                                        name="notes_officer_infos"></textarea>
                                </div>
                                <!--end::Input group-->
                                <!--end::Wrapper-->
                            </div>

                            <!--begin::Input group-->
                            <div class="mt-5 fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Label-->
                                    <div class="fw-semibold me-5">
                                        <label class="fs-6">4. Maklumat Jalan</label>
                                    </div>
                                    <!--end::Label-->
                                </div>

                                <!--begin::Checkboxes-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="route_infos[]"
                                            value="district" />
                                        <span class="form-check-label fw-semibold">Daerah</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="route_infos[]"
                                            value="road" />
                                        <span class="form-check-label fw-semibold">Jalan</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="route_infos[]"
                                            value="doc_technical" />
                                        <span class="form-check-label fw-semibold">Dokumen Teknikal</span>
                                    </label>
                                    <!--end::Checkbox-->
                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid me-10">
                                        <input class="form-check-input h-20px w-20px" type="checkbox" name="route_infos[]"
                                            value="plan_location" />
                                        <span class="form-check-label fw-semibold">Gambar Lokasi</span>
                                    </label>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Checkboxes-->

                                <!--begin::Input group-->
                                <div class="d-flex flex-column mb-3 mt-3">
                                    <textarea class="form-control" placeholder="Catatan"
                                        name="notes_route_infos"></textarea>
                                </div>
                                <!--end::Input group-->
                                <!--end::Wrapper-->
                            </div>

                            <input type="text" name="system-id" id="system-id" value="<?php echo $systemId; ?>" hidden>
                        </div>

                    </div>
                    <!--end::Modal body-->

                    <!--begin::Modal footer-->
                    <div class="modal-footer py-3">
                        <button type="button" id="amendCancelBtn" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="amend_sv_amendReview_submit" class="btn btn-primary me-3">Simpan</button>
                    </div>
                    <!--end::Modal footer-->
                </form>
            </div>
            <!--end::Modal content-->
        </div>
    </div>
    <!--end::Modal - Upgrade plan-->
<?php // } ?>