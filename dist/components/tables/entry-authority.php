<div class="card card-flush">
    <div class="card-header align-items-center pt-5 gap-2 gap-md-5">
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <!-- <span class="svg-icon fs-1 position-absolute ms-4">...</span> -->
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                    <i width="50" height="50" class="fa-duotone fa-magnifying-glass"></i>
                </span>
                <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14"
                    placeholder="Carian Permohonan..." />
            </div>
        </div>
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <!--begin::Export dropdown-->
            <!-- <button type="button" class="btn btn-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span class="path2"></span></i>
                Jana Rumusan
            </button> -->
            <!--begin::Menu-->
            <div id="kt_datatable_example_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-kt-export="copy">
                        Copy to clipboard
                    </a>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-kt-export="excel">
                        Jana sebagai Excel
                    </a>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-kt-export="csv">
                        Jana sebagai CSV
                    </a>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-kt-export="pdf">
                        Jana sebagai PDF
                    </a>
                </div>
                <!--end::Menu item-->
            </div>
            <!--end::Menu-->
            <!--end::Export dropdown-->

            <!--begin::Hide default export buttons-->
            <div id="kt_datatable_example_buttons" class="d-none"></div>
            <!--end::Hide default export buttons-->
        </div>
    </div>
    <div class="card-body">

        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="tracker-entry">
            <!--begin::Table head-->
            <thead>
                <tr class="text-center text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th>No Rujukan</th>
                    <th>Pihak Berkuasa</th>
                    <th>Daerah</th>
                    <th>Jarak</th>
                    <th>Status</th>
                    <th>Tarikh</th>
                    <th class="no-export">Tindakan</th>
                    <th class="no-export">Id</th>
                </tr>
            </thead>
            <!--end::Table head-->

            <!--begin::Table body-->
            <tbody class="fw-semibold text-gray-800"></tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->

    </div>
</div>