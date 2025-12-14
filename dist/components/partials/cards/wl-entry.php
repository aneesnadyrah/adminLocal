<?php 
$cleanRange = trim($data->project_date, '[]()');
$project_date = explode(',',$cleanRange);
$project_dateStr = new DateTime($project_date[0]);
$project_dateStrFmt = $project_dateStr->format('d M Y');
$project_dateEnd = new DateTime($project_date[1]);
$project_dateEndFmt = $project_dateEnd->format('d M Y');
?>

<!--begin::Card-->
<div class="card card-flush pt-3 mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header d-flex justify-content-between align-items-center">
        <!--begin::Card title-->
        <div class="card-title">
            <h2 class="fw-bold">Butiran Permohonan</h2>
        </div>
        <!--begin::Card title-->
        <?php
            if($_SESSION["roleId"] == 1){
                echo '<div class="d-flex justify-content-center">';
                echo <<<Action
                    <button class="btn btn-flex flex-center btn-light me-2" data-edit="butiran">
                        <i class="fa-solid fa-pen-to-square fs-3"></i>
                    </button>
                Action;
                echo '</div>';
            }
        ?>
    </div>
    <!--end::Card header-->
    <div class="separator separator-dashed"></div>
    <!--begin::Card body-->
    <form id="application-form">
        <div class="card-body">
        <!-- Field 1: Project Title -->
            <div class="mb-5">
                <h3 class="read-only text-justify text-gray-900 fs-4">
                    <span data-application="title"><?= $data->project_title ?></span>
                </h3>
            </div>

            <!--begin::Details-->
            <div class="d-flex flex-wrap">
                <!--begin::Row-->
                <div class="flex-equal me-5">
                    <?php
                    switch ($data->type_application) {
                        case "BF":
                            $type = "Brownfield";
                            $color = "primary";
                        break;
                        case "GF":
                            $type = "Greenfield";
                            $color = "success";
                        break;
                        case "EG":
                            $type = "Kecemasan";
                            $color = "danger";
                        break;
                        default:
                            $type = "-";
                            $color = "dark";
                        break;
                    }
                    ?>
                    <!--begin::Details-->
                    <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                        <tbody>
                            <!--begin::Row-->
                            <tr class="editable-field" data-field-name="site_start">
                                <td class="text-gray-500">Nama Tapak A :</td>
                                <td class="read-only text-gray-800">
                                    <span data-application="site_start"><?= $data->site_start ?></span>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Jenis Permohonan :</td>
                                <td class="text-gray-800">
                                <span class="badge badge-light-<?= $color ?> me-auto"><?= $type ?></span>
                                </td>

                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Kos Projek (RM) :</td>
                                <td class="text-gray-800">
                                <span data-application="project_costs"><?= number_format($data->project_costs, 2, '.', ',') ?></span>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Link ID / No Projek :</td>
                                <td class="text-gray-800">
                                    <span data-application="link_id"><?= $data->link_id ?></span>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Status Permohonan :</td>
                                <td class="text-gray-800">
                                    <span class="badge badge-secondary me-auto"><?= $data->status ?></span>
                                </td>

                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Jarak Permohonan (m) :</td>
                                <td class="text-gray-800">
                                    <span data-application="length"><?= $data->application_length ?></span>
                                </td>
                            </tr>
			    <tr>
    				<td class="text-gray-500">Tarikh Mula :</td>
    				<td class="text-gray-800">
        				<span data-application="date"><?php echo $project_dateStrFmt; ?></span>
    				</td>
			    </tr>

                            <!--end::Row-->
                        </tbody>
                    </table>
                    <!--end::Details-->
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="flex-equal">
                    <?php
                        switch ($data->application_code){
                            case "KT":
                                $type = "Kerja Terancang";
                                $color = "info";
                            break;
                            case "KP":
                                $type = "Kerja Pengalihan";
                                $color = "warning";
                            break;
                            default:
                                $type = "-";
                                $color = "dark";
                            break;
                        }
                    ?>
                    <!--begin::Details-->
                    <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                        <tbody>
                            <!--begin::Row-->
                            <tr class="editable-field" data-field-name="site_end">
                                <td class="text-gray-500">Nama Tapak B :</td>
                                <td class="text-gray-800">
                                    <span data-application="site_end"><?= $data->site_end ?></span>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Kategori Permohonan :</td>
                                <td class="text-gray-800">
                                <span class="badge badge-light-<?= $color ?> me-auto"><?= $type ?></span>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Tag Permohonan :</td>
                                <td class="text-gray-800">
                                    <?php
                                    $tags = explode(',', $data->tags);
                                    foreach ($tags as $tag){
                                        echo '<span class="badge badge-light-info me-auto">' . $tag . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Cara Pembayaran :</td>
                                <td class="text-gray-800">
                                    <?php 
                                    switch ($data->payment_method) {
                                        case 1:
                                            echo 'Pindahan Wang';
                                            break;
                                        case 2:
                                            echo 'Invois';
                                            break;
                                        case 3:
                                            echo 'QR DuitNow';
                                            break;
                                        case 4:
                                            echo 'Atas Talian Toyyibpay';
                                            break;
                                        default:
                                            echo '-';
                                            break;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Daerah Terlibat :</td>
                                <td class="text-gray-800" id="de-districts">
                                    <?php
                                    $districts = explode(",", $data->districts);
                                    $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                    shuffle($colors);
                                    foreach ($districts as $district) {
                                        echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $district . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!--end::Row-->
                            <!--begin::Row-->
                            <tr>
                                <td class="text-gray-500">Tarikh Permohonan :</td>
                                <td class="text-gray-800">
                                    <span data-application="date"><?= (new DateTime($data->application_date))->format('d M Y') ?></span>
                                </td>
                            </tr>
			    <tr>
    				<td class="text-gray-500">Tarikh Akhir :</td>
    				<td class="text-gray-800">
        				<span data-application="date"><?php echo $project_dateEndFmt; ?></span>
    				</td>
			   </tr>
                            <!--end::Row-->
                        </tbody>
                    </table>
                    <!--end::Details-->
                </div>
                <!--end::Row-->
            </div>
        </div>
    </form>
    <!--end::Card body-->
</div>
<!--end::permohonan-->