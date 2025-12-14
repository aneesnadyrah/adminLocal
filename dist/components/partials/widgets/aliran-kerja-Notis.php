<?php  
    $count = ProjectDetails::countWorkFlow($detail['system_id'], 3)[0]; 
    $data = ProjectDetails::getWorkFlow($detail['system_id'], 3); 
?>
<!--begin::col kelulusan izin lalu -->
<div class="col-md-4 col-lg-12 col-xl-4">
    <!--begin::Col header-->
    <div class="mb-2">
        <div class="d-flex flex-stack">
            <div class="fw-bold fs-4">Notis Kerja
                <!-- <span class="fs-6 text-danger ms-2"><?php echo $count['pending']?> / </span> -->
                <!-- <span class="fs-6 text-success ms-2"><?php echo $count['done']?></span> -->
            </div>
        </div>
        <div class="h-3px w-100 bg-warning"></div>
    </div>
    <!--end::Col header-->

    <!--begin::Scroll-->
    <div class="tab-content hover-scroll-overlay-y pe-1 me-3 mb-2" style="height: 350px">
        <!--begin::Table Container-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle gs-0 gy-4">
                <!--begin::Table head-->
                <thead>
                    <tr>                            
                        <th class="p-0 min-w-200px"></th>
                        <th class="p-0 min-w-100px"></th>    
                    </tr>
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody>
                    <?php 
                        foreach($data as $row) { 
                            if ($row['badge_status'] === 'Selesai') {
                                $badgeClass = '<span data-kt-element="status" class="badge badge-light-success">Selesai</span>';
                            } else {
                                $badgeClass = '<span data-kt-element="status" class="badge badge-light-danger">Belum Selesai</span>';
                            }
                            ?>
                            <tr>
                                <td>
                                    <span class="text-gray-400 fw-bold fs-6"><?php echo $row['details'] ?></span>
                                    <!-- <span class="text-gray-400 fw-bold fs-7 d-block">Mathematics</span> -->
                                </td>
                                <td class="text-end">
                                    <?php echo $badgeClass ?>
                                </td>
                            </tr>
                        <?php } ?>
            </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table Container-->
    </div>
    <!--end::Scroll-->

</div>
<!-- end::col kelulusan izin lalu -->
