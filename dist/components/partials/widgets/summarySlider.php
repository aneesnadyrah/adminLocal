<!--begin::Row-->
<div class="row g-5 g-xl-10">
<?php

// Using a for loop to get the index
for ($index = 0; $index < count($data); $index++) {
    $item = $data[$index];
    
        echo '<div class="col-xl-6">';
        echo '<div id="slider-$index" class="card card-flush carousel carousel-custom carousel-stretch slide" data-bs-ride="carousel"data-bs-interval="5000">';
        echo '<div class="card-header pt-5">';
        echo '<h4 class="card-title d-flex align-items-start flex-column">';
        echo '<span class="card-label fw-bold text-gray-800">'.$item->title.'</span>';
        echo '<span class="text-gray-500 mt-2 fw-bold fs-5">'.$item->counts.' Permohonan</span>';
        echo '</h4>';
        echo '<div class="card-toolbar">';
        echo '<ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-primary">';

    for ($i = 0; $i < $item->counts; $i++) {
        // Set the class based on the current index
        $class = ($i == 0) ? 'active ms-1' : 'ms-1';
        echo '<li data-bs-target="#slider-' . $index . '" data-bs-slide-to="' . $i . '" class="' . $class . '"></li>';
    }

    echo <<<TEMPLATE
        </ol>
        <!--end::Carousel Indicators-->
        </div>
        <!--end::Toolbar-->
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body py-8">
        <!--begin::Carousel-->
        <div class="carousel-inner mt-n5">
    TEMPLATE;

    $active = true;

    if (!empty($item->data)) {
        // Access properties of the 'data' object
        foreach ($item->data as $info) {
            $class = ($active) ? 'carousel-item active show' : 'carousel-item';
            $district = explode(",", $info->districts);
            $reference = $info->reference_no ?? '-';

            $provider = General::getProvider($info->provider_id);

            echo <<<TEMPLATE
                <!--begin::Item-->
                <div class="$class">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column align-items-center">
                    <!--begin::Info-->
                        <div class="d-flex flex-row align-items-center mb-3">
                        <!--begin::Chart-->
                            <div class="symbol symbol-50px">
                                <img src="$provider->logo" alt="$provider->name"/>
                            </div>
                            <div class="ms-3">
                            <!--begin::Subtitle-->
                            <h3 class="fw-bold text-gray-800 mb-0 fs-5">$reference</h3>
                            <h6 class="fw-semibold text-gray-600 mb-3 fs-6">#$info->system_id</h6>
                            <!--end::Subtitle-->
                            </div>
                        <!--end::Chart-->
                        </div>
                    <div class="m-0">
                    <!--begin::Section-->
                    <span class="d-flex align-items-center text-gray-500 fw-bold fs-6">
                    <i class="fad fa-caret-right fs-6 text-gray-600 me-2"></i>
                TEMPLATE;
                foreach ($district as $dist) {
                    echo '<span class="badge badge-light-primary me-2">' . $dist . '</span>';
                }

                echo <<<TEMPLATE
                </span>
                <!--end::Section-->

                <!--begin::Items-->
                <div class="d-flex d-grid gap-5">
                <!--begin::Item-->
                <div class="d-flex flex-column flex-shrink-0 me-4">
                <!--begin::Section-->
                <span class="d-flex align-items-center fs-6 fw-bold text-gray-500">
                <i class="fad fa-caret-right fs-6 text-gray-600 me-2"></i> $info->application_length m</span>
                <!--end::Section-->

                <!--begin::Section-->
                <span class="d-flex align-items-center fs-6 fw-bold text-gray-500">
                <i class="fad fa-caret-right fs-6 text-gray-600 me-2"></i> $info->status
                </span>
                <!--end::Section-->

                <!--begin::Section-->
                <span class="d-flex align-items-center text-gray-500 fw-bold fs-6">
                <i class="fad fa-caret-right fs-6 text-gray-600 me-2"></i> {$info->{$item->date_filter}}
                </span>
                <!--end::Section-->
                </div>
                <!--end::Item-->
                </div>
                <!--end::Items-->
                </div>
                <!--end::Info-->
                </div>
                <!--end::Wrapper-->
                </div>
                <!--end::Item-->
            TEMPLATE;
            $active = false;
        }
    } else {
        echo <<< TEMPLATE
            <!--begin::Empty-->
            <div class="text-center pt-3">
            <!--begin::Illustration-->
            <div class="text-center px-5">
            <img src="assets/media/illustrations/empty/lostConnection.svg" alt="" class="w-125px" />
            </div>
            <!--end::Illustration-->
            <!--begin::Message-->
            <h4 class="text-gray-600 fs-4 fw-bold mb-10">Tiada $item->title</h4>
            <!--end::Message-->
            </div>
            <!--end::Empty-->
        TEMPLATE;
    }

    if (!empty($item->data)) {
    echo <<<TEMPLATE
        </div>
        <!--end::Carousel-->
        </div>

        <!--end::Body-->
        </div>
        <!--end::Slider Widget $index-->
        </div>
        <!--end::Col-->
    TEMPLATE;
    } else {
    echo <<<TEMPLATE
        </div>
        <!--end::Carousel-->
        </div>
        <!--end::Body-->
        </div>
        <!--end::Slider Widget $index-->
        </div>
        <!--end::Col-->
    TEMPLATE;
    }

}

?>
</div>
<!--end::Row-->