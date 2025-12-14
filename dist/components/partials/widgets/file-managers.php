
<!--begin::Content-->
<div class="form d-flex flex-column flex-lg-row">
    <!--begin::List File column-->
    <div class="d-flex flex-column flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
        <!--begin::File Structure-->
        <div class="card card-flush py-4">
            <!--begin::Card header-->
            <div class="card-header">
                <div class="card-title">
                    <h2>Fail</h2>
                </div>
            </div>
            <!--end::Card header-->
            <div class="separator"></div>
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-10">
                    <div id="jstree">
                        <ul>
                            <li data-tab="tab-wl" data-jstree='{ "opened" : true }'>
                                Projek
                                <ul>
                                    <li data-tab="tab-wl" data-jstree='{ "icon" : "fad fa-road text-primary fs-2" }'>
                                        Izin Lalu
                                    </li>
                                    <li data-tab="tab-permit" data-jstree='{ "icon" : "fad fa-file-certificate text-primary fs-2" }'>
                                        Permit Kerja
                                    </li>
                                    <li data-tab="tab-notice" data-jstree='{ "icon" : "fad fa-folder-open text-primary fs-2" }'>
                                        Notis 
                                    </li>
                                    <li data-tab="tab-cpc" data-jstree='{ "icon" : "fad fa-file-check text-primary fs-2" }'>
                                        CPC
                                    </li>
                                    <li data-tab="tab-ccc" data-jstree='{ "icon" : "fad fa-file-contract text-primary fs-2" }'>
                                        CCC
                                    </li>
                                    <li data-tab="tab-wc" data-jstree='{ "icon" : "fad fa-money-check-pen text-primary fs-2" }'>
                                        Pemulangan Wang Cagaran
                                    </li>
                                    <!-- <li data-jstree='{ "icon" : "fad fa-folder-open text-success fs-4" }'>
                                        CPC
                                        <ul>
                                            <li data-jstree='{ "disabled" : true }'>
                                                Disabled Node
                                            </li>
                                            <li data-jstree='{ "type" : "file" }'>
                                                Another node
                                            </li>
                                        </ul>
                                    </li> -->
                                </ul>
                            </li>
                            <li data-tab="tab-udm">
                                Ukur
                                <ul>
                                    <li data-tab="tab-udm" data-jstree='{ "icon" : "fad fa-files text-primary fs-2" }'>
                                        UDM
                                    </li>
                                    <li data-tab="tab-tmp" data-jstree='{ "icon" : "fad fa-file text-primary fs-2" }'>
                                        TMP
                                    </li>
                                    <li data-tab="tab-ab" data-jstree='{ "icon" : "fad fa-folder-open text-primary fs-2" }'>
                                        As Built
                                    </li>
                                </ul>
                            </li>
                            <li data-tab="tab-plan">
                                Pelan
                            </li>
                            <!-- <li data-jstree='{ "type" : "file" }'>
                                <a href="#kt_vtab_plan"> Plan </a>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::File Structure-->
    </div>
    <!--end::List File column-->

    <!--begin::View File column-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <!--begin::myTabContent-->
        <div id="myTabContent">
            <!--begin::tab Wayleave-->
            <div id="tab-wl" class="doc-content current">
                <!--begin::Card details-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="me-2">Senarai Lampiran</h2>
                            <span class="fs-3 fw-bold text-gray-400">(Permohonan Izin Lalu)</span>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <div class="separator mb-5"></div>

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                    <?php 
                        $attachWy =  ProjectDetails::getAttachments($detail['system_id'], 1);
                        if (count($attachWy) > 1) { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                <?php 
                                    foreach($attachWy as $row) { 
                                        $date = $row['attachment_date'];
                                        $timestamp = strtotime($date);
                                        $current_timestamp = time();
                                        $elapsed_seconds = $current_timestamp - $timestamp;

                                        if ($elapsed_seconds < 60) {
                                            $time_ago = "Just Now";
                                        } else if ($elapsed_seconds < 3600) {
                                            $elapsed_minutes = floor($elapsed_seconds / 60);
                                            $time_ago = $elapsed_minutes . " Minit Lepas";
                                        } else if ($elapsed_seconds < 86400) {
                                            $elapsed_hours = floor($elapsed_seconds / 3600);
                                            $time_ago = $elapsed_hours . " Jam Lepas";
                                        } else if ($elapsed_seconds < 2592000) {
                                            $elapsed_hours = floor($elapsed_seconds / 86400);
                                            $time_ago = $elapsed_hours . " Hari Lepas";
                                        } else if ($elapsed_seconds < 31104000) {
                                            $elapsed_hours = floor($elapsed_seconds / 2592000);
                                            $time_ago = $elapsed_hours . " Bulan Lepas";
                                        } else {
                                            $elapsed_days = floor($elapsed_seconds / 31104000);
                                            $time_ago = $elapsed_days . " Tahun Lepas";
                                        }
                                ?>
                                        <!--begin::Col-->
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <!--begin::Card-->
                                            <div class="card h-100 hover-scale ">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="pdf" href="#pdf-<?php echo $row['id']?>">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-45px mb-3 mt-1">
                                                            <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->
                                                        <!--begin::Title-->
                                                        <div class="fs-6 fw-bold mb-2"><?php echo $row['details'] ?></div>
                                                        <!--end::Title-->

                                                        <!--begin::Description-->
                                                        <div class="fs-7 fw-semibold text-gray-400"><?php echo $time_ago ?></div>
                                                        <!--end::Description-->

                                                        <!--begin::Action-->
                                                        <!-- <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow min-h-170px">
                                                            <i class="fad fa-print text-white fs-2x"></i>
                                                        </div> -->
                                                        <!--end::Action-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->

                                        <div style="display: none;">
                                            <div id="pdf-<?php echo $row['id']?>">
                                                <iframe
                                                    class="embed-responsive-item card-rounded"
                                                    src="/components/files/<?php echo $row['url']?>/<?php echo $row['attachment_type']?> "
                                                    width="940"
                                                    height="680"
                                                    frameBorder="0"
                                                    allow="autoplay; fullscreen"
                                                    allowfullscreen="allowfullscreen"
                                                ></iframe>
                                            </div>
                                        </div>

                                <?php } ?>
                            </div>
                            <!--end:Row-->
                        <?php } else { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                <!--begin::Empty-->
                                <div data-kt-search-element="empty" class="text-center">
                                    <!--begin::Message-->
                                    <div class="fw-semibold py-10">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Lampiran</div>
                                        <div class="text-muted fs-6">Permohonan Izin Lalu...</div>
                                    </div>
                                    <!--end::Message-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-5">
                                        <img src="assets/media/illustrations/empty/07.svg" alt="" class="w-100 h-200px" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Empty-->
                            </div>
                            <!--end:Row-->
                        <?php } ?>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card details-->
            </div>
            <!--end::tab Wayleave-->

            <!--begin::tab Permit-->
            <div id="tab-permit" class="doc-content">
                <!--begin::Card details-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="me-2">Senarai Lampiran</h2>
                            <span class="fs-3 fw-bold text-gray-400">(Permit Kerja)</span>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <div class="separator mb-5"></div>

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                    <?php 
                        $attachPK =  ProjectDetails::getAttachments($detail['system_id'], 2);
                        if (count($attachPK) > 1) { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                <?php 
                                    foreach($attachPK as $row) { 
                                        $date = $row['attachment_date'];
                                        $timestamp = strtotime($date);
                                        $current_timestamp = time();
                                        $elapsed_seconds = $current_timestamp - $timestamp;

                                        if ($elapsed_seconds < 60) {
                                            $time_ago = "Just Now";
                                        } else if ($elapsed_seconds < 3600) {
                                            $elapsed_minutes = floor($elapsed_seconds / 60);
                                            $time_ago = $elapsed_minutes . " Minit Lepas";
                                        } else if ($elapsed_seconds < 86400) {
                                            $elapsed_hours = floor($elapsed_seconds / 3600);
                                            $time_ago = $elapsed_hours . " Jam Lepas";
                                        } else if ($elapsed_seconds < 2592000) {
                                            $elapsed_hours = floor($elapsed_seconds / 86400);
                                            $time_ago = $elapsed_hours . " Hari Lepas";
                                        } else if ($elapsed_seconds < 31104000) {
                                            $elapsed_hours = floor($elapsed_seconds / 2592000);
                                            $time_ago = $elapsed_hours . " Bulan Lepas";
                                        } else {
                                            $elapsed_days = floor($elapsed_seconds / 31104000);
                                            $time_ago = $elapsed_days . " Tahun Lepas";
                                        }
                                ?>
                                        <!--begin::Col-->
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <!--begin::Card-->
                                            <div class="card h-100 hover-scale ">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="pdf" href="#pdf-<?php echo $row['id']?>">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-45px mb-3 mt-1">
                                                            <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->
                                                        <!--begin::Title-->
                                                        <div class="fs-6 fw-bold mb-2"><?php echo $row['details'] ?></div>
                                                        <!--end::Title-->

                                                        <!--begin::Description-->
                                                        <div class="fs-7 fw-semibold text-gray-400"><?php echo $time_ago ?></div>
                                                        <!--end::Description-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->

                                <?php } ?>
                            </div>
                            <!--end:Row-->
                        <?php } else { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                <!--begin::Empty-->
                                <div data-kt-search-element="empty" class="text-center">
                                    <!--begin::Message-->
                                    <div class="fw-semibold py-10">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Lampiran</div>
                                        <div class="text-muted fs-6">Permohonan Permit Kerja...</div>
                                    </div>
                                    <!--end::Message-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-5">
                                        <img src="assets/media/illustrations/empty/07.svg" alt="" class="w-100 h-200px" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Empty-->
                            </div>
                            <!--end:Row-->
                        <?php } ?>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card details-->
            </div>
            <!--end::tab Permit-->

            <!--begin::tab Notice-->
            <div id="tab-notice" class="doc-content">
                <!--begin::Card details-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="me-2">Senarai Lampiran</h2>
                            <span class="fs-3 fw-bold text-gray-400">(Notis)</span>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <div class="separator mb-5"></div>

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                    <?php 
                        $attachNtc =  ProjectDetails::getAttachments($detail['system_id'], 3);
                        if (count($attachNtc) > 1) { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                <?php 
                                    foreach($attachNtc as $row) { 
                                        $date = $row['attachment_date'];
                                        $timestamp = strtotime($date);
                                        $current_timestamp = time();
                                        $elapsed_seconds = $current_timestamp - $timestamp;

                                        if ($elapsed_seconds < 60) {
                                            $time_ago = "Just Now";
                                        } else if ($elapsed_seconds < 3600) {
                                            $elapsed_minutes = floor($elapsed_seconds / 60);
                                            $time_ago = $elapsed_minutes . " Minit Lepas";
                                        } else if ($elapsed_seconds < 86400) {
                                            $elapsed_hours = floor($elapsed_seconds / 3600);
                                            $time_ago = $elapsed_hours . " Jam Lepas";
                                        } else if ($elapsed_seconds < 2592000) {
                                            $elapsed_hours = floor($elapsed_seconds / 86400);
                                            $time_ago = $elapsed_hours . " Hari Lepas";
                                        } else if ($elapsed_seconds < 31104000) {
                                            $elapsed_hours = floor($elapsed_seconds / 2592000);
                                            $time_ago = $elapsed_hours . " Bulan Lepas";
                                        } else {
                                            $elapsed_days = floor($elapsed_seconds / 31104000);
                                            $time_ago = $elapsed_days . " Tahun Lepas";
                                        }
                                ?>
                                        <!--begin::Col-->
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <!--begin::Card-->
                                            <div class="card h-100 hover-scale ">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="pdf" href="#pdf-<?php echo $row['id']?>">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-45px mb-3 mt-1">
                                                            <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->
                                                        <!--begin::Title-->
                                                        <div class="fs-6 fw-bold mb-2"><?php echo $row['details'] ?></div>
                                                        <!--end::Title-->

                                                        <!--begin::Description-->
                                                        <div class="fs-7 fw-semibold text-gray-400"><?php echo $time_ago ?></div>
                                                        <!--end::Description-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->

                                <?php } ?>
                            </div>
                            <!--end:Row-->

                        <?php } else { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                <!--begin::Empty-->
                                <div data-kt-search-element="empty" class="text-center">
                                    <!--begin::Message-->
                                    <div class="fw-semibold py-10">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Lampiran</div>
                                        <div class="text-muted fs-6">Bagi Notis...</div>
                                    </div>
                                    <!--end::Message-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-5">
                                        <img src="assets/media/illustrations/empty/07.svg" alt="" class="w-100 h-200px" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Empty-->
                            </div>
                            <!--end:Row-->
                        <?php } ?>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card details-->
            </div>
            <!--end::tab Notice-->

            <!--begin::tab CPC-->
            <div id="tab-cpc" class="doc-content">
                <!--begin::Card details-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="me-2">Senarai Lampiran</h2>
                            <span class="fs-3 fw-bold text-gray-400">(Sijil Siap Kerja (CPC))</span>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <div class="separator mb-5"></div>

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                    <?php 
                        $attachCPC =  ProjectDetails::getAttachments($detail['system_id'], 4);
                        if (count($attachCPC) > 1) { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                <?php 
                                    foreach($attachCPC as $row) { 
                                        $date = $row['attachment_date'];
                                        $timestamp = strtotime($date);
                                        $current_timestamp = time();
                                        $elapsed_seconds = $current_timestamp - $timestamp;

                                        if ($elapsed_seconds < 60) {
                                            $time_ago = "Just Now";
                                        } else if ($elapsed_seconds < 3600) {
                                            $elapsed_minutes = floor($elapsed_seconds / 60);
                                            $time_ago = $elapsed_minutes . " Minit Lepas";
                                        } else if ($elapsed_seconds < 86400) {
                                            $elapsed_hours = floor($elapsed_seconds / 3600);
                                            $time_ago = $elapsed_hours . " Jam Lepas";
                                        } else if ($elapsed_seconds < 2592000) {
                                            $elapsed_hours = floor($elapsed_seconds / 86400);
                                            $time_ago = $elapsed_hours . " Hari Lepas";
                                        } else if ($elapsed_seconds < 31104000) {
                                            $elapsed_hours = floor($elapsed_seconds / 2592000);
                                            $time_ago = $elapsed_hours . " Bulan Lepas";
                                        } else {
                                            $elapsed_days = floor($elapsed_seconds / 31104000);
                                            $time_ago = $elapsed_days . " Tahun Lepas";
                                        }
                                ?>
                                        <!--begin::Col-->
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <!--begin::Card-->
                                            <div class="card h-100 hover-scale ">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="pdf" href="#pdf-<?php echo $row['id']?>">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-45px mb-3 mt-1">
                                                            <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->
                                                        <!--begin::Title-->
                                                        <div class="fs-6 fw-bold mb-2"><?php echo $row['details'] ?></div>
                                                        <!--end::Title-->

                                                        <!--begin::Description-->
                                                        <div class="fs-7 fw-semibold text-gray-400"><?php echo $time_ago ?></div>
                                                        <!--end::Description-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->

                                <?php } ?>
                            </div>
                            <!--end:Row-->
                        <?php } else { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                <!--begin::Empty-->
                                <div data-kt-search-element="empty" class="text-center">
                                    <!--begin::Message-->
                                    <div class="fw-semibold py-10">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Lampiran</div>
                                        <div class="text-muted fs-6">CPC...</div>
                                    </div>
                                    <!--end::Message-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-5">
                                        <img src="assets/media/illustrations/empty/07.svg" alt="" class="w-100 h-200px" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Empty-->
                            </div>
                            <!--end:Row-->
                        <?php } ?>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card details-->
            </div>
            <!--end::tab CPC-->

            <!--begin::tab CCC-->
            <div id="tab-ccc" class="doc-content">
                <!--begin::Card details-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="me-2">Senarai Lampiran</h2>
                            <span class="fs-3 fw-bold text-gray-400">(Sijil Sempurna Kerja (CCC))</span>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <div class="separator mb-5"></div>

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                    <?php 
                        $attachCCC =  ProjectDetails::getAttachments($detail['system_id'], 5);
                        if (count($attachCCC) > 1) { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                <?php 
                                    foreach($attachCCC as $row) { 
                                        $date = $row['attachment_date'];
                                        $timestamp = strtotime($date);
                                        $current_timestamp = time();
                                        $elapsed_seconds = $current_timestamp - $timestamp;

                                        if ($elapsed_seconds < 60) {
                                            $time_ago = "Just Now";
                                        } else if ($elapsed_seconds < 3600) {
                                            $elapsed_minutes = floor($elapsed_seconds / 60);
                                            $time_ago = $elapsed_minutes . " Minit Lepas";
                                        } else if ($elapsed_seconds < 86400) {
                                            $elapsed_hours = floor($elapsed_seconds / 3600);
                                            $time_ago = $elapsed_hours . " Jam Lepas";
                                        } else if ($elapsed_seconds < 2592000) {
                                            $elapsed_hours = floor($elapsed_seconds / 86400);
                                            $time_ago = $elapsed_hours . " Hari Lepas";
                                        } else if ($elapsed_seconds < 31104000) {
                                            $elapsed_hours = floor($elapsed_seconds / 2592000);
                                            $time_ago = $elapsed_hours . " Bulan Lepas";
                                        } else {
                                            $elapsed_days = floor($elapsed_seconds / 31104000);
                                            $time_ago = $elapsed_days . " Tahun Lepas";
                                        }
                                ?>
                                        <!--begin::Col-->
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <!--begin::Card-->
                                            <div class="card h-100 hover-scale ">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="pdf" href="#pdf-<?php echo $row['id']?>">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-45px mb-3 mt-1">
                                                            <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->
                                                        <!--begin::Title-->
                                                        <div class="fs-6 fw-bold mb-2"><?php echo $row['details'] ?></div>
                                                        <!--end::Title-->

                                                        <!--begin::Description-->
                                                        <div class="fs-7 fw-semibold text-gray-400"><?php echo $time_ago ?></div>
                                                        <!--end::Description-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->

                                <?php } ?>
                            </div>
                            <!--end:Row-->
                        <?php } else { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                <!--begin::Empty-->
                                <div data-kt-search-element="empty" class="text-center">
                                    <!--begin::Message-->
                                    <div class="fw-semibold py-10">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Lampiran</div>
                                        <div class="text-muted fs-6">Bagi CCC...</div>
                                    </div>
                                    <!--end::Message-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-5">
                                        <img src="assets/media/illustrations/empty/07.svg" alt="" class="w-100 h-200px" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Empty-->
                            </div>
                            <!--end:Row-->
                        <?php } ?>
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card details-->
            </div>
            <!--end::tab CCC-->

            <!--begin::tab WC-->
            <div id="tab-wc" class="doc-content">
                <!--begin::Card details-->
                <div class="card card-flush py-4">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="me-2">Senarai Lampiran</h2>
                            <span class="fs-3 fw-bold text-gray-400">(Pemulangan Wang Cagaran)</span>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <div class="separator mb-5"></div>

                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                    <?php 
                        $attachWC =  ProjectDetails::getAttachments($detail['system_id'], 5);
                        if (count($attachWC) > 1) { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                                <?php 
                                    foreach($attachWC as $row) { 
                                        $date = $row['attachment_date'];
                                        $timestamp = strtotime($date);
                                        $current_timestamp = time();
                                        $elapsed_seconds = $current_timestamp - $timestamp;

                                        if ($elapsed_seconds < 60) {
                                            $time_ago = "Just Now";
                                        } else if ($elapsed_seconds < 3600) {
                                            $elapsed_minutes = floor($elapsed_seconds / 60);
                                            $time_ago = $elapsed_minutes . " Minit Lepas";
                                        } else if ($elapsed_seconds < 86400) {
                                            $elapsed_hours = floor($elapsed_seconds / 3600);
                                            $time_ago = $elapsed_hours . " Jam Lepas";
                                        } else if ($elapsed_seconds < 2592000) {
                                            $elapsed_hours = floor($elapsed_seconds / 86400);
                                            $time_ago = $elapsed_hours . " Hari Lepas";
                                        } else if ($elapsed_seconds < 31104000) {
                                            $elapsed_hours = floor($elapsed_seconds / 2592000);
                                            $time_ago = $elapsed_hours . " Bulan Lepas";
                                        } else {
                                            $elapsed_days = floor($elapsed_seconds / 31104000);
                                            $time_ago = $elapsed_days . " Tahun Lepas";
                                        }
                                ?>
                                        <!--begin::Col-->
                                        <div class="col-md-6 col-lg-4 col-xl-3">
                                            <!--begin::Card-->
                                            <div class="card h-100 hover-scale ">
                                                <!--begin::Card body-->
                                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                                    <!--begin::Overlay-->
                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="pdf" href="#pdf-<?php echo $row['id']?>">
                                                        <!--begin::Image-->
                                                        <div class="overlay-wrapper symbol symbol-45px mb-3 mt-1">
                                                            <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" />
                                                            <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" />
                                                        </div>
                                                        <!--end::Image-->
                                                        <!--begin::Title-->
                                                        <div class="fs-6 fw-bold mb-2"><?php echo $row['details'] ?></div>
                                                        <!--end::Title-->

                                                        <!--begin::Description-->
                                                        <div class="fs-7 fw-semibold text-gray-400"><?php echo $time_ago ?></div>
                                                        <!--end::Description-->
                                                    </a>
                                                    <!--end::Overlay-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Card-->
                                        </div>
                                        <!--end::Col-->

                                <?php } ?>
                            </div>
                            <!--end:Row-->
                        <?php } else { ?>
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                <!--begin::Empty-->
                                <div data-kt-search-element="empty" class="text-center">
                                    <!--begin::Message-->
                                    <div class="fw-semibold py-10">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Lampiran</div>
                                        <div class="text-muted fs-6">Pengeluaran Wang Cagaran...</div>
                                    </div>
                                    <!--end::Message-->
                                    <!--begin::Illustration-->
                                    <div class="text-center px-5">
                                        <img src="assets/media/illustrations/empty/07.svg" alt="" class="w-100 h-200px" />
                                    </div>
                                    <!--end::Illustration-->
                                </div>
                                <!--end::Empty-->
                            </div>
                            <!--end:Row-->
                        <?php } ?>
                    </div>
                    <!--end::Card body-->

                </div>
                <!--end::Card details-->
            </div>
            <!--end::tab WC-->
        </div>
        <!--end::myTabContent-->  
    </div>
    <!--end::View File column-->
</div>
<!--end::Content-->

