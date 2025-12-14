
<!--begin::Form-->
<form id="add_record" class="form d-flex flex-column flex-lg-row">
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
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <button type="button"
                        class="btn btn-sm btn-icon btn-outline btn-outline-dashed btn-outline-primary btn-active-light-primary"
                        data-bs-toggle="modal" data-bs-target="#add-provider-list">
                        <i class="fad fa-circle-plus"></i>
                    </button>
                </div>
                <!--begin::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body text-center pt-0">
                <div class="symbol symbol-125px">
                    <img id="provider-img" src="assets/media/provider/99.webp" alt="" />
                </div>
                <!--begin::Input group-->
                <div class="form-floating mt-5 fv-row">
                    <select onchange="updateImage(this.value)" class="form-select mb-2" data-control="select2"
                    data-placeholder="Sila buat pilihan" name="utility-provider" id="utility-provider" aria-label="Select Status">
                        <option value="99">Sila buat pilihan</option>
                        <?php
                            foreach (General::selection('ls_provider') as $row) {
                                $selected = $row['id'] == $e[0]['utility_provider'] ? 'selected' : '';
                                echo '<option value="'. $row['id'] .'"' . $selected . '>' . $row['name'] . '</option>';
                            }
                        ?>
                    </select>
                    <label for="utility-provider">Pilih Penyedia Utiliti</label>
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Thumbnail settings-->
        <!--begin::Status-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2>Status</h2>
                </div>
                <!--end::Card title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <div class="rounded-circle bg-warning w-15px h-15px" id="project_status"></div>
                </div>
                <!--begin::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Select2-->
                <div class="form-floating fv-row">
                    <select class="form-select mb-2" data-control="select2" data-hide-search="true"
                        data-placeholder="Sila buat pilihan" name="project-status" id="project_status_select">
                        <option></option>
                        <?php
                            $draf='';
                            $baru='';

                            if (!empty($s)) {
                                if($s['ProjectStatus'] === '1'){
                                    $draf = 'selected';
                                } else if($s['ProjectStatus'] === '2') {
                                    $baru = 'selected';
                                }
                            }
                        ?>
                        <!-- <option value="1" <?php echo $draf; ?>>Draf Permohonan</option> -->
                        <option value="2" <?php echo $baru; ?>>Permohonan Baru</option>
                    </select>
                    <label for="project_status_select">Sila Pilih Status</label>
                </div>
                <!--end::Select2-->
                <!--begin::Input group-->
                <div class="fv-row">
                    <!--begin::Input group-->
                    <div class="form-floating">
                        <input type="date" class="form-control" name="application-date" placeholder="pilih tarikh"
                            id="application-date" value="<?php if (!empty($e)) { echo $e[0]['application_date']; } ?>" />
                        <label for="application-date">Tarikh Permohonan</label>
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Status-->
        <!--begin::Category & tags-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2>Daerah</h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <!--begin::Input group-->
                <div class="form-floating mt-4 fv-row">
                    <select name="district[]" class="form-select mb-2" data-control="select2" data-allow-clear="false"
                        data-hide-search="false" multiple="multiple" data-placeholder="Sila buat pilihan"
                        id="district">
                        <?php
                            // FIXME: changing districts by using UPI standard
                            // foreach (General::selection('ls_districts') as $row) {
                            //     $string = str_replace(array('{', '}'), '', $e[0]['district']);
                            //     // echo $string;
                            //     $dist = explode("," , $string);
                            //     // print_r($dist);

                            //     foreach ($dist as $value) {
                            //         // $selected = $row['id'] == $value ? 'selected' : '';
                            //         if ($row['id'] == $value){
                            //             $selected = 'selected';
                            //             break;
                            //         } else {
                            //             $selected = '';
                            //         }
                            //     }
                            //     echo '<option value="'. $row['id'] .'"' . $selected . '>' . $row['name'] . '</option>';
                            // }
                        ?>
                    </select>
                    <label for="district">Pilih Daerah Terlibat</label>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="form-floating fv-row">
                    <select class="form-select mb-2" name="region" id="region" data-control="select2" data-hide-search="true"
                        data-placeholder="Sila buat pilihan" id="region">
                        <option></option>
                        <?php
                            $region = str_replace(array('{', '}', "'"), '', $e[0]['project_region']);
                            $barat='';
                            $timur='';
                            $baratTimur='';

                            if($region === 'B'){
                                $barat = 'selected';
                            } else if($region === 'T') {
                                $timur = 'selected';
                            } else if($region === 'B,T') {
                                $baratTimur = 'selected';
                            }
                        ?>
                        <option value="{B}" <?php echo $barat; ?>>Barat</option>
                        <option value="{T}" <?php echo $timur; ?>>Timur</option>
                        <option value="{B,T}" <?php echo $baratTimur; ?>>Barat & Timur</option>
                    </select>
                    <label for="region">Pilih Zon Terlibat</label>
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Category & tags-->
    </div>
    <!--end::Aside column-->
    <!--begin::Main column-->
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <!--begin:::Tabs-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5">
            <li class="nav-item">
                <a class="nav-link w-100 active btn btn-flex btn-active-light-primary" data-bs-toggle="tab"
                    href="#project-details">

                    <span class="d-flex flex-stack align-items-start">
                        <i class="fa-duotone fa-triangle-person-digging fs-2 me-2"></i>
                        <span class="fs-4 fw-bold">Permohonan</span>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link w-100 btn btn-flex btn-active-light-primary" data-bs-toggle="tab"
                    href="#officer-details">

                    <span class="d-flex flex-stack align-items-start">
                        <i class="fa-duotone fa-user-helmet-safety fs-2 me-2"></i>
                        <span class="fs-4 fw-bold">Pegawai</span>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link w-100 btn btn-flex btn-active-light-primary" data-bs-toggle="tab"
                    href="#road-details">

                    <span class="d-flex flex-stack align-items-start">
                        <i class="fa-duotone fa-road fs-2 me-2"></i>
                        <span class="fs-4 fw-bold">Jalan</span>
                    </span>
                </a>
            </li>
        </ul>
        <!--end:::Tabs-->
        <!--begin::Tab content-->
        <div class="tab-content">
            <!--begin::Tab pane-->
            <div class="tab-pane fade show active" id="project-details" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10 mb-5">
                    <!--begin::General options-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Tajuk Surat Permohonan</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="fv-row">
                                <!--begin::Input group-->
                                <div class="form-floating">
                                    <textarea class="form-control" name="project-title"
                                        placeholder="Tajuk Permohonan..." id="project-title" style="height: 100px"><?php
                                            if (!empty($e)){
                                                echo $e[0]['project_title'];
                                            } else {

                                            }

                                        ?></textarea>
                                    <label for="project-title required">Tajuk
                                        Permohonan</label>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7 mt-2">Sila pastikan anda
                                    hanya menggunakan huruf besar dan tiada <span
                                        class="badge badge-light">&#9166;&nbsp;&nbsp;Enter</span>
                                </div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::General options-->
                    <!--begin::Pricing-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Info Permohonan</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="row">
                                <div class="col mb-7 fv-row">
                                    <label class="form-label">Nama Tapak A</label>
                                    <input type="text" name="site-start" class="form-control"
                                        placeholder="Nama Tapak A" value="<?php
                                            if (!empty($e)){
                                                echo $e[0]['site_start'];
                                            } else {

                                            }

                                        ?>" />
                                </div>
                                <div class="col mb-7 fv-row">
                                    <label class="form-label">Nama Tapak B</label>
                                    <input type="text" name="site-end" class="form-control"
                                        placeholder="Nama Tapak B" value="<?php
                                            if (!empty($e)){
                                                echo $e[0]['site_end'];
                                            } else {

                                            }

                                        ?>" />
                                </div>
                                <div class="col mb-7 fv-row">
                                    <label class="form-label">Link ID / No
                                        Projek</label>
                                    <input type="text" name="link-id" class="form-control" placeholder="Link ID" value="<?php
                                            if (!empty($e)){
                                                echo $e[0]['link_id'];
                                            } else {

                                            }

                                        ?>" />
                                </div>
                            </div>

                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row fv-row">
                                <div class="col-12 col-md-4 mb-5 fv-row">
                                    <label class="form-label required">Kos Projek</label>
                                    <input type="text" name="project-costs" class="form-control" placeholder="RM 0.00" value="<?php echo isset($e[0]['project_costs']) ? $e[0]['project_costs'] : ''; ?>" />
                                </div>
                                <div class="col-12 col-md-4 mb-5 fv-row">
                                    <label class="form-label required">Tarikh Mula Projek</label>
                                    <input type="text" name="project-start" class="form-control" placeholder="Tarikh Mula Projek" id="project-start" value="<?php echo isset($e[0]['project_date']) ? $e[0]['project_date'] : ''; ?>" />
                                </div>
                                <div class="col-12 col-md-4 mb-5 fv-row">
                                    <label class="form-label required">Tarikh Tamat Projek</label>
                                    <input type="text" name="project-end" class="form-control" placeholder="Tarikh Tamat Projek" id="project-end" value="<?php echo isset($e[0]['project_date']) ? $e[0]['project_date'] : ''; ?>" />
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="fv-row mb-10">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-semibold mb-3">Jenis
                                    Permohonan</label>
                                <!--End::Label-->
                                <!--begin::Row-->
                                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9"
                                    data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
                                    <?php
                                        $bf=''; $act1='';
                                        $gf=''; $act2='';
                                        $eg=''; $act3='';

                                        if($_GET['a'] == 'add' && empty($e[0]['type_application'])) {
                                            $bf = 'checked';
                                            $act1 = 'active';
                                        }

                                        if(!empty($e)){
                                            if($e[0]['type_application'] === 'BF'){
                                                $bf = 'checked';
                                                $act1 = 'active';
                                            } elseif($e[0]['type_application'] === 'GF') {
                                                $gf = 'checked';
                                                $act2 = 'active';
                                            } elseif($e[0]['type_application'] === 'GF') {
                                                $eg = 'checked';
                                                $act3 = 'active';
                                            }
                                        }

                                    ?>
                                    <!--begin::Col-->
                                    <div class="col">
                                        <!--begin::Option-->
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $act1; ?> d-flex text-start p-6"
                                            data-kt-button="true">
                                            <!--begin::Radio-->
                                            <span
                                                class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                <input class="form-check-input" type="radio" name="type-application"
                                                    value="BF" <?php echo $bf; ?>/>
                                            </span>
                                            <!--end::Radio-->
                                            <!--begin::Info-->
                                            <span class="ms-5">
                                                <span class="fs-4 fw-bold text-gray-800 d-block">Brownfield</span>
                                                <span class="fs-7 text-gray-600 d-block">Kawasan
                                                    Sedia ada</span>
                                            </span>
                                            <!--end::Info-->
                                        </label>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col">
                                        <!--begin::Option-->
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $act2; ?> d-flex text-start p-6"
                                            data-kt-button="true">
                                            <!--begin::Radio-->
                                            <span
                                                class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                <input class="form-check-input" type="radio" name="type-application"
                                                    value="GF" <?php echo $gf; ?>/>
                                            </span>
                                            <!--end::Radio-->
                                            <!--begin::Info-->
                                            <span class="ms-5">
                                                <span class="fs-4 fw-bold text-gray-800 d-block">Greenfield</span>
                                                <span class="fs-7 text-gray-600 d-block">Kawasan
                                                    Baharu</span>
                                            </span>
                                            <!--end::Info-->
                                        </label>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col">
                                        <!--begin::Option-->
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary <?php echo $act3; ?> d-flex text-start p-6"
                                            data-kt-button="true">
                                            <!--begin::Radio-->
                                            <span
                                                class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                                                <input class="form-check-input" type="radio" name="type-application"
                                                    value="EG" <?php echo $eg; ?>/>
                                            </span>
                                            <!--end::Radio-->
                                            <!--begin::Info-->
                                            <span class="ms-5">
                                                <span class="fs-4 fw-bold text-gray-800 d-block">Kecemasan</span>
                                                <span class="fs-7 text-gray-600 d-block">Kerja Baik Pulih &lt; 48 Jam</span>
                                            </span>
                                            <!--end::Info-->
                                        </label>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row" id="appl-category">

                                <!--begin::Label-->
                                <label class="required form-label">Kategori Permohonan</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <!--begin::Select2-->
                                <select class="form-select mb-2" name="appl-category" data-control="select2"
                                    data-hide-search="true" data-placeholder="Pilih Kategori Kerja">
                                    <option></option>
                                    <?php
                                        $a='';
                                        $b='';

                                        if (!empty($e)) {
                                            if($e[0]['application_code'] === 'KT'){
                                                $a = 'selected';
                                            } elseif($e[0]['application_code'] === 'KP') {
                                                $b = 'selected';
                                            }
                                        }

                                    ?>
                                    <option value="KT" <?php echo $a; ?>>Kerja Terancang</option>
                                    <option value="KP" <?php echo $b; ?>>Kerja Pengalihan</option>
                                </select>
                                <!--end::Select2-->
                                <!--end::Input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">pastikan kerja korek gali
                                    disemak sebelum pemilihan kategori dibuat</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Pricing-->
                    <!--begin::Media-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Lampiran</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="fv-row mb-2">
                                <!--begin::Dropzone-->
                                <div class="dropzone" id="add-attachment">
                                    <!--begin::Message-->
                                    <div class="dz-message needsclick">
                                        <!--begin::Icon-->
                                        <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                        <!--end::Icon-->
                                        <!--begin::Info-->
                                        <div class="ms-4">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                Leret dokumen ke sini atau klik di sini
                                                untuk muatnaik</h3>
                                            <span class="fs-7 fw-semibold text-gray-400">Sila
                                                Pilih Borang Kelulusan Izin Lalu</span>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <input type="text" name="system-id" id="system-id" value="<?php echo $systemId; ?>"
                                        hidden>
                                    <input type="text" name="ref-no" id="ref-no" value="<?php echo $e[0]['reference_no']; ?>"
                                        hidden>
                                </div>
                                <!--end::Dropzone-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Description-->
                            <div class="text-muted fs-7">Pastikan dokumen BKIL telah
                                disatukan dalam bentuk format pdf
                            </div>
                            <!--end::Description-->
                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Media-->

                </div>
            </div>
            <!--end::Tab pane-->
            <!--begin::Tab pane-->
            <div class="tab-pane fade" id="officer-details" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10 mb-5">
                    <?php
                        if ($_GET['a'] == 'add' || ($_GET['a'] == 'edit' && empty($c))) {
                    ?>
                    <!--begin::Applicant Info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Maklumat Pemohon</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-5 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nama
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-company-name" class="form-control mb-2"
                                        placeholder="Nama Syarikat" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Jenis
                                        Pemohon</label>
                                    <!--end::Label-->
                                    <select name="applicant-type" class="form-select mb-2" data-control="select2"
                                        data-hide-search="true" data-placeholder="Sila buat pilihan">
                                        <option></option>
                                        <?php
                                            $sendiri='';
                                            $kontrak='';
                                            $konsult='';
                                            $pemaju='';
                                            $utiliti='';
                                            $tidakTahu='';

                                            if($c[0]['Type'] == 1){
                                                $sendiri = 'selected';
                                            } elseif($c[0]['Type'] == 2) {
                                                $kontrak = 'selected';
                                            } elseif($c[0]['Type'] == 3) {
                                                $konsult = 'selected';
                                            } elseif($c[0]['Type'] == 4) {
                                                $pemaju = 'selected';
                                            } elseif($c[0]['Type'] == 5) {
                                                $utiliti = 'selected';
                                            } elseif($c[0]['Type'] == 9) {
                                                $tidakTahu = 'selected';
                                            }
                                        ?>
                                        <option value="1" <?php echo $sendiri; ?>>Persendirian</option>
                                        <option value="2" <?php echo $kontrak; ?>>Kontraktor</option>
                                        <option value="3" <?php echo $konsult; ?>>Konsultan</option>
                                        <option value="4" <?php echo $pemaju; ?>>Pemaju</option>
                                        <option value="5" <?php echo $utiliti; ?>>Penyedia Utiliti</option>
                                        <option value="9" <?php echo $tidakTahu; ?>>Tidak Diketahui</option>
                                    </select>
                                </div>

                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Alamat
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-address-1" class="form-control mb-2"
                                        placeholder="No Unit, Bangunan" />
                                    <!--end::Input-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-address-2" class="form-control mb-2"
                                        placeholder="Jalan, Taman" />
                                    <!--end::Input-->
                                </div>

                                <div class="row">
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-postcode" id="a-postcode" class="form-control mb-2"
                                            placeholder="Poskod" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-city" id="a-city" class="form-control mb-2 form-control-solid"
                                            placeholder="Bandar" readonly />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-state" id="a-state" class="form-control mb-2 form-control-solid"
                                            placeholder="Negeri" readonly />
                                        <!--end::Input-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-5 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Nama Pemohon</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="appl-full-name" class="form-control mb-2"
                                    placeholder="Nama Penuh Pemohon" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Jawatan</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-position" class="form-control mb-2"
                                        placeholder="Jawatan Pemohon" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">No
                                        Telefon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-phone-no" class="form-control mb-2"
                                        placeholder="No Telefon Pemohon" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">E-mel</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="appl-email" class="form-control mb-2"
                                        placeholder="Alamat E-mel" />
                                    <!--end::Input-->
                                </div>

                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Applicant Info-->
                    <!--begin::Officer Info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Maklumat Pegawai Utiliti</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-5 fv-row">
                                <!--begin::Label-->
                                <label class="required form-label">Nama Pegawai</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="officer-full-name" class="form-control mb-2"
                                    placeholder="Nama Penuh Pegawai" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Jawatan</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-position" class="form-control mb-2"
                                        placeholder="Jawatan Pegawai" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">No
                                        Telefon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-phone-no" class="form-control mb-2"
                                        placeholder="No Telefon Pegawai" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">E-mel</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="officer-email" class="form-control mb-2"
                                        placeholder="Alamat E-mel" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Alamat
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-address-1" class="form-control mb-2"
                                        placeholder="No Unit, Bangunan" />
                                    <!--end::Input-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-address-2" class="form-control mb-2"
                                        placeholder="Jalan, Taman" />
                                    <!--end::Input-->
                                </div>
                                <div class="row">
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-postcode" id="o-postcode" class="form-control mb-2"
                                            placeholder="Poskod" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-city" id="o-city" class="form-control mb-2 form-control-solid"
                                            placeholder="Bandar" readonly />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-state" id="o-state" class="form-control mb-2 form-control-solid"
                                            placeholder="Negeri" readonly />
                                        <!--end::Input-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Officer Info-->
                    <?php
                        } elseif ($_GET['a'] == 'edit') {
                    ?>
                    <!--begin::Applicant Info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Maklumat Pemohon</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-5 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nama
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-company-name" class="form-control mb-2"
                                        placeholder="Nama Syarikat" value="<?php echo $c[0]['CompanyName']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Jenis
                                        Pemohon</label>
                                    <!--end::Label-->
                                    <select name="applicant-type" class="form-select mb-2" data-control="select2"
                                        data-hide-search="true" data-placeholder="Sila buat pilihan">
                                        <option></option>
                                        <?php
                                            $sendiri='';
                                            $kontrak='';
                                            $konsult='';
                                            $pemaju='';
                                            $utiliti='';
                                            $tidakTahu='';

                                            if($c[0]['Type'] == 1){
                                                $sendiri = 'selected';
                                            } elseif($c[0]['Type'] == 2) {
                                                $kontrak = 'selected';
                                            } elseif($c[0]['Type'] == 3) {
                                                $konsult = 'selected';
                                            } elseif($c[0]['Type'] == 4) {
                                                $pemaju = 'selected';
                                            } elseif($c[0]['Type'] == 5) {
                                                $utiliti = 'selected';
                                            } elseif($c[0]['Type'] == 9) {
                                                $tidakTahu = 'selected';
                                            }
                                        ?>
                                        <option value="1" <?php echo $sendiri; ?>>Persendirian</option>
                                        <option value="2" <?php echo $kontrak; ?>>Kontraktor</option>
                                        <option value="3" <?php echo $konsult; ?>>Konsultan</option>
                                        <option value="4" <?php echo $pemaju; ?>>Pemaju</option>
                                        <option value="5" <?php echo $utiliti; ?>>Penyedia Utiliti</option>
                                        <option value="9" <?php echo $tidakTahu; ?>>Tidak Diketahui</option>
                                    </select>
                                </div>

                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Alamat
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-address-1" class="form-control mb-2"
                                        placeholder="No Unit, Bangunan" value="<?php echo $c[0]['Address1']; ?>" />
                                    <!--end::Input-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-address-2" class="form-control mb-2"
                                        placeholder="Jalan, Taman" value="<?php echo $c[0]['Address2']; ?>" />
                                    <!--end::Input-->
                                </div>

                                <div class="row">
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-postcode" id="a-postcode" class="form-control mb-2"
                                            placeholder="Poskod" value="<?php echo $c[0]['Postcode']; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-city" id="a-city" class="form-control mb-2 form-control-solid"
                                            placeholder="Bandar" value="<?php echo $c[0]['City']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-state" id="a-state" class="form-control mb-2 form-control-solid"
                                            placeholder="Negeri" value="<?php echo $c[0]['State']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-5 fv-row">
                                <input type="text" name="appl-id" class="form-control mb-2" value="<?php echo $c[0]['Id']; ?>" hidden />
                                <!--begin::Label-->
                                <label class="required form-label">Nama Pemohon</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="appl-full-name" class="form-control mb-2"
                                    placeholder="Nama Penuh Pemohon" value="<?php echo $c[0]['FullName']; ?>" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="form-label">Jawatan</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-position" class="form-control mb-2"
                                        placeholder="Jawatan Pemohon" value="<?php echo $c[0]['Position']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">No
                                        Telefon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-phone-no" class="form-control mb-2"
                                        placeholder="No Telefon Pemohon" value="<?php echo $c[0]['PhoneNo']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">E-mel</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="appl-email" class="form-control mb-2"
                                        placeholder="Alamat E-mel" value="<?php echo $c[0]['Email']; ?>" />
                                    <!--end::Input-->
                                </div>

                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Applicant Info-->
                    <!--begin::Officer Info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Maklumat Pegawai Utiliti</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-5 fv-row">
                                <input type="text" name="officer-id" class="form-control mb-2" value="<?php echo $c[1]['Id']; ?>" hidden />
                                <!--begin::Label-->
                                <label class="required form-label">Nama Pegawai</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="officer-full-name" class="form-control mb-2"
                                    placeholder="Nama Penuh Pegawai" value="<?php echo $c[1]['FullName']; ?>" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="form-label">Jawatan</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-position" class="form-control mb-2"
                                        placeholder="Jawatan Pegawai" value="<?php echo $c[1]['Position']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">No
                                        Telefon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-phone-no" class="form-control mb-2"
                                        placeholder="No Telefon Pegawai" value="<?php echo $c[1]['PhoneNo']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">E-mel</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="officer-email" class="form-control mb-2"
                                        placeholder="Alamat E-mel" value="<?php echo $c[1]['Email']; ?>" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Alamat
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-address-1" class="form-control mb-2"
                                        placeholder="No Unit, Bangunan" value="<?php echo $c[1]['Address1']; ?>" />
                                    <!--end::Input-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-address-2" class="form-control mb-2"
                                        placeholder="Jalan, Taman" value="<?php echo $c[1]['Address2']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="row">
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-postcode" id="o-postcode" class="form-control mb-2"
                                            placeholder="Poskod" value="<?php echo $c[1]['Postcode']; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-city" id="o-city" class="form-control mb-2 form-control-solid"
                                            placeholder="Bandar" value="<?php echo $c[1]['City']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-state" id="o-state" class="form-control mb-2 form-control-solid"
                                            placeholder="Negeri" value="<?php echo $c[1]['State']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Officer Info-->
                    <?php
                        } elseif ($_GET['a'] == 'approve') {
                    ?>
                    <!--begin::Applicant Info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Maklumat Pemohon</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-5 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Nama
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-company-name" class="form-control mb-2"
                                        placeholder="Nama Syarikat" value="<?php echo $c[0]['CompanyName']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Jenis
                                        Pemohon</label>
                                    <!--end::Label-->
                                    <select name="applicant-type" class="form-select mb-2" data-control="select2"
                                        data-hide-search="true" data-placeholder="Sila buat pilihan">
                                        <option></option>
                                        <?php
                                            $sendiri='';
                                            $kontrak='';
                                            $konsult='';
                                            $pemaju='';
                                            $utiliti='';
                                            $tidakTahu='';

                                            if($c[0]['Type'] == 1){
                                                $sendiri = 'selected';
                                            } elseif($c[0]['Type'] == 2) {
                                                $kontrak = 'selected';
                                            } elseif($c[0]['Type'] == 3) {
                                                $konsult = 'selected';
                                            } elseif($c[0]['Type'] == 4) {
                                                $pemaju = 'selected';
                                            } elseif($c[0]['Type'] == 5) {
                                                $utiliti = 'selected';
                                            } elseif($c[0]['Type'] == 9) {
                                                $tidakTahu = 'selected';
                                            }
                                        ?>
                                        <option value="1" <?php echo $sendiri; ?>>Persendirian</option>
                                        <option value="2" <?php echo $kontrak; ?>>Kontraktor</option>
                                        <option value="3" <?php echo $konsult; ?>>Konsultan</option>
                                        <option value="4" <?php echo $pemaju; ?>>Pemaju</option>
                                        <option value="5" <?php echo $utiliti; ?>>Penyedia Utiliti</option>
                                        <option value="9" <?php echo $tidakTahu; ?>>Tidak Diketahui</option>
                                    </select>
                                </div>

                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Alamat
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-address-1" class="form-control mb-2"
                                        placeholder="No Unit, Bangunan" value="<?php echo $c[0]['Address1']; ?>" />
                                    <!--end::Input-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-address-2" class="form-control mb-2"
                                        placeholder="Jalan, Taman" value="<?php echo $c[0]['Address2']; ?>" />
                                    <!--end::Input-->
                                </div>

                                <div class="row">
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-postcode" id="a-postcode" class="form-control mb-2"
                                            placeholder="Poskod" value="<?php echo $c[0]['Postcode']; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-city" id="a-city" class="form-control mb-2 form-control-solid"
                                            placeholder="Bandar" value="<?php echo $c[0]['City']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="appl-state" id="a-state" class="form-control mb-2 form-control-solid"
                                            placeholder="Negeri" value="<?php echo $c[0]['State']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-5 fv-row">
                                <input type="text" name="appl-id" class="form-control mb-2" value="<?php echo $c[0]['ID']; ?>" hidden />
                                <!--begin::Label-->
                                <label class="required form-label">Nama Pemohon</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="appl-full-name" class="form-control mb-2"
                                    placeholder="Nama Penuh Pemohon" value="<?php echo $c[0]['FullName']; ?>" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="form-label">Jawatan</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-position" class="form-control mb-2"
                                        placeholder="Jawatan Pemohon" value="<?php echo $c[0]['Position']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">No
                                        Telefon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="appl-phone-no" class="form-control mb-2"
                                        placeholder="No Telefon Pemohon" value="<?php echo $c[0]['PhoneNo']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">E-mel</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="appl-email" class="form-control mb-2"
                                        placeholder="Alamat E-mel" value="<?php echo $c[0]['Email']; ?>" />
                                    <!--end::Input-->
                                </div>

                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Applicant Info-->
                    <!--begin::Officer Info-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Maklumat Pegawai Utiliti</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Input group-->
                            <div class="mb-5 fv-row">
                                <input type="text" name="officer-id" class="form-control mb-2" value="<?php echo $c[1]['ID']; ?>" hidden />
                                <!--begin::Label-->
                                <label class="required form-label">Nama Pegawai</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" name="officer-full-name" class="form-control mb-2"
                                    placeholder="Nama Penuh Pegawai" value="<?php echo $c[1]['FullName']; ?>" />
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10 row">
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="form-label">Jawatan</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-position" class="form-control mb-2"
                                        placeholder="Jawatan Pegawai" value="<?php echo $c[1]['Position']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">No
                                        Telefon</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-phone-no" class="form-control mb-2"
                                        placeholder="No Telefon Pegawai" value="<?php echo $c[1]['PhoneNo']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="col fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">E-mel</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="email" name="officer-email" class="form-control mb-2"
                                        placeholder="Alamat E-mel" value="<?php echo $c[1]['Email']; ?>" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <div class="fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">Alamat
                                        Syarikat</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-address-1" class="form-control mb-2"
                                        placeholder="No Unit, Bangunan" value="<?php echo $c[1]['Address1']; ?>" />
                                    <!--end::Input-->
                                    <!--begin::Input-->
                                    <input type="text" name="officer-address-2" class="form-control mb-2"
                                        placeholder="Jalan, Taman" value="<?php echo $c[1]['Address2']; ?>" />
                                    <!--end::Input-->
                                </div>
                                <div class="row">
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-postcode" id="o-postcode" class="form-control mb-2"
                                            placeholder="Poskod" value="<?php echo $c[1]['Postcode']; ?>" />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-city" id="o-city" class="form-control mb-2 form-control-solid"
                                            placeholder="Bandar" value="<?php echo $c[1]['City']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                    <div class="col fv-row">
                                        <!--begin::Input-->
                                        <input type="text" name="officer-state" id="o-state" class="form-control mb-2 form-control-solid"
                                            placeholder="Negeri" value="<?php echo $c[1]['State']; ?>" readonly />
                                        <!--end::Input-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->

                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Officer Info-->
                    <?php
                        }
                    ?>
                </div>
            </div>
            <!--end::Tab pane-->
            <!--begin::Tab pane-->
            <div class="tab-pane fade" id="road-details" role="tab-panel">
                <div class="d-flex flex-column gap-7 gap-lg-10 mb-5">
                    <!--begin::Road Involved-->
                    <div class="card card-flush py-4">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Butiran Jalan Terlibat</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Repeater-->
                            <div id="road-involved">
                                <!--begin::Form group-->
                                <div class="form-group">

                                    <div data-repeater-list="road-list">
                                        <?php
                                            if ($_GET['a'] == "add" || ($_GET['a'] == "edit" && empty($r))) {
                                        ?>
                                        <div data-repeater-item>
                                            <div class="form-group row mb-5">
                                                <div class="col-12 col-md-7 mb-2 fv-row">
                                                    <label class="form-label required">Nama Jalan</label>
                                                    <input type="text" name="road-name" class="form-control" placeholder="Nama Jalan">
                                                </div>
                                                <div class="col-12 col-md-5 mb-2 fv-row">
                                                    <label class="form-label required">Kaedah Kerja</label>
                                                    <select id="road-method-0" name="road-method" class="form-select method-select" multiple></select>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2 fv-row">
                                                    <label class="form-label required">Koordinat Mula</label>
                                                    <input type="text" name="start-coord" class="form-control" placeholder="5.32891701, 103.14635436">
                                                </div>
                                                <div class="col-12 col-md-4 mb-2 fv-row">
                                                    <label class="form-label required">Koordinat Akhir</label>
                                                    <input type="text" name="end-coord" class="form-control" placeholder="5.33046594, 103.14851092">
                                                </div>
                                                <div class="col-12 col-md-3 mb-2 fv-row">
                                                    <label class="form-label required">Jarak</label>
                                                    <input type="text" name="road-length" class="form-control repeater-value" placeholder="Jarak">
                                                </div>
                                                <div class="col-6 col-md-1 mb-2">
                                                    <button type="button" data-repeater-delete="" class="btn btn-light-danger py-4 mt-8">
                                                        <i class="fad fa-trash fs-4"></i><span class="d-md-none">Padam</span>
                                                    </button>
                                                </div>
                                                <div class="separator separator-dashed mt-2"></div>
                                            </div>
                                        </div>
                                        <?php
                                            } elseif ($_GET['a'] == "edit") {
                                                $count = 0;
                                                foreach ($r as $road) {
                                        ?>
                                        <div data-repeater-item>
                                            <div class="form-group row mb-5">
                                                <input type="text" name="road-id" class="form-control" value="<?php echo $road['id']; ?>" hidden />
                                                <div class="col-12 col-md-7 mb-2 fv-row">
                                                    <label class="form-label required">Nama Jalan</label>
                                                    <input type="text" name="road-name" class="form-control"
                                                        placeholder="Nama Jalan" value="<?php echo $road['road_name']; ?>" />
                                                </div>
                                                <div class="col-12 col-md-5 mb-2 fv-row">
                                                    <label class="form-label required">Kaedah Kerja</label>
                                                    <select id="road-method-0" name="road-method" class="form-select" >
                                                        <?php
                                                            foreach (General::selection('ls_work_methods') as $row) {
                                                                $string = str_replace(array('{', '}'), '', $road['method']);
                                                                // echo $string;
                                                                $dist = explode("," , $string);
                                                                // print_r($dist);

                                                                foreach ($dist as $value) {
                                                                    // $selected = $row['id'] == $value ? 'selected' : '';
                                                                    if ($row['method'] == $value){
                                                                        $selected = 'selected';
                                                                        break;
                                                                    } else {
                                                                        $selected = '';
                                                                    }
                                                                }
                                                                echo '<option value="'. $row['method'] .'"' . $selected . '>' . $row['name'] . '</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2 fv-row">
                                                    <label class="form-label required">Koordinat Mula</label>
                                                    <input type="text" name="start-coord" class="form-control" placeholder="5.32891701, 103.14635436"
                                                    value="<?php echo $road['latitude_start']; ?>, <?php echo $road['longitude_start']; ?>">
                                                </div>
                                                <div class="col-12 col-md-4 mb-2 fv-row">
                                                    <label class="form-label required">Koordinat Akhir</label>
                                                    <input type="text" name="end-coord" class="form-control" placeholder="5.32891701, 103.14635436"
                                                    value="<?php echo $road['latitude_end']; ?>, <?php echo $road['longitude_end']; ?>">
                                                </div>
                                                <div class="col-12 col-md-3 mb-2 fv-row">
                                                    <label class="form-label required">Jarak</label>
                                                    <input type="text" id="road-length" name="road-length" class="form-control repeater-value"
                                                        placeholder="Jarak" value="<?php echo $road['road_length']; ?>" />
                                                </div>
                                                <div class="col-6 col-md-1 mb-2">
                                                    <button type="button" data-repeater-delete=""
                                                        class="btn btn-light-danger py-4 mt-8 repeater-delete" id="test" value="<?php echo $road['road_length']; ?>">
                                                        <i class="fad fa-trash fs-4"></i><span class="d-md-none">Padam</span>
                                                    </button>
                                                </div>
                                                <div class="separator separator-dashed mt-2"></div>
                                            </div>
                                        </div>
                                        <?php
                                                    $count++;
                                                }
                                            }
                                        ?>
                                    </div>
                                    <div class="d-flex flex-end row">
                                        <div class="col-2 fv-row ">
                                            <label class="form-label">Jumlah Jarak</label>
                                            <input type="text" name="application-length" id="application-length" class="form-control form-control-solid" placeholder="Jarak Permohonan" value=""/>
                                        </div>
                                        <div class="col-1"></div>
                                    </div>
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <button type="button" id="btn-create" data-repeater-create class="btn btn-light-primary">
                                            <i class="fad fa-plus"></i>Tambah Jalan
                                        </button>
                                    </div>
                                    <!--end::Form group-->
                                </div>
                                <!--end::Form group-->

                            </div>
                            <!--end::Repeater-->
                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Road Involved-->
                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="/dashboard" class="btn btn-light me-5">Batal</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" id="add_record_submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan</span>
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                </div>
            </div>
            <!--end::Tab pane-->

            <input type="text" name="route" value="<?php echo $_GET['a']; ?>" hidden />
        </div>
        <!--end::Tab content-->
    </div>
    <!--end::Main column-->
</form>
<!--end::Form-->
