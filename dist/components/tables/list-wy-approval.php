 <!--begin::Products-->
 <div class="card card-flush">
     <!--begin::Card header-->
     <div class="card-header align-items-center py-5 gap-2 gap-md-5">
         <!--begin::Card title-->
         <div class="card-title">
             <!--begin::Search-->
             <div class="d-flex align-items-center position-relative my-1">
                 <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                 <span class="svg-icon svg-icon-1 position-absolute ms-4">
                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                         <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                     </svg>
                 </span>
                 <!--end::Svg Icon-->
                 <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Carian Permohonan..." />
             </div>
             <!--end::Search-->
         </div>
         <!--end::Card title-->
         <!--begin::Card toolbar-->
         <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
             <!--begin::Flatpickr-->
             <div class="input-group w-250px">
                 <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pilih Julat Tarikh" id="table-date-range" />
                 <button class="btn btn-icon btn-light" id="date-range-clear">
                     <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                     <span class="svg-icon svg-icon-2">
                         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                             <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                         </svg>
                     </span>
                     <!--end::Svg Icon-->
                 </button>
             </div>
             <!-- <a  class="btn btn-icon btn-light-info" data-fslightbox="lightbox" data-class="fslightbox-source"
                  href="#view-004118" data-bs-toggle="tooltip" data-bs-placement="bottom" title="BKIL">
                    <i class="fad fa-file-pdf fs-2"></i>
                  </a> -->
             <!--end::Flatpickr-->
         </div>
         <!--end::Card toolbar-->
     </div>
     <!--end::Card header-->
     <!--begin::Card body-->
     <div class="card-body pt-0">
         <!--begin::Table-->
         <table class="table align-middle table-row-dashed fs-6 gy-5" id="wayleave-approval">
             <!--begin::Table head-->
             <thead>
                 <!--begin::Table row-->
                 <tr class="text-center text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                     <th>No Rujukan</th>
                     <th>Pihak Berkuasa</th>
                     <th>Jarak</th>
                     <th>Status</th>
                     <th>Tarikh Surat</th>
                     <th>Tindakan</th>
                 </tr>
                 <!--end::Table row-->
             </thead>
             <!--end::Table head-->
             <!--begin::Table body-->
             <tbody class="fw-semibold text-gray-700"></tbody>
             <!--end::Table body-->
         </table>
         <!--end::Table-->
     </div>
     <!--end::Card body-->
 </div>
 <!--end::Products-->