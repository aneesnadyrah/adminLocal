<div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
    <!--begin::Card-->
    <div class="card card-flush mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary"
        data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', xl: '300px'}"
        data-kt-sticky-left="auto" data-kt-sticky-top="100px" data-kt-sticky-animation="false"
        data-kt-sticky-zindex="95">
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Form-->
            <form class="modal-body" novalidate="novalidate" id="form_action_permit_checklist">
                <!--begin::Actions-->
                <div class="mb-0">
                    <!--begin::Buttons-->
                    <?php
                        function generateButtons($buttonData) {
                            echo '<div class="d-flex justify-content-center">';
                            foreach ($buttonData as $key => $button) {
                                $attributes = implode(' ', $button->attributes);
                                $class = $key === 0 ? 'me-2' : ''; // Add 'me-2' class to the first button
                                echo <<<Action
                                    <a class=" btn btn-flex flex-center btn-{$button->color} {$class}" {$attributes}>
                                        <i class="fad {$button->icon} fs-3"></i>
                                        <span>{$button->item}</span>
                                    </a>
                                Action;
                            }
                            echo '</div>';
                        }

                        $systemId = $_GET['id'];

                        if ("/projects/wayleave/checking/{$systemId}" == $_SERVER['REQUEST_URI']) {
                            // Buttons for checking
                            $buttonData = [
                                (object)['color' => 'light-dark', 'icon' => 'fa-file-circle-xmark', 'item' => 'Pinda', 'modal' => '#modal-amend'],
                                (object)['color' => 'primary', 'icon' => 'fa-file-circle-check', 'item' => 'Sahkan', 'modal' => '#modal-approve'],
                            ];

                            // Add extra attributes for data-bs-toggle and data-bs-target
                            foreach ($buttonData as &$button) {
                                $button->attributes = ["data-bs-toggle=\"modal\"", "data-bs-target=\"{$button->modal}\""];
                            }
                        } else {
                            // Buttons for other pages
                            $buttonData = [
                                (object)['color' => 'dark', 'icon' => 'fa-arrow-left', 'item' => 'Kembali', 'href' => "/operation/general/tasks/new"],
                                (object)['color' => 'light-dark', 'icon' => 'fa-print', 'item' => 'Cetak', 'href' => "/projects/prints/BKIL/{$systemId}"],
                            ];

                            // Add extra attributes for href
                            foreach ($buttonData as &$button) {
                                $button->attributes = ["href=\"{$button->href}\""];
                            }
                        }

                        generateButtons($buttonData);
                        ?>


                    <!--end::Buttons-->
                    <!--begin::Seperator-->
                    <div class="separator separator-dashed my-7"></div>
                    <!--end::Seperator-->
                    <h4 class="text-center text-gray-800 fs-5">
                        <?= $data->reference ?? "#$systemId" ?>
                    </h4>

                    <!--begin::Provider-->
                    <div class="mt-3">
                        <!--begin::Details-->
                        <div class="text-center">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px">
                                <img src="<?= $data->provider->logo ?>" alt="<?= $data->provider->name ?>" />
                            </div>
                            <h5 class="fw-semibold">
                                <?= !empty($data->provider->name) ? $data->provider->name : 'Tidak Diketahui' ?>
                            </h5>
                            <!--end::Avatar-->
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Provider-->

                    <!--begin::Seperator-->
                    <div class="separator separator-dashed my-7"></div>
                    <!--end::Seperator-->

                    <!--begin::Timeline-->
                    <div class="timeline">
                        <?php
                        foreach ($data->notes as $notes) {
                            // $time = date('h:i A', strtotime($notes->NewTimestamp));

                            // if (!empty($row['FirstName'])) {
                            //     $firstName = ucwords($row['FirstName']);
                            // } else {
                            //     $firstName = $row['Username'];
                            // }

                            // if ($row['ProfilePic'] == null) {
                            //     $img = "blank";
                            // } else {
                            //     $img = $row['ProfilePic'];
                            // }
                            // ;

                            // $picture = General::getProfile($img);
                            // $date = General::convertDate(date($row['NewTimestamp']));
                            // $template = <<<NOTES
                                                                
                            //     <!--begin::Timeline item-->
                            //     <div class="timeline-item">
                            //     <!--begin::Timeline line-->
                            //     <div class="timeline-line w-40px"></div>
                            //     <!--end::Timeline line-->
                            //     <!--begin::Timeline icon-->
                            //     <div class="timeline-icon symbol symbol-circle symbol-40px">
                            //     <div class="symbol-label bg-light">
                            //     <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                            //     <span class="svg-icon svg-icon-2  svg-icon-gray-500">
                            //     <i class="fad fa-$row[StatusIcon] fs-2"></i>
                            //     </span>
                            //     <!--end::Svg Icon-->
                            //     </div>
                            //     </div>
                            //     <!--end::Timeline icon-->
                            //     <!--begin::Timeline content-->
                            //     <div class="timeline-content">
                            //     <!--begin::Timeline heading-->
                            //     <div class="pe-3 mb-5">
                            //     <!--begin::Title-->
                            //     <div class="fs-5 fw-bold mb-2">$row[StatusName]</div>
                            //     <div class="fs-7 fw-semibold mb-2">$row[Notes]</div>
                            //     <!--end::Title-->
                            //     <!--begin::Description-->
                            //     <div class="d-flex align-items-center fs-6">
                            //     <!--begin::User-->
                            //     <div class="symbol symbol-20px symbol-circle me-3" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="$firstName">
                            //     <img src="$picture.jpg" alt="img" />
                            //     </div>
                            //     <!--end::User-->

                            //     <!--begin::Info-->
                            //     <div class="text-muted me-2 fs-8">
                            //     $date <br>
                            //     $time
                            //     </div>
                            //     <!--end::Info-->

                            //     </div>
                            //     <!--end::Description-->
                            //     </div>
                            //     <!--end::Timeline heading-->
                            //     </div>
                            //     <!--end::Timeline content-->
                            //     </div>
                            //     <!--end::Timeline item-->
                            // NOTES;

                            // if (!empty($row['StatusName'])) {
                            //     echo $template;
                            // }
                        }
                        ?>
                    </div>
                    <!--end::Timeline-->
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>