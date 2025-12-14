<?php
$Role = new Roles();
$List = new Lists();
$username = $_SESSION['username'];
?>

<!--begin::Products-->
 <div class="card card-flush">
     <!--begin::Card header-->
     <div class="card-header align-items-center py-5 gap-2 gap-md-5">
         <!--begin::Card title-->
         <div class="card-title">
             <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <i class="fad fa-search position-absolute ms-4"></i>
                <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14"
                    placeholder="Carian Kumpulan..." />
            </div>
            <!--end::Search-->
         </div>
         <!--end::Card title-->
         <!--begin::Card toolbar-->
         <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
             <button type="button" href="#" class="btn btn-flex flex-center btn-primary w-40px w-md-auto h-40px px-0 px-md-6" data-bs-toggle="modal" data-bs-target="#survey-add-team">
                 <i class="fad fa-circle-plus fs-4"></i>
                 <span class="d-none d-md-inline">Tambah</span>
             </button>
         </div>
         <!--end::Card toolbar-->
     </div>
     <!--end::Card header-->
     <!--begin::Card body-->
     <div class="card-body pt-0">
         <!--begin::Table-->
         <table class="table align-middle table-row-dashed fs-6 gy-5" id="survey-team">
             <!--begin::Table head-->
             <thead>
                 <!--begin::Table row-->
                 <tr class="text-center text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                     <th class="text-center">Kumpulan</th>
                     <th class="text-center">Bilangan</th>
                     <th class="text-center">Ahli</th>
                     <th class="text-center">Kemaskini</th>
                 </tr>
                 <!--end::Table row-->
             </thead>
             <!--end::Table head-->
             <!--begin::Table body-->
             <tbody class="fw-semibold text-gray-700">
                <?php
                foreach ($data as $field) {
                    // $provider = json_decode(file_get_contents("https://app.kudr.my/gateway/internal/provider/$field->provider_id"));
                    // $districts = explode(",", $field->districts);
                    // $date = new DateTime($field->created_at);

                    echo '<tr>';
                    // NOTE - Kumpulan
                    echo '<td class="fw-bold text-center">' . $field->survey_team . '</td>';
                    // NOTE - Bilangan
                    $nums = $field->team_members ? count(explode(",", $field->team_members)) : 0;
                    echo '<td class="fw-bold text-center">' . number_format($nums, 0, '.', ',') . ' orang</td>';
                    // NOTE - Ahli
                    echo '<td class="text-center">';
                    if ($field->team_members) {
                        $named = substr($field->team_members, 1, -1);
                        $members = explode(",", $named);

                        $output = "";

                        $colors = [
                            "success",
                            "danger",
                            "warning",
                            "info",
                            "dark",
                            "primary",
                            "secondary",
                        ];

                        foreach ($members as $member) {
                            $member = trim($member);
                            // $initials = strtoupper(substr($member, 0, 1));
                            $randomIndex = rand(0, count($colors) - 1);
                            $state = $colors[$randomIndex];

                            array_splice($colors, $randomIndex, 1); // Remove the selected color from the array
                            $memberName = Survey::getSurveyName($member)[0];
                            $initials = strtoupper(substr($memberName, 0, 1));
                
                            $output .= '<div class="symbol symbol-circle symbol-40px">';
                            $output .= '<div class="symbol-label fs-5 fw-semibold bg-' .
                                $state .
                                ' text-inverse-' .
                                $state .
                                '" data-bs-toggle="tooltip" data-bs-placement="bottom" title="' .
                                $memberName .
                                '">' .
                                $initials .
                                '</div>';
                            $output .= '</div>';
                        }

                        echo $output;
                    }
                    echo '</td>';
                    // NOTE - Kemaskini
                    echo '<td>';
                    if ($field->id) {
                        echo '<div class="text-center">';
                        echo '<button type="button" class="btn btn-icon btn-light-primary me-2" data-bs-toggle="modal" data-bs-target="#action-edit-' . $field->id . '" data-row-id="' . $field->id . '">';
                        echo '<i class="fad fa-pen fs-2"></i>';
                        echo '</button>';
                        echo '<button type="button" id="delete_button" data-row-id="' . $field->id . '" class="btn btn-icon btn-light-danger confirm-delete-btn">';
                        echo '<i class="fad fa-trash-can fs-2"></i>';
                        echo '</button>';
                        echo '</div>';
                    }
                    echo '</td>';

                }
                ?>
             </tbody>
             <!--end::Table body-->
         </table>
         <!--end::Table-->
     </div>
     <!--end::Card body-->
 </div>
 <!--end::Products-->