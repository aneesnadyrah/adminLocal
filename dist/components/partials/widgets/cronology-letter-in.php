
<!--begin::Timeline-->
<div class="timeline">
    <?php 
        echo '
        <div>';
            foreach($cronology as $row) {
                $time = date('h:i A', strtotime($row['created_at']));

                if($row['profile_pic'] == null){
                    $img = "blank"; 
                }
                else{
                    $img = $row['profile_pic'];
                };

                if($row['status'] == 0) {
                    $icon = 'file-circle-plus';
                } else if($row['status'] == 1) {
                    $icon = 'user-plus';
                } else {
                    $icon = 'file-check';
                }

                echo'
                <!--begin::Timeline item-->
                <div class="timeline-item content">
                    <!--begin::Timeline line-->
                    <div class="timeline-line w-40px"></div>
                    <!--end::Timeline line-->

                    <!--begin::Timeline icon-->
                    <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
                        <div class="symbol-label bg-light">
                            <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                <i class="fad fa-'.$icon.' fs-2"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                    </div>
                    <!--end::Timeline icon-->

                    <!--begin::Timeline content-->
                        <div class="timeline-content mb-10 mt-n1">
                            <!--begin::Timeline heading-->
                            <div class="pe-3 mb-5">
                                <!--begin::Title-->
                                <div class="fs-5 fw-semibold mb-2">'.$row['title'].'</div>
                                <!--end::Title-->
                                <!--begin::Notes-->
                                <div class="d-flex align-items-center mt-1 mb-4 fs-6">
                                    <div class="fs-6 fw-semibold text-gray-600 text-dark">'.$row['notes'].'</div>
                                </div>
                                <!--end::Notes-->

                                <!--begin::Description-->
                                <div class="d-flex align-items-center mt-1 fs-6">
                                    <!--begin::User-->
                                    <div class="symbol symbol-30px symbol-circle me-3" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="'.$row['first_name'].'">
                                        <img src="'.General::getProfile($img).'.jpg" alt="img" />
                                    </div>
                                    <!--end::User-->
                                    <!--begin::Info-->
                                    <div class="text-muted me-2 fs-7">Pada '.General::convertDate(date($row['created_at'])).' - '.$time.'</div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Description-->
                            </div>
                            <!--end::Timeline heading-->
                        </div>
                    
                    <!--end::Timeline content-->
                </div>
                <!--end::Timeline item-->';
            }
        echo'
        </div>';
    ?>
</div>
<!--end::Timeline-->