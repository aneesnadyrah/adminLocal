<div class="d-flex flex-column flex-lg-row">
    <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
        <div class="card">
            <div class="card-body py-12 px-20 print-content-only">
                <div class="d-flex justify-content-center">
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-column align-items-start flex-xxl-row mt-10 mb-20 ms-10 me-5">
                            <!--begin::Input group-->
                            <div class="d-flex align-items-center justify-content-start flex-equal order-3 fw-row mt-10 ms-10">
                                <!--begin::Input-->
                                <div class="position-relative d-flex align-items-center w-250px h-100px">
                                    <img class="mw-300px"
                                        src="assets/media/logos/<?php echo strtolower($system->App->tenant) ?>-default.svg"
                                        alt="image" />
                                </div>
                                <!--end::Input-->

                                <div style="width: 180px;"></div>
                                <!-- Add a div with a specific width to create space -->

                                <!--begin::Input-->
                                <div class="position-relative d-flex align-items-center w-200px me-10">
                                    <img class="mw-150px"
                                        src="<?php echo General::getProvider($provider)->logo; ?>" />
                                </div>
                                <!--end::Input-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Wrapper-->
                </div>
                <div class="d-flex justify-content-center align-items-center mb-15">
                        <!--begin::Wrapper-->
                        <div class="mb-0">
                            <!--begin::Wrapper-->
                            <div
                                class="d-flex flex-stack flex-column justify-content-center align-items-center flex-xxl-row mb-10 fw-row">
                                <!--begin::Section-->
                                <div class="mw-500px">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-10 justify-content-center">
                                        <div class="fw-bold fs-1">LAPORAN KERJA LAPANGAN
                                        </div>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-1  justify-content-center text-center">
                                        <div class="fs-5 ">NO. RUJUKAN</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center fs-5">
                                            <?php echo $reference_no; ?>
                                        </div>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack  justify-content-center text-center">
                                        <div class="fs-5 ">TARIKH DIUKUR</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center fs-5"
                                            style="text-transform: uppercase;">
                                            <?php echo $start_date; ?>
                                        </div>
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Wrapper-->
                </div>
                <div class="table-responsive mt-10 mb-10">
                    <table class="table table-bordered border-gray-800 gy-4 gs-4">
                        <tbody>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">TAJUK PERMOHONAN</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php echo $title; ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">JALAN TERLIBAT</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php echo $uniqueRoadNames; ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">TARIKH MULA / TARIKH TAMAT</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php echo 'MULA : '.$lowest; ?><br>
                                    <?php echo 'AKHIR : '.$biggest; ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">JARAK MOHON / JARAK DIUKUR</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php echo 'JARAK MOHON : '.$application_length.' m'; ?><br>
                                    <?php echo 'JARAK DIUKUR : '.$survey_length.' m'; ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">KOORDINAT MULA / KOORDINAT AKHIR</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php echo 'Koordinat Mula : '.$kordinatMula; ?><br>
                                    <?php echo 'Koordinat Akhir : '.$kordinatAkhir; ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">KERJA-KERJA YANG DILAKUKAN DI LAPANGAN</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php 
                                    if(isset($workDone)) {
                                        $work = explode(',', trim($workDone, '{}'));
                                        $i = 1;
                                        foreach ($work as $workDesc) {
                                            foreach ($surveyWorkList as $workItem) {
                                                if ($workItem->id == $workDesc) {
                                                    echo $workItem->name .'<br>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">PERALATAN KERJA YANG DIGUNAKAN</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php 
                                    if(isset($equipementUsed)) {
                                        $equip = explode(',', trim($equipementUsed, '{}'));
                                        $i = 1;
                                        foreach ($equip as $equipmentDesc) {
                                            foreach ($surveyEquipmentList as $list) {
                                                if ($list->id == $equipmentDesc) {
                                                    echo $i++ .'. '. $list->equipment_name .'<br>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase;">SENARAI ANGGOTA YANG TERLIBAT</td>
                                <td style="text-transform: uppercase; width: 70%;">
                                    <?php echo 'AHLI KUMPULAN '.$survey_team.'<br>'; 
                                    $i = 1;
                                    foreach ($memberList as $memberItem) {
                                        echo '<br>'.$i.'. '.$memberItem->first_name.' '.$memberItem->last_name;
                                        $i++;
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($imgcat01)): ?>
                    <div class="row gx-20 mb-5">
                        <span class="fw-bold fs-4 mb-5" style="text-transform:uppercase;">Kerja-kerja yang dilakukan di
                            lapangan</span>
                    </div>
                <div class="table-responsive mb-10">
                    <table class="table table-bordered border-gray-800 gy-4 gs-4">
                        <tbody>
                            <?php for ($i = 0; $i < $countImgCat01; $i += 2): ?>
                                    <tr class="fw-semibold fs-6">
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- Center the content -->
                                                <!--begin::Input group-->
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!--begin::Input-->
                                                    <div class="position-relative d-flex align-items-center"
                                                        style="height: 300px; width: 300px;">
                                                        <img class="image-fit" src="<?php echo base64_decode($imgcat01[$i]->url); ?>"
                                                            alt="image"
                                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- Center the content -->
                                                <!--begin::Input group-->
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!--begin::Input-->
                                                    <div class="position-relative d-flex align-items-center"
                                                        style="height: 300px; width: 300px;">
                                                        <?php if (isset($imgcat01[$i+1])): ?>
                                                            <img class="image-fit" src="<?php echo base64_decode($imgcat01[$i+1]->url); ?>"
                                                                alt="image"
                                                                style="width: 100%; height: 100%; object-fit: cover;" />
                                                        <?php endif; ?>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo isset($imgcat01[$i])? $imgcat01[$i]->description:''; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo isset($imgcat01[$i+1])? $imgcat01[$i+1]->description:''; ?>
                                        </td>
                                    </tr>
                                    <?php
                                endfor;?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <?php if (!empty($imgcat02)): ?>
                    <div class="row gx-20 mb-5">
                        <span class="fw-bold fs-4 mb-5" style="text-transform:uppercase;">Kerja-kerja pengukuran dan
                                penandaan chainage sedang dijalankan</span>
                    </div>
                <div class="table-responsive mb-10">
                    <table class="table table-bordered border-gray-800 gy-4 gs-4">
                        <tbody>
                            <?php for ($i = 0; $i < $countImgCat02; $i += 2): ?>
                                    <tr class="fw-semibold fs-6">
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- Center the content -->
                                                <!--begin::Input group-->
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!--begin::Input-->
                                                    <div class="position-relative d-flex align-items-center"
                                                        style="height: 300px; width: 300px;">
                                                        <img class="image-fit" src="<?php echo base64_decode($imgcat02[$i]->url); ?>"
                                                            alt="image"
                                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- Center the content -->
                                                <!--begin::Input group-->
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!--begin::Input-->
                                                    <div class="position-relative d-flex align-items-center"
                                                        style="height: 300px; width: 300px;">
                                                        <?php if (isset($imgcat02[$i+1])): ?>
                                                            <img class="image-fit" src="<?php echo base64_decode($imgcat02[$i+1]->url); ?>"
                                                                alt="image"
                                                                style="width: 100%; height: 100%; object-fit: cover;" />
                                                        <?php endif; ?>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo (isset($imgcat02[$i]))? $imgcat02[$i]->description:''; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo isset($imgcat02[$i+1])? $imgcat02[$i+1]->description:''; ?>
                                        </td>
                                    </tr>
                                    <?php
                                endfor;?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <?php if (!empty($imgcat03)): ?>
                    <div class="row gx-20 mb-5">
                        <span class="fw-bold fs-4 mb-5" style="text-transform:uppercase;">Kerja-kerja penandaan pegging
                                'right of way' row(diwarnakan <span class="text-danger">merah</span>) dan pegging propose
                                (diwarnakan <span class="text-primary">biru</span>). paip pegging
                                diletakkan selari dengan chainage yang ditanda.</span>
                    </div>
                <div class="table-responsive mb-10">
                    <table class="table table-bordered border-gray-800 gy-4 gs-4">
                        <tbody>
                            <?php for ($i = 0; $i < $countImgCat03; $i += 2): ?>
                                    <tr class="fw-semibold fs-6">
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- Center the content -->
                                                <!--begin::Input group-->
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!--begin::Input-->
                                                    <div class="position-relative d-flex align-items-center"
                                                        style="height: 300px; width: 300px;">
                                                        <img class="image-fit" src="<?php echo base64_decode($imgcat03[$i]->url); ?>"
                                                            alt="image"
                                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <!-- Center the content -->
                                                <!--begin::Input group-->
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!--begin::Input-->
                                                    <div class="position-relative d-flex align-items-center"
                                                        style="height: 300px; width: 300px;">
                                                        <?php if (isset($imgcat03[$i+1])): ?>
                                                            <img class="image-fit" src="<?php echo base64_decode($imgcat03[$i+1]->url); ?>"
                                                                alt="image"
                                                                style="width: 100%; height: 100%; object-fit: cover;" />
                                                        <?php endif; ?>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo (isset($imgcat03[$i]))? $imgcat03[$i]->description:''; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo (isset($imgcat03[$i+1]))? $imgcat03[$i+1]->description:''; ?>
                                        </td>
                                    </tr>
                                    <?php
                                endfor;?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <div class="table-responsive mb-10">
                    <table class="table table-rounded table-row-bordered table-striped border-gray-800 gy-4 gs-4">
                        <tbody class="table-striped">
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase; width: 50%;">
                                    disemak oleh</td>
                                <td style="text-transform: uppercase; width: 50%;">
                                    disahkan oleh</td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase; width: 50%;"></td>
                                <td style="text-transform: uppercase; width: 50%;"></td>
                            </tr>
                            <tr class="fw-semibold fs-6">
                                <td style="text-transform: uppercase; width: 50%;"></td>
                                <td style="text-transform: uppercase; width: 50%;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-lg-auto min-w-lg-350px"></div>
</div>