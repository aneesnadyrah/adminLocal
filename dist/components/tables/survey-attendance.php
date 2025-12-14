<?php 
$attendance = new SurveyAttendanceModel();
$data = $attendance->getSurveyAttendance();
$roleId = $_SESSION['roleId'];
?>
<div class="card card-flush">
    <div class="card-header align-items-center pt-5 gap-2 gap-md-5">
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <!-- <span class="svg-icon fs-1 position-absolute ms-4">...</span> -->
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                    <i width="50" height="50" class="fa-duotone fa-magnifying-glass"></i>
                </span>
                <input type="text" id="searchRujukan" data-table-filter="search" class="form-control form-control-solid w-250px ps-14"
                    placeholder="Carian Permohonan..." />
            </div>
        </div>
    
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="survey-attendance">
                <!--begin::Table head-->
                <thead class="text-center">
                    <tr class="text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th style="text-align:center;">No Rujukan</th>
                        <th style="text-align:center;">Clock In</th>
                        <th style="text-align:center;">Clock Out</th>
                        <th style="text-align:center;">Kumpulan</th>
                        <th style="text-align:center;">Nama</th>
                    </tr>
                </thead>
                <!--end::Table head-->

                <!--begin::Table body-->
                <tbody class="text-gray-700 text-center">
                    <?php if (!empty($data)) : ?>
                        <?php $no = 1; ?>
                        <?php foreach ($data as $row) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['reference_no']); ?></td> 
                                <td><?php 
                                    $timestamp = strtotime($row['created_timestamp']);
                                    echo date('d/m/Y  H:i A', $timestamp); ?>
                                </td>
                                <td><?= htmlspecialchars($row['clock_out'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['survey_team'] ?? '-'); ?></td> 
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No records found</td>
                        </tr>
                    <?php endif; ?>
                    
                </tbody>

            </table>
            <!--end::Table-->
        </div>
    </div>
</div>
