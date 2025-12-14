<!--begin::Sidebar-->
<div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
    <!--begin::Card-->
    <div class="card card-flush mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary" data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto" data-kt-sticky-top="100px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95" style="">
        <!--begin::Card header-->
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title">
                <h2>Penyedia Utiliti</h2>
            </div>
            <!--end::Card title-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body pt-0 fs-6">
            <!--begin::Form-->
            <form class="modal-body" novalidate="novalidate"
                id="form_action_permit_checklist">
                <!--begin::Actions-->
                <div class="mb-0">
                    <!--begin::Provider-->
                    <div name="logo-provider">
                        <!--begin::Details-->
                        <div class="text-center">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px">
                                <img src="assets/media/provider/<?php echo isset($e['utility_provider']) ? $e['utility_provider'] : 99 ?>.webp" alt="image" />
                            </div>
                            <h3 class="card-title d-none d-md-block">
                                <?php echo isset($e['name']) ? $e['name'] : 'Tidak Diketahui' ?>
                            </h3>
                            <span class="text-gray-800 fs-5"><?php echo isset($e['reference_no']) ? $e['reference_no'] : ''; ?></span>
                            <!--end::Avatar-->
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Provider-->

                    <!--begin::Seperator-->
                    <div class="separator separator-dashed my-7"></div>
                    <!--end::Seperator-->

                    <!--begin::Buttons-->
                    <div name="buttons" class="d-flex justify-content-between">
                        <?php
                        if (("/projects/permit/checklist/" . $systemId == $_SERVER['REQUEST_URI'])) {

                            //Array for this Nested
                            $colors = ['light-dark', 'dark'];
                            $modals = ['#modal-amend-permit', '#modal-approve'];
                            $icons = ['fa-file-circle-xmark', 'fa-file-circle-check'];
                            $items = ['Pinda', 'Sahkan'];
                        
                            $array = array_map(function ($color, $modal, $icon, $item) {
                                return ['color' => $color, 'modal' => $modal, 'icon' => $icon, 'item' => $item];
                            }, $colors, $modals, $icons, $items);
                        
                            foreach ($array as $button) {
                                echo <<<Action
                                <button type="button" class="btn btn-flex flex-center btn-{$button['color']} me-2" data-bs-toggle="modal" data-bs-target="{$button['modal']}">
                                    <i class="fad {$button['icon']} fs-4"></i>
                                    <span class="d-none d-md-inline">{$button['item']}</span>
                                </button>
                                Action;
                            }
                        
                        } else {
                        
                            //Array for this Nested
                            $colors = ['light-dark', 'dark'];
                            $href = ['/projects/prints/BKIL/' . $systemId, '/dashboard'];
                            $icons = ['fa-print', 'fa-arrow-left'];
                            $items = ['Cetak', 'Kembali'];
                        
                            $array = array_map(function ($color, $href, $icon, $item) {
                                return ['color' => $color, 'href' => $href, 'icon' => $icon, 'item' => $item];
                            }, $colors, $href, $icons, $items);
                        
                            foreach ($array as $button) {
                                echo <<<Action
                                <a href="{$button['href']}" class="btn btn-flex flex-center btn-{$button['color']} me-2">
                                    <i class="fad {$button['icon']} fs-4"></i>
                                    <span class="d-none d-md-inline">{$button['item']}</span>
                                </a>
                                Action;
                            }
                        
                        }                                        
                        ?>
                    </div>
                    <!--begin::Buttons-->
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
<!--end::Sidebar-->