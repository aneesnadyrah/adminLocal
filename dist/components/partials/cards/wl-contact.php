<div class="card card-flush pt-3 mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Card title-->
        <div class="card-title">
            <h2 class="fw-bold">Butiran Pegawai</h2>
        </div>
        <!--begin::Card title-->
    </div>
    <!--end::Card header-->
    <div class="separator separator-dashed"></div>
    <!--begin::Card body-->
    <div class="card-body">
        <!--begin::Section Pegawai-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr
                        class="border-bottom border-gray-200 text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="">Nama</th>
                        <th class="">Email</th>
                        <th class="">No Telefon</th>
                        <th class="min-w-200px">Nama Syarikat</th>
                        <th class="">Alamat</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-semibold text-gray-800">
                    <?php
                        foreach ($data as $contact) {
                            echo <<<HTML
                            <tr>
                                <td>
                                    <label class="w-150px">$contact->name</label>
                                    <div class="fw-normal text-gray-600">$contact->type</div>
                                </td>
                                <td>
                                    <span class="badge badge-light-danger">$contact->email</span>
                                </td>
                                <td>$contact->phone</td>
                                <td>
                                    $contact->company_name
                                    <div class="fw-normal text-gray-600">$contact->position</div>
                                </td>
                                <td>
                                    $contact->unit_no,  
                                    $contact->street, <br/>
                                    $contact->postcode, 
                                    $contact->city, <br/>
                                    $contact->state
                                </td>
                            </tr>
                            HTML;
                        }
                    ?>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Section Pegawai-->
    </div>
    <!--end::pegawai-->
</div>