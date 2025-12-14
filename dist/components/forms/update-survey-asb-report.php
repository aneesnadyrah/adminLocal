<!--begin::Form-->
<form id="report_edit" class="form d-flex flex-column flex-lg-row">
    <input type="text" name="report-asb-edit" value="1" hidden />
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
                <input type="text" name="application-length" value="<?php echo $p[0]['Length']; ?>" hidden />
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
                        <h2>Kemaskini Laporan Akhir</h2>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-light-primary btn-active-primary w-40px w-md-auto h-40px px-0 px-md-6" onclick="window.location.href=`/surveys/site/reportUdm/<?php echo $p[0]['system_id']; ?>`">
                            <i class="fad fa-file-text fs-4"></i>
                            <span class="d-none d-md-inline">Lihat Laporan</span>
                        </button>
                    </div>
                </div>
                    
                <!--end::Actions-->
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
                            <input type="text" name="survey-name" class="form-control" value="<?php echo $team['team_name']; ?>" disabled />
                            <!--end::Input-->
                        </div>
                    </div>
                    <input type="text" name="survey-team" value="<?php echo $team['team_id']; ?>" hidden />
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-7 fv-row">
                            <label class="form-label">Tarikh Mula</label>
                            <input type="text" name="start-time" class="form-control flatpickr-input" placeholder="Pilih Tarikh" id="dateStart" value="<?php echo htmlspecialchars($startDate); ?>" data-date="<?php echo htmlspecialchars($startDate); ?>" disabled />
                        </div>
                        <div class="col mb-7 fv-row">
                            <label class="form-label">Tarikh Tamat</label>
                            <input type="text" name="end-time" class="form-control flatpickr-input" placeholder="Pilih Tarikh" id="dateEnd" value="<?php echo htmlspecialchars($endDate); ?>" data-date="<?php echo htmlspecialchars($endDate); ?>" disabled />
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-7 fv-row">
                            <label class="form-label">Jarak Mohon</label>
                            <input type="text" name="daily-progress" class="form-control" placeholder="Jarak Mohon" value="<?php echo $p[0]['Length']; ?> m" disabled />
                        </div>
                        <div class="col mb-7 fv-row">
                            <label class="form-label">Jumlah Jarak Diukur</label>
                            <input type="text" name="daily-progress" class="form-control" placeholder="Jarak Diukur" value="<?php echo $c ?> m" disabled />
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col mb-7 fv-row">
                            <label class="form-label">Jumlah Jarak Pancangan (Pegging)</label>
                            <input type="text" name="pegging-distance" class="form-control" placeholder="Jarak Menambat" value="<?php echo $totalDistance['total_pegging'] ?>" disabled />
                        </div>
                        <div class="col mb-7 fv-row">
                            <label class="form-label">Jumlah Jarak Pengesanan (Detection)</label>
                            <input type="text" name="detection-distance" class="form-control" placeholder="Jarak Pengesanan" value="<?php echo $totalDistance['total_detection'] ?>" disabled />
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col-md-6 mb-7 fv-row">
                            <label class="form-label required">Koordinat Mula</label>
                            <input type="text" name="start-coord" class="form-control" placeholder="5.32891701, 103.14635436" value="<?php echo $startCoor['latitude_start'] ?? 'null'; ?>, <?php echo $startCoor['longitude_start'] ?? 'null'; ?>">
                        </div>
                        <div class="col-md-6 mb-7 fv-row">
                            <label class="form-label required">Koordinat Akhir</label>
                            <input type="text" name="end-coord" class="form-control" placeholder="5.33046594, 103.14851092" value="<?php echo $endCoor['latitude_end'] ?? 'null'; ?>, <?php echo $endCoor['longitude_end'] ?? 'null'; ?>">
                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="row">
                        <div class="col-md-6 mb-7 fv-row">
                            <label class="form-label">Kerja yang dilakukan di lapangan</label>
                            <!-- <select class="form-select mb-2" name="survey-work[]" data-control="select2" data-hide-search="true" data-placeholder="Pilih Kerja yang dilakukan" data-close-on-select="false" data-allow-clear="true" multiple="multiple">
                                <option value="1">MENJALANKAN KERJA-KERJA PENGESANAN UTILITI BAWAH TANAH</option>
                                <option value="2">MENJALANLAN KERJA-KERJA PENGUKURAN TOPOGRAFI</option>
                            </select> -->

                            <div class="mb-5">
                                <div class="d-flex fv-row">
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" type="checkbox" value="1" name="survey-work[]"
                                            <?php echo in_array(1, $surveyWorkData['survey_work']) ? 'checked' : ''; ?>>
                                        <span class="form-check-label">MENJALANKAN KERJA-KERJA PENGESANAN UTILITI BAWAH TANAH</span>
                                    </label>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex fv-row">
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" type="checkbox" value="2" name="survey-work[]"
                                            <?php echo in_array(2, $surveyWorkData['survey_work']) ? 'checked' : ''; ?>>
                                        <span class="form-check-label">MENJALANKAN KERJA-KERJA PENGUKURAN TOPOGRAFI</span>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6 mb-7 fv-row">
                            <label class="form-label">Peralatan kerja yang digunakan</label>
                            <!-- <select class="form-select mb-2" name="equipment[]" data-control="select2" data-hide-search="true" data-placeholder="Pilih Peralatan kerja" data-close-on-select="false" data-allow-clear="true" multiple="multiple">
                                <option value="1">GLOBAL POSITIONING SYSTEM CHC i73 (CHC)</option>
                                <option value="2">RADIO DETECTION 8200</option>
                                <option value="3">RODA PENGUKURAN JARAK</option>
                                <option value="4">GROUND PENETRATING RADAR (GPR) MALA</option>
                            </select> -->

                            <div class="mb-5">
                                <div class="d-flex fv-row">
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" type="checkbox" value="1" name="equipment[]"
                                            <?php echo in_array(1, $surveyWorkData['equipment']) ? 'checked' : ''; ?>>
                                        <span class="form-check-label">GLOBAL POSITIONING SYSTEM CHC i73 (CHC)</span>
                                    </label>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex fv-row">
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" type="checkbox" value="2" name="equipment[]"
                                            <?php echo in_array(2, $surveyWorkData['equipment']) ? 'checked' : ''; ?>>
                                        <span class="form-check-label">RADIO DETECTION 8200</span>
                                    </label>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex fv-row">
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" type="checkbox" value="3" name="equipment[]"
                                            <?php echo in_array(3, $surveyWorkData['equipment']) ? 'checked' : ''; ?>>
                                        <span class="form-check-label">RODA PENGUKURAN JARAK</span>
                                    </label>
                                </div>
                                <div class="separator separator-dashed my-3"></div>
                                <div class="d-flex fv-row">
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" type="checkbox" value="4" name="equipment[]"
                                            <?php echo in_array(4, $surveyWorkData['equipment']) ? 'checked' : ''; ?>>
                                        <span class="form-check-label">GROUND PENETRATING RADAR (GPR) MALA</span>
                                    </label>
                                </div>
                            </div>

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
                <!--begin::Add Image-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="required card-label">Muatnaik Gambar Kerja Ukur</h2>
                            <label class="form-label"><i class="fas fa-exclamation-circle fs-4" data-bs-toggle="tooltip" aria-label="Sila muatnaik gambar yang menggunakan kamera timestamp & on location" data-bs-original-title="Sila muatnaik gambar yang menggunakan kamera timestamp & on location" data-kt-initialized="1"></i></label>
                        </div>
                        <!--begin::Label-->
                        <!-- <span class="fw-semibold fs-7 text-gray-500">Sila muatnaik gambar yang menggunakan kamera timestamp & on location</span> -->
                        <!--end::Label-->
                    </div>
                    
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-3 my-0 dataTable no-footer">
                                <tbody>             
                                    <?php foreach($surveyImageData as $imageData) { ?>       
                                    <tr class="odd">
                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-750px px-7 py-3 mb-5">  
                                            <div class="symbol symbol-100px min-w-150px pe-2">                                                   
                                                <img src="<?php echo $imageData['url']; ?>" class="" alt="">                                                    
                                            </div>     
                                            <div class="min-w-275px pe-2">
                                                <span class="badge badge-light text-muted"><?php echo $imageData['description']; ?></span>
                                            </div>
                                            <div class="min-w-200px pe-2">
                                                <span class="badge badge-light-primary">Selesai muatnaik</span>
                                            </div>
                                            <!--begin::Action-->
                                            <button type="button" id="delete_button" data-row-id="<?php echo $imageData['id']; ?>" class="btn btn-icon btn-light-danger confirm-delete-btn">
                                                <i class="fad fa-trash fs-4"></i>
                                            </button> 
                                            <!--end::Action-->                                
                                        </div>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" name="surveyImageCount" id="surveyImageCount" value="<?php echo !empty($surveyImageData) ? '1' : '0'; ?>">

                    <div id="add-repeater" class="card-body pt-2">
                        <!--begin::Repeater-->
                        <div id="add-image">
                            <!--begin::Form group-->
                            <div class="form-group" id="repeater-list">
                                <div data-repeater-list="workDone-list">
                                    <div data-repeater-item>
                                        <div class="form-group row mb-4">
                                            <div class="col-md-3 fv-row">
                                                <!--begin::Dropzone-->
                                                <div class="dropzone" id="kt_dropzonejs_example_1" data-custom-instance="dropzone" name="repeater-dropzone">
                                                    <!--begin::Message-->
                                                    <div class="dz-message needsclick">
                                                        <!--begin::Info-->
                                                        <div class="dropzone-panel">
                                                            <a class="dropzone-select btn"><i class="fa-solid fa-cloud-arrow-up fs-2 me-1 text-primary"></i>
                                                            <span class="fs-6 text-primary">Muatnaik Gambar</span></a>
                                                        </div>
                                                        <!--end::Info-->
                                                    </div>

                                                    <!--begin::Preview Area-->
                                                    <div class="preview-area">
                                                        <div class="dropzone-items dropzone-previews">
                                                            <!-- Preview Item Template -->
                                                            <div class="preview-item" style="display:none">
                                                                <div class="preview-content">
                                                                    <!-- Image Preview -->
                                                                    <div class="image-preview">
                                                                        <img src="" alt="Preview" data-dz-thumbnail class="img-fluid rounded" />
                                                                    </div>

                                                                    <div class="image-preview-container mt-2">
                                                                        <!-- Images will be displayed here -->
                                                                    </div>
                                                                    
                                                                    <!-- File Info -->
                                                                    <div class="dropzone-file p-3">
                                                                        <div class="filename" data-dz-name></div>
                                                                        <div class="filesize text-gray-600" data-dz-size></div>

                                                                        <!-- Progress Bar -->
                                                                        <!-- <div class="progress mt-2" style="height: 5px;">
                                                                            <div class="progress-bar bg-primary" role="progressbar" data-dz-uploadprogress></div>
                                                                        </div> -->
                                                                        
                                                                        <!-- Error Message -->
                                                                        <!-- <div class="text-danger mt-2" data-dz-errormessage></div> -->

                                                                        <!-- Success/Error Messages -->
                                                                        <div class="upload-status mt-2">
                                                                            <div class="text-success upload-success" style="display: none;">
                                                                                <i class="fa-solid fa-check-circle me-1"></i> Muatnaik Berjaya
                                                                            </div>
                                                                            <div class="text-danger upload-error" data-dz-errormessage style="display: none;"></div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Delete Button -->
                                                                    <div class="dropzone-delete" data-dz-remove>
                                                                        <i class="fa-solid fa-times text-danger"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Preview Area-->
                                                </div>
                                                <!--end::Dropzone-->
                                                <!-- Hidden inputs -->
                                                <input type="text" name="image-url" id="imageUrlInput" class="image-url-input" value="" hidden>
                                                <input type="text" name="latitude" id="latitude" value="" hidden>
                                                <input type="text" name="longitude" id="longitude" value="" hidden>
                                                <input type="hidden" name="upload_status" id="uploadStatus" value="0">
                                                <!-- Hidden inputs -->
                                            </div>
                                            <div class="col-md-4 mb-7 fv-row">
                                                <div class="form-floating">
                                                    <!--start::Select-->
                                                    <select class="form-select selectOption" name="survey-work-select" data-kt-repeater="select2" id="surveyWorkSelect" style="height: 57px;">
                                                        <option value="" selected disabled>Pilih kategori kerja ukur</option>
                                                        <?php echo $surveyWork; ?>
                                                    </select>
                                                    <!--end::Select-->
                                                    <label>Sila Pilih Kategori Kerja Ukur <span class="text-danger">*</span></label>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-7 fv-row surveyWork1" id="surveyWork1" style="display: none;">
                                                <div class="form-floating">
                                                    <!--start::Select-->
                                                    <select class="form-select selectOption" name="survey-work-desc" data-kt-repeater="select2" id="surveyWorkDesc" style="height: 57px;">
                                                        <option value="" selected disabled>Pilih ulasan kerja ukur</option>
                                                        <?php echo $surveyWorkDesc; ?>
                                                    </select>
                                                    <!--end::Select-->
                                                    <label>Sila Pilih Ulasan Kerja Ukur <span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-7 fv-row surveyWork2" id="surveyWork2" style="display: none;">
                                                <!--begin::Input group-->
                                                <div class="form-floating">
                                                    <input class="form-control" type="number" name="chainage-value" placeholder="Contoh : 00" style="height: 57px;"></input>
                                                    <label>Nilai Kedudukan Chainage <span class="text-danger">*</span></label>
                                                </div>
                                                <label class="text-primary fs-8">(masukkan nilai kedudukan chainage sahaja. Contoh : 00)</label>
                                                <div class="text-danger fs-8" id="validationChainage" style="display: none;">Sila masukkan nilai sahaja</div>
                                                <div>

                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <div class="col-md-4 mb-7 fv-row surveyWork3" id="surveyWork3" style="display: none;">
                                                <!--begin::Input group-->
                                                <div class="form-floating">
                                                    <textarea class="form-control" name="notes" placeholder="Catatan gambar" style="height: 57px;"></textarea>
                                                    <label>Catatan</label>
                                                </div>
                                                <div>

                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" data-repeater-delete class="btn btn-light-danger p-4">
                                                    <i class="fad fa-trash fs-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Form group-->
                            <!--begin::Form group-->
                            <div class="form-group">
                                <button type="button" id="btn-create" data-repeater-create class="btn btn-light-primary">
                                    <i class="fad fa-plus"></i> Tambah
                                </button>
                            </div>
                            <!--end::Form group-->
                        </div>
                        <!--end::Repeater-->
                    </div>
                    <!--end::Card header-->
                </div>
                <!--end::Add Image-->
                <div class="d-flex justify-content-end">
                    <!--begin::Button-->
                    <a href="/dashboard" class="btn btn-light me-5">Batal</a>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <!-- <a href="/tasks/new" class="btn btn-primary me-5">Simpan</a> -->
                    <button type="submit" id="report_edit_submit" class="btn btn-primary">
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