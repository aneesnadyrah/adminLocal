<div class="card card-flush card-custom bgi-no-repeat bgi-size-contain bgi-position-x-center border-0 h-md-50 mb-5 mb-xl-10" style="background-color: <?php if (isset($bgColor)){echo $bgColor;} else {echo "#c6eaff";} ?>;">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <div class="card-title d-flex flex-column">
            <!--begin::Amount-->
            <span class="fs-2hx fw-bold me-2 lh-1 ls-n2">Kelulusan</span>
            <!--end::Amount-->
            <!--begin::Subtitle-->
            <span class="text-<?php if (isset($progressColor)){echo $progressColor;} else {echo "white opacity-50";} ?> pt-1 fw-semibold fs-6">Kemajuan Kelulusan</span>
            <!--end::Subtitle-->
        </div>
        <!--end::Title-->
    </div>
    <!--end::Header-->
    <!--begin::Card body-->
    <div class="card-body d-flex align-items-end pt-0">
        <!--begin::Progress-->
        <div class="d-flex align-items-center flex-column mt-3 w-100">
            <div class="d-flex justify-content-between fw-bold fs-6 text-<?php if (isset($progressColor)){echo $progressColor;} else {echo "white opacity-50";} ?> w-100 mt-auto mb-2">
                <span>CPC</span>
                <span>72%</span>
            </div>
            <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                <div class="bg-<?php if (isset($progressColor)){echo $progressColor;} else {echo "success";} ?> rounded h-8px" role="progressbar" style="width: 72%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <!--end::Progress-->
    </div>
    <!--end::Card body-->
    <!--begin::Card body-->
    <div class="card-body d-flex align-items-end pt-0">
        <!--begin::Progress-->
        <div class="d-flex align-items-center flex-column mt-3 w-100">
            <div class="d-flex justify-content-between fw-bold fs-6 text-<?php if (isset($progressColor)){echo $progressColor;} else {echo "white opacity-50";} ?> w-100 mt-auto mb-2">
                <span>CCC/CMGD/PWC</span>
                <span>50%</span>
            </div>
            <div class="h-8px mx-3 w-100 bg-light-danger rounded">
                <div class="bg-<?php if (isset($progressColor)){echo $progressColor;} else {echo "success";} ?> rounded h-8px" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <!--end::Progress-->
    </div>
    <!--end::Card body-->
</div>