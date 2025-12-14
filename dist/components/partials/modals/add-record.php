<!--begin::Modal-->
<div class="modal fade" id="modal-add-record" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Rekod Permohonan</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fad fa-xmark fs-2"></i>
                </div>
                <!--end::Close-->
            </div>

            <form class="modal-body" action="https://<?php echo $_SERVER['HTTP_HOST'] ?>/v1/projects/record.php" method="POST">
                <div class="form-floating mb-10">
                    <input type="text" class="form-control" name="reference-no" id="no-reference" placeholder="XXX/XXX/XX/XX/XX/XX"/>
                    <label for="no-reference">No Rujukan</label>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success"><i class="fad fa-circle-plus"></i>Tambah</button>
                </div>
                
            </form>
        </div>
    </div>
</div>
<!--end::Modal - Upgrade plan-->
