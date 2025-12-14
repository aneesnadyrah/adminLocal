<?php

foreach ($data as $index => $item) {
    $title = $item['title'];
    $subtitle = $item['subtitle'];
    $providerDataArray = $item['data']; // Now this is an ARRAY of stdClass objects

    echo <<<TEMPLATE
    <div class="card card-stretch card-flush mh-750px">
        <!--begin::Header-->
        <div class="card-header pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-dark">{$title}</span>
                <span class="text-gray-400 pt-2 fw-semibold fs-6">{$subtitle}</span>
            </h3>
        </div>
        <!--end::Header-->

        <!--begin::Body with scroll-->
        <div class="card-body pt-2 d-flex flex-column" style="overflow: hidden;">
            <div style="overflow-y: auto; flex-grow: 1;">
                <ul class="list-group list-group-flush">
    TEMPLATE;

    // Loop through each provider in the array
    foreach ($providerDataArray as $provider) {
        $providerName = htmlspecialchars($provider->name ?? 'Unknown Provider');
        $providerCount = (int)($provider->count ?? 0);
        $providerLogo = General::getProvider($provider->utility_provider)->logo;
        echo <<<PROVIDER_ITEM
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{$providerLogo}" width="30" height="30" class="me-2"/>                        
                        <span>{$providerName}</span>
                    </div>
                    <span class="badge bg-light-primary rounded-pill">{$providerCount}</span>
                </li>

        PROVIDER_ITEM;
    }

    echo <<<TEMPLATE_END
                </ul>
            </div>
        </div>
        <!--end::Body-->
    </div>
    TEMPLATE_END;
}
