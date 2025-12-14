<!--begin::Form-->
<form id="kt_account_profile_details_form" class="card mb-5 mb-xl-10 form">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Maklumat Diri</h3>
        </div>
        <!--end::Card title-->
    </div>
    <!--begin::Card header-->
    <!--begin::Card body-->
    <div class="card-body p-9">
    
        <!--begin::Input group-->
        <div class="row col-xl-6 mb-5">
            <!--begin::Label-->
            <label class="col-xl-3 col-form-label fw-semibold fs-6">Gambar Profil</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-xl-8">
                <!--begin::Image input-->
                <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('assets/media/avatars/blank.svg')">
                    <!--begin::Preview existing avatar-->
                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url(assets/media/avatars/blank.jpg)"></div>
                    <!--end::Preview existing avatar-->
                    <!--begin::Label-->
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <!--begin::Inputs-->
                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="avatar_remove" />
                        <!--end::Inputs-->
                    </label>
                    <!--end::Label-->
                    <!--begin::Cancel-->
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <!--end::Cancel-->
                    <!--begin::Remove-->
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <!--end::Remove-->
                </div>
                <!--end::Image input-->
                <!--begin::Hint-->
                <div class="form-text">Fail yang dibenarkan: png, jpg, jpeg.</div>
                <!--end::Hint-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->

        <div class="row mb-5">
            <!--begin::Col - Nama Pertama-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Nama Pertama</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="first_name" placeholder="Cth: Mohd Abu" value="<?php echo $data->first_name ? ucfirst($data->first_name) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Nama Pertama-->

            <!--begin::Col - Nama Akhir-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Nama Akhir</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="last_name" placeholder="Cth: Abdul Rahman" value="<?php echo $data->last_name ? ucfirst($data->last_name) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Nama Akhir-->
        </div>

        <div class="row mb-5">
            <!--begin::Col - No Kad Pengenalan-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">No Kad Pengenalan</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="identification_card" disabled="disabled" placeholder="Cth: 870124105388" value="<?php echo $data->identification_card ? $data->identification_card : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - No Kad Pengenalan-->

            <!--begin::Col - Emel-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Emel</label>
                    <div class="col-xl-8 fv-row">
                        <input type="email" class="form-control" name="email" disabled="disabled" placeholder="Cth: abu_87@gmail.com" value="<?php echo $data->email ? $data->email : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Emel-->
        </div>

        <div class="row mb-5">
            <!--begin::Col - Jawatan-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Jawatan</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="position" disabled="disabled" placeholder="Cth: Pegawai Kanan" value="<?php echo $data->position ? ucfirst($data->position) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Jawatan-->

            <!--begin::Col - No Telefon-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">No Telefon</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="phone_no" placeholder="Cth: 0195342767" value="<?php echo $data->phone_no ? $data->phone_no : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - No Telefon-->
        </div>

        <div class="row mb-5">
            <!--begin::Col - Alamat 1-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Alamat 1</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="first_address" placeholder="Cth: Unit 33A, Blok Permai" value="<?php echo $data->first_address ? ucfirst($data->first_address) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Alamat 1-->

            <!--begin::Col - Alamat 2-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Alamat 2</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" name="second_address" placeholder="Cth: Kampung Siakap, Taman Mudah" value="<?php echo $data->second_address ? ucfirst($data->second_address) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Alamat 2-->
        </div>

        <div class="row mb-5">
            <!--begin::Col - Poskod-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Poskod</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Cth: 40210" value="<?php echo $data->postcode ? $data->postcode : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Poskod-->

            <!--begin::Col - Bandar-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Bandar</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" id="city" name="city" disabled="disabled" placeholder="Cth: Kuala Kubu" value="<?php echo $data->city ? ucfirst($data->city) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Bandar-->
        </div>

        <div class="row mb-5">
            <!--begin::Col - Negeri-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Negeri</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" id="state" name="state" disabled="disabled" placeholder="Cth: Pahang" value="<?php echo $data->state ? ucfirst($data->state) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Negeri-->

            <!--begin::Col - Nama Pengguna-->
            <div class="col-xl-6">
                <div class="row">
                    <label class="col-xl-3 col-form-label fw-semibold fs-6">Nama Pengguna</label>
                    <div class="col-xl-8 fv-row">
                        <input type="text" class="form-control" disabled="disabled" name="username" value="<?php echo $data->username ? $data->username[0] . str_repeat("*", max(strlen($data->username) - 2, 0)) . substr($data->username, -1) : ''; ?>" />
                    </div>
                </div>
            </div>
            <!--end::Col - Nama Pengguna-->
        </div>

        <div class="row mb-5">
            <div class="col-xl-6"></div>

            <!--begin::Col - Butang Kemaskini-->
            <div class="col-xl-6">
                <div class="col-xl-11 d-flex justify-content-end">
                    <button type="submit" id="kt_account_profile_details_submit" class="btn btn-primary">
                        <span class="indicator-label"><i class="fad fa-file-pen fs-4"></i> Kemaskini</span>
                    </button>
                </div>
            </div>
            <!--end::Col - Butang Kemaskini-->
        </div>

    </div>
    <!--end::Card body-->
</form>
<!--end::Form-->