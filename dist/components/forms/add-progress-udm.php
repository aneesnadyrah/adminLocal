<!--begin::Form-->
<form id="add_progress" class="form d-flex flex-column flex-lg-row">
    <input type="text" name="progress-udm" value="1" hidden />
    <input type="text" name="system-id" value="<?php echo $p[0]['system_id']; ?>" hidden />
    <!--begin::Aside column-->
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 mw-lg-350px mb-7 me-lg-10">
        <!--begin::Thumbnail settings-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h3>Penyedia Utiliti</h3>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body text-center pt-0">
                <div class="d-flex flex-column align-items-center">
                    <div class="col-12">
                        <div class="symbol symbol-100px rounded-circle overflow-hidden">
                            <img src="<?php echo General::getProvider($p[0]['ProviderID'])->logo;?>" />
                        </div>
                    </div>
                    <div class="col-12">
                        <!--begin::Input group-->
                        <span class="fw-semibold fs-6 text-gray-700">
                            <?php echo $p[0]['Provider']; ?>
                        </span>
                        <!--end::Input group-->
                        <!-- Add space between the two spans -->
                        <div style="margin-bottom: 5px;"></div>
                        <!--begin::Input group-->
                        <span class="fw-semibold fs-6 text-gray-700">
                            <?php echo $p[0]['RefNo']; ?>
                        </span>
                        <!--end::Input group-->
                        <input type="hidden" id="RefNo" name="noRef" value="<?php echo $p[0]['RefNo']; ?>">
                    </div>
                </div>
            </div>
            <!--end::Card body-->
            <!--begin::Progress-->
            <div class="d-flex align-items-center w-300px w-sm-260px flex-column ms-10">
                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                    <span class="fw-semibold fs-6 text-gray-600">Progress Jarak <?php echo $c ?> / <?php echo $p[0]['sitevisit_length']; ?></span>
                    <span class="fw-bold fs-6"><?php echo round(($c / $p[0]['sitevisit_length']) * 100); ?>%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: <?php echo round(($c / $p[0]['sitevisit_length']) * 100); ?>%;" aria-valuenow="<?php echo round(($c / $p[0]['sitevisit_length']) * 100); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    <span class="progress-value"><?php echo $c ?></span>
                </div>
                <input type="text" name="application-length" value="<?php echo $p[0]['sitevisit_length']; ?>" hidden />
                <input type="text" name="current-progress" value="<?php echo $c ?>" hidden />
            </div>
            <!--end::Progress-->
        </div>
        <!--end::Thumbnail settings-->
    </div>
    <!--end::Aside column-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <div class="d-flex flex-column gap-7 gap-lg-10 mb-5">
            <!--begin::Kemaskini Laporan Harian-->
            <div class="card card-flush py-4">
                <!--begin::Card header-->
                <div class="card-header">
                    <div class="card-title">
                        <h2>Kemaskini Laporan Harian</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-7 fv-row" id="survey-team">
                            <!--begin::Label-->
                            <label class="form-label">Kumpulan Ukur</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <!--begin::Select-->
                            <select class="form-select mb-2" name="survey-team" data-control="select2" data-hide-search="true" data-placeholder="Pilih Kumpulan Ukur">
                                Kumpulan <?php echo $option; ?>
                            </select>
                            <!--end::Select-->
                            <!--end::Description-->
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-10 fv-row">
                            <label class="required form-label">Masa Mula</label>
                            <input type="text" name="start-time" class="form-control" placeholder="Masa Tamat" value="<?php echo $start; ?>" id="start-time" />
                        </div>
                        <div class="col mb-10 fv-row">
                            <label class="required form-label">Masa Tamat</label>
                            <input type="text" name="end-time" class="form-control" placeholder="Masa Tamat" value="<?php echo $end; ?>" id="end-time" />
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-2 fv-row">
                            <label class="required form-label">Jarak Harian</label>
                            <input type="text" name="daily-progress" class="form-control" placeholder="Jarak Harian" />
                        </div>
                        <div class="col mb-2 fv-row">
                            <label for="date" class="form-label">Tarikh</label>
                            <input type="date" name="date" class="form-control" placeholder="Pilih Tarikh" id="date" />
                        </div>
                    </div>
                    <!--end::Input group-->
                    <div class="form-check mb-10">
                        <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" name="jarak_pengukuran" />
                        <label class="form-check-label" for="flexCheckDefault">
                            Jarak Pengukuran < Jarak Permohonan
                        </label>
                    </div>
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-10 fv-row">
                            <label class="required form-label">Jarak Pancangan (Pegging)</label>
                            <input type="text" name="pegging-distance" class="form-control" placeholder="Jarak Menambat" />
                        </div>
                        <div class="col mb-10 fv-row">
                            <label class="required form-label">Jarak Pengesanan (Detection)</label>
                            <input type="text" name="detection-distance" class="form-control" placeholder="Jarak Pengesanan" />
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col-md-6 mb-10 fv-row">
                            <label class="form-label required">Koordinat Mula</label>
                            <input type="text" name="start-coord" class="form-control" placeholder="5.32891701, 103.14635436">
                        </div>
                        <div class="col-md-6 mb-10 fv-row">
                            <label class="form-label required">Koordinat Akhir</label>
                            <input type="text" name="end-coord" class="form-control" placeholder="5.33046594, 103.14851092">
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-2 fv-row">
                            <!--begin::Label-->
                            <label class="form-label">Catatan Laporan Hari Ini</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                             <textarea name="notes" class="form-control" placeholder="Catatan" rows="2"></textarea>
                            <!--end::Description-->
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Card header-->
            </div>
            <!--end::Kemaskini Laporan Harian-->
            <!--start::Kerja-kerja yang Dilakukan Di Lapangan-->
            <div class="d-flex flex-column gap-7 gap-lg-10 mb-5">
                <div class="d-flex justify-content-end">
                    <!--begin::Button-->
                    <a href="/dashboard" class="btn btn-light me-5">Batal</a>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <!-- <a href="/tasks/new" class="btn btn-primary me-5">Simpan</a> -->
                    <button type="submit" id="add_progress_submit" class="btn btn-primary">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">Sila Tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
            </div>
            <!--end::Kerja-kerja yang Dilakukan Di Lapangan-->

        </div>
    </div>
    <!--end::Main column-->

</form>
<!--end::Form-->