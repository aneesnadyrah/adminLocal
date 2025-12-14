<?php 
        echo '<!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="add-provider-list">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Tambah Penyedia Utiliti</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <form class="modal-body" action="https://'.$_SERVER['HTTP_HOST'].'/v1/projects/add-provider.php" method="POST" enctype="multipart/form-data">
                        <!--begin::Image input-->
                        <div class="d-flex justify-content-center">
                            <div class="image-input image-input-circle image-input-empty mb-10" data-kt-image-input="true" style="background-image: url(/assets/media/svg/avatars/blank.svg)">
                                <!--begin::Image preview wrapper-->
                                <div class="image-input-wrapper w-100px h-100px"></div>
                                <!--end::Image preview wrapper-->

                                <!--begin::Edit button-->
                                <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change"
                                    data-bs-toggle="tooltip"
                                    data-bs-dismiss="click"
                                    title="Tambah Gambar">
                                    <i class="fad fa-pencil fs-7"></i>

                                    <!--begin::Inputs-->
                                    <input type="file" name="provider-picture" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="provider-remove" />
                                    <!--end::Inputs-->
                                </label>
                                <!--end::Edit button-->

                                <!--begin::Cancel button-->
                                <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="cancel"
                                    data-bs-toggle="tooltip"
                                    data-bs-dismiss="click"
                                    title="Batal Gambar">
                                    <i class="fad fa-xmark fs-7"></i>
                                </span>
                                <!--end::Cancel button-->

                                <!--begin::Remove button-->
                                <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove"
                                    data-bs-toggle="tooltip"
                                    data-bs-dismiss="click"
                                    title="Padam Gambar">
                                    <i class="fad fa-xmark fs-7"></i>
                                </span>
                                <!--end::Remove button-->
                            </div>
                        
                        </div>
                        
                        <!--end::Image input-->
                        <div class="form-floating mb-10">
                            <input type="text" class="form-control" name="provider" id="provider" placeholder="XXXXXXXXX"/>
                            <label for="provider">Nama Penyedia Utiliti</label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fad fa-circle-plus"></i>Tambah</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal - Upgrade plan-->';
?>