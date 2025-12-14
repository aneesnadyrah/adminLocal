var createDeposit = (function () {
  // const url = window.location.href;
  // const systemID = url.split("/").pop();

  // Get the full URL
  var url = window.location.href;

  // Split the URL by "/" to get individual segments
  var segments = url.split("/");

  // Find the segment that contains the systemid
  var systemidSegment = segments[segments.length - 1]; // Assuming systemid is the second-to-last segment

  // Split the last segment by "?" to separate the systemid from query parameters
  var parts = systemidSegment.split("?");

  // Extract systemid
  var systemID = parts[0];

  console.log(systemID);
  // Use URLSearchParams to get the value of the aid parameter
  var urlParams = new URLSearchParams(window.location.search);
  var flw_auth_id = urlParams.get("aid");
  console.log(authorityID);

  // const sysId =

  // TODO: new signature script test
  const selectmethod = [];
  const selectitem = [];

  // var systemID = document.querySelector('input[name="system-id"]').value;
  api
    .get("wayleave/deposit/index/" + systemID + "?aid=" + authorityID)
    .then((response) => {
      // console.log(response);
      // console.log(response.work_method);
      //   console.log(JSON.stringify(response.work_method));
      selectitem.push(...response.rp_item);
      console.log(selectitem);

      selectmethod.push(...response.work_method);
      console.log(selectmethod);
      // console.log(response.authority_id);

      //     authorityId.push(...response.authority_id);
      //   console.log(JSON.stringify(authorityId));

      const formattedSM = selectmethod.map((item) => ({
        id: item.method,
        text: item.name,
      }));

      const formattedSI = selectitem
        .filter((item) => item.work_method_parent === null)
        .map((item) => ({
          id: item.name,
          text: item.name,
        }));

      const formattedHDD = selectitem
        .filter((item) => item.work_method_parent === "HDD")
        .map((item) => ({
          id: item.name,
          text: item.name,
        }));

      const formattedGV = selectitem
        .filter((item) => item.work_method_parent === "GV")
        .map((item) => ({
          id: item.name,
          text: item.name,
        }));

      const formattedCW = selectitem
        .filter((item) => item.work_method_parent === "CW")
        .map((item) => ({
          id: item.name,
          text: item.name,
        }));
      const formattedOH = selectitem
        .filter((item) => item.work_method_parent === "OH")
        .map((item) => ({
          id: item.name,
          text: item.name,
        }));

      const formattedDataSelect = [...formattedSM, ...formattedSI];

      //   const authorityId = [1, 5];

      console.log(response.authority_id);
      // const authorityId = response.authority_id;
      const authorityId = response.authority_id.map((item) => ({
        id: item.authority_id,
        name: item.name,
        group:
          item.group === 1
            ? "JKR"
            : item.group === 2
            ? "MD"
            : item.group === 3
            ? "JPS"
            : "Lain-Lain",
        road: item["road_name"]
          ? item["road_name"].replace(/[{}"]/g, "").split(", ")
          : [],
      }));

      console.log(authorityId);

      console.log(formattedDataSelect);

      // document.addEventListener("DOMContentLoaded", function() {
      // var modalStatusOpen = "#modal-open-status-" + modalId.slice(8);
      // var modalOpenStatus = $('input[name="modal-open-status"]').val();
      // var modalOpenStatus = document.querySelector(modalStatusOpen);
      // modalOpenStatus.value = '0';

      // let authCount = 0;

      // if (modalOpenStatus == '0') {
      modalContent(authorityId,formattedDataSelect);

      roadRepeaterInit(
        authorityId,
        formattedDataSelect,
        formattedHDD,
        formattedGV,
        formattedCW,
        formattedOH
      );

      totalLengthRepeaterInit(authorityId);

      // $(modalStatusOpen).val('1');
      // modalOpenStatus.value = '1';
      // console.log(modalOpenStatus);

      // }
      // });
    })
    .catch((error) => {
      console.log(error);
    });

  function modalContent(authorityId,formattedDataSelect) {
    // Stepper lement
    let stepperModal = "#kt_stepper_deposit_return";
    // Stepper lement
    var element = document.querySelector(stepperModal);

    // element setup
    console.log(element);

    var sNavElParent = element.querySelector('[data-rp-stepper="nav"]');
    var sContentElParent = element.querySelector('[data-rp-stepper="content"]');
    //   var modalStatusOpen = "modal-open-status-" + modalId.slice(8);
    //   console.log(modalStatusOpen);
    // var modalOpenStatus = document.getElementById(modalStatusOpen);

    console.log({ sNavElParent: sNavElParent });
    // const authTest = [1, 5];
    const authTest = authorityId;
    //count stepper number
    let authCount = 0;

    let optionMethodEL = [];
    formattedDataSelect.forEach(function(option) {

      optionElement = `<option value="${option.id}">${option.text}</option>`;
      optionMethodEL.push(optionElement);

      console.log(optionMethodEL);
    });



    //   console.log({
    //   modalstatus:
    //     modalOpenStatus.previousElementSibling.previousElementSibling
    //       .previousElementSibling.value,
    // });
    // if (!isContentAppended) {
    authTest.forEach((element) => {
      let stepperNavEl = `<div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
                              <!--begin::Wrapper-->
                              <div class="stepper-wrapper d-flex align-items-start">
                                  <!--begin::Icon-->
                                  <div class="stepper-icon w-40px h-40px">
                                      <i class="stepper-check fas fa-check"></i>
                                      <span class="stepper-number">${
                                        authCount + 2
                                      }</span>
                                  </div>
                                  <!--end::Icon-->

                                  <!--begin::Label-->
                                  <div class="stepper-label w-175px">
                                      <h3 class="stepper-title">
                                          Maklumat ${element.group}
                                      </h3>

                                      <div class="stepper-desc">
                                         ${element.name}
                                      </div>
                                  </div>
                                  <!--end::Label-->
                              </div>
                              <!--end::Wrapper-->

                              <!--begin::Line-->
                              <div class="stepper-line h-40px"></div>
                              <!--end::Line-->
                          </div>`;

      sNavElParent.innerHTML += stepperNavEl;

      let stepperContentEl = `<div class="flex-column " data-kt-stepper-element="content">

                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                <span class="col-4">Pihak Berkuasa Terlibat</span>
                                <span class="col-1">:</span>
                                <div class="col-7">
                                    <span id="road-name" name="road-name" type="text"
                                        class="fw-bold fs-6 text-gray-800">${element.name}</span>
                                </div>
                            </div>

                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                <span class="col-4">Nama Jalan/Lokasi Terlibat</span>
                                <span class="col-1">:</span>
                                <div class="col-7">
                                    <span id="road-name" name="road-name" type="text"
                                        class="fw-bold fs-6 text-gray-800" style="text-transform: capitalize;">${element.road}</span>
                                </div>
                            </div>

                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                              <span class="col-4">Pembersihan Tapak</span>
                              <span class="col-1">:</span>
                              <div class="col-5">
                                  <input id="clean-site-${element.id}" name="clean-site-${element.id}" type="text"
                                      class="form-control" placeholder="Sila Isi Pembersihan Tapak"/>
                              </div>
                              <span class="col-2 ms-5 fw-semibold">Pukal</span>
                          </div>

                          <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                              <span class="col-4">Pelan Kawalan Trafik (TCP)</span>
                              <span class="col-1">:</span>
                              <div class="col-5">
                                  <input id="plan-tcp-${element.id}" name="plan-tcp-${element.id}" type="text"
                                      class="form-control" placeholder="Pelan Kawalan Trafik (TCP)"/>
                              </div>
                              <span class="col-2 ms-5 fw-semibold">Hari</span>
                          </div>


                            <!--<div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                <span class="col-4">Pegawai Bertanggungjawab</span>
                                <span class="col-1">:</span>
                                <div class="col-7">
                                    <span id="road-name" name="road-name" type="text"
                                        class="fw-bold fs-6 text-gray-800"></span>
                                </div>
                            </div>-->

                            <!--begin::Repeater-->
                            <div id="method-involved-${element.id}" class="mt-5">
                                <!--begin::Form group-->
                                <div class="form-group">
                                    <div data-repeater-list="rp-list-${element.id}">
                                        <div data-repeater-item>
                                            <div class="form-group row">

                                                <div class="col-md-7 mb-5 fv-row">
                                                    <label class="form-label required">Kaedah
                                                        Pemasangan</label>
                                                    <select name="road-method-${element.id}" data-rp-repeater-${element.id}="select2"
                                                        data-rp-parent class="form-select method-select pe-5"
                                                        data-placeholder="Sila Pilih Kaedah Kerja">
                                                        <option></option>
                                                        ${optionMethodEL}
                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-5 fv-row d-none" data-rp-length-${element.id}>
                                                    <label class="form-label required">Jarak</label>
                                                    <input type="text" name="road-length-${element.id}"
                                                        class="form-control repeater-value-${element.id}"
                                                        placeholder="Sila Isi Jarak" />
                                                </div>

                                                <div class="col-md-4 mb-5 fv-row d-none" data-rp-unit-${element.id}>
                                                    <label class="form-label required">Kuantiti</label>
                                                    <input type="text" name="road-unit-${element.id}"
                                                        class="form-control"
                                                        placeholder="Sila Isi Kuantiti" />
                                                </div>


                                                <div class="col-md-1">
                                                    <button type="button" data-repeater-delete
                                                        class="btn btn-light-danger mt-8 min-w-100">
                                                        <i class="fad fa-trash fs-4"></i>
                                                    </button>
                                                </div>

                                                <div class="row d-none" id="nested_hdd" data-rp-nested-hdd-${element.id}>
                                                    <div class="inner-repeater-hdd-${element.id} d-flex">
                                                        <div class="col-md-1 mt-8 mb-9">
                                                            <button
                                                                class="btn btn-sm btn-flex btn-light-primary p-4"
                                                                data-repeater-create type="button">
                                                                <i class="fad fa-plus fs-5"></i>
                                                            </button>
                                                        </div>
                                                        <div data-repeater-list="inner-repeater-hdd-${element.id}"
                                                            class="col-md-11 mb-5">
                                                            <div data-repeater-item>
                                                                <div class="form-group row">
                                                                    <div class="col-md-7 mb-5 fv-row">
                                                                        <label
                                                                            class="form-label required">Senarai</label>
                                                                        <select name="road-method-hdd-${element.id}" id="road-method-hdd-${element.id}"
                                                                            data-rp-hdd-repeater-${element.id}="select2"
                                                                            class="form-select method-select"
                                                                            data-placeholder="Sila Pilih Kaedah Kerja">
                                                                            <option></option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-4 mb-5 fv-row d-none" data-rp-length-hdd-${element.id}>
                                                                        <label
                                                                            class="form-label required">Jarak</label>
                                                                        <input type="text"
                                                                            name="road-length-hdd-${element.id}"
                                                                            class="form-control repeater-value-${element.id}"
                                                                            placeholder="Sila Isi Jarak" />
                                                                    </div>

                                                                    <div class="col-md-4 mb-5 fv-row d-none" data-rp-unit-hdd-${element.id}>
                                                                        <label
                                                                            class="form-label required">Kuantiti</label>
                                                                        <input type="text"
                                                                            name="road-unit-hdd-${element.id}"
                                                                            class="form-control"
                                                                            placeholder="Sila Isi Kuantiti" />
                                                                    </div>

                                                                    <div class="col-md-1">
                                                                        <button type="button" data-repeater-delete
                                                                            class="btn btn-light-danger mt-8 min-w-100">
                                                                            <i class="fad fa-trash fs-4"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row d-none" id="nested_gv" data-rp-nested-gv-${element.id}>
                                                    <div class="inner-repeater-gv-${element.id} d-flex">
                                                        <div class="col-md-1 mt-8 mb-9">
                                                            <button
                                                                class="btn btn-sm btn-flex btn-light-primary p-4"
                                                                data-repeater-create type="button">
                                                                <i class="fad fa-plus fs-5"></i>
                                                            </button>
                                                        </div>
                                                        <div data-repeater-list="inner-repeater-gv-${element.id}"
                                                            class="col-md-11 mb-5">
                                                            <div data-repeater-item>
                                                                <div class="form-group row">

                                                                    <div class="col-md-7 mb-5 fv-row">
                                                                        <label
                                                                            class="form-label required">Senarai</label>
                                                                        <select name="road-method-gv-${element.id}"
                                                                            data-rp-gv-repeater-${element.id}="select2"
                                                                            class="form-select method-select"
                                                                            data-placeholder="Sila Pilih Kaedah Kerja">
                                                                            <option></option>

                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-4 mb-5 fv-row">
                                                                        <label
                                                                            class="form-label required">Jarak</label>
                                                                        <input type="text" name="road-length-gv-${element.id}"
                                                                            class="form-control repeater-value-${element.id}"
                                                                            placeholder="Sila Isi Jarak" />
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button" data-repeater-delete
                                                                            class="btn btn-light-danger mt-8 min-w-100">
                                                                            <i class="fad fa-trash fs-4"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="row d-none" id="nested_cw" data-rp-nested-cw-${element.id}>
                                                    <div class="inner-repeater-cw-${element.id} d-flex">
                                                        <div class="col-md-1 mt-8 mb-9">
                                                            <button
                                                                class="btn btn-sm btn-flex btn-light-primary p-4"
                                                                data-repeater-create type="button">
                                                                <i class="fad fa-plus fs-5"></i>
                                                            </button>
                                                        </div>
                                                        <div data-repeater-list="inner-repeater-cw-${element.id}"
                                                            class="col-md-11 mb-5">
                                                            <div data-repeater-item>
                                                                <div class="form-group row">

                                                                    <div class="col-md-7 mb-5 fv-row">
                                                                        <label
                                                                            class="form-label required">Senarai</label>
                                                                        <select name="road-method-cw-${element.id}"
                                                                            data-rp-cw-repeater-${element.id}="select2"
                                                                            class="form-select method-select"
                                                                            data-placeholder="Sila Pilih Kaedah Kerja">
                                                                            <option></option>

                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-4 mb-5 fv-row d-none" data-rp-length-cw-${element.id}>
                                                                        <label
                                                                            class="form-label required">Jarak</label>
                                                                        <input type="text" name="road-length-cw-${element.id}"
                                                                            class="form-control repeater-value-${element.id}"
                                                                            placeholder="Sila Isi Jarak" />
                                                                    </div>
                                                                    <div class="col-md-4 mb-5 fv-row d-none" data-rp-unit-cw-${element.id}>
                                                                        <label
                                                                            class="form-label required">Kuantiti</label>
                                                                        <input type="text" name="road-unit-cw-${element.id}"
                                                                            class="form-control"
                                                                            placeholder="Sila Isi Kuantiti" />
                                                                    </div>

                                                                    <div class="col-md-1">
                                                                        <button type="button" data-repeater-delete
                                                                            class="btn btn-light-danger mt-8 min-w-100">
                                                                            <i class="fad fa-trash fs-4"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="row d-none" id="nested_oh" data-rp-nested-oh-${element.id}>
                                                    <div class="inner-repeater-oh-${element.id} d-flex">
                                                        <div class="col-md-1 mt-8 mb-9">
                                                            <button
                                                                class="btn btn-sm btn-flex btn-light-primary p-4"
                                                                data-repeater-create type="button">
                                                                <i class="fad fa-plus fs-5"></i>
                                                            </button>
                                                        </div>
                                                        <div data-repeater-list="inner-repeater-oh-${element.id}"
                                                            class="col-md-11 mb-5">
                                                            <div data-repeater-item>
                                                                <div class="form-group row">

                                                                    <div class="col-md-7 mb-5 fv-row">
                                                                        <label
                                                                            class="form-label required">Senarai</label>
                                                                        <select name="road-method-oh-${element.id}"
                                                                            data-rp-oh-repeater-${element.id}="select2"
                                                                            class="form-select method-select"
                                                                            data-placeholder="Sila Pilih Kaedah Kerja">
                                                                            <option></option>

                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-4 mb-5 fv-row d-none" data-rp-length-oh-${element.id}>
                                                                        <label
                                                                            class="form-label required">Jarak</label>
                                                                        <input type="text" name="road-length-oh-${element.id}"
                                                                            class="form-control repeater-value-${element.id}"
                                                                            placeholder="Sila Isi Jarak" />
                                                                    </div>
                                                                    <div class="col-md-4 mb-5 fv-row d-none" data-rp-unit-oh-${element.id}>
                                                                        <label
                                                                            class="form-label required">Kuantiti</label>
                                                                        <input type="text" name="road-unit-oh-${element.id}"
                                                                            class="form-control"
                                                                            placeholder="Sila Isi Kuantiti" />
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button" data-repeater-delete
                                                                            class="btn btn-light-danger mt-8 min-w-100">
                                                                            <i class="fad fa-trash fs-4"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <!--begin::Form group-->
                                        <div class="form-group">
                                            <button type="button" id="btn-create" data-repeater-create
                                                class="btn btn-light-primary">
                                                <i class="fad fa-plus"></i>Tambah Kaedah
                                            </button>
                                        </div>
                                        <!--end::Form group-->
                                        <div class="d-flex flex-end row">
                                            <div class="col-12 fv-row d-flex flex-row">
                                                <label class="w-100px d-flex align-items-center">Jumlah
                                                    Jarak</label>
                                                <input type="text" name="application-length-${element.id}"
                                                    id="application-length-${element.id}"
                                                    class="form-control form-control-solid w-150px"
                                                    placeholder="Jarak Permohonan" value="" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Form group-->

                            </div>
                            <!--end::Repeater-->
                        </div>
                    `;

      sContentElParent.innerHTML += stepperContentEl;

      authCount++;
    });
    // isContentAppended = true;
    // console.log({ modalStatus: modalOpenStatus.value });
    // }

    // Initialize Stepper
    var stepper = new KTStepper(element);

    // Handle next step
    stepper.on("kt.stepper.next", function (stepper) {
      stepper.goNext(); // go next step
    });

    // Handle previous step
    stepper.on("kt.stepper.previous", function (stepper) {
      stepper.goPrevious(); // go previous step
    });
  }

  // console.log(formattedData);

  function roadRepeaterInit(
    authorityId,
    formattedDataSelect,
    formattedHDD,
    formattedGV,
    formattedCW,
    formattedOH
  ) {
    // initialize repeater
    authorityId.forEach((element) => {
      let repeat = $(`#method-involved-${element.id}`).repeater({
        // defaultValues: {
        //   'road-method-4': 'OH',
        //   'inner-repeater-cw-4': [{
        //     'road-length-cw-4': '',
        //     'road-method-cw-4': '',
        //     'road-unit-cw-4': ''
        //   }],
        //   'inner-repeater-gv-4': [{
        //     'road-length-gv-4': '',
        //     'road-method-gv-4': ''
        //   }],
        //   'inner-repeater-hdd-4': [{
        //     'road-length-hdd-4': '',
        //     'road-method-hdd-4': '',
        //     'road-unit-hdd-4': ''
        //   }],
        //   'inner-repeater-oh-4': [{
        //     'road-length-oh-4': '',
        //     'road-method-oh-4': 'Merentangi Jalan',
        //     'road-unit-oh-4': ''
        //   }],
        //   'road-length-4': '',
        //   'road-unit-4': ''
        // },

        repeaters: [
          {
            selector: `.inner-repeater-hdd-${element.id}`,

            show: function () {
              $(this).slideDown();

              $(this)
                .find(`[data-rp-hdd-repeater-${element.id}="select2"]`)
                .select2({ data: formattedHDD });

              var LengthHddSection = $(this).find(
                `[data-rp-length-hdd-${element.id}]`
              );

              var UnitHddSection = $(this).find(
                `[data-rp-unit-hdd-${element.id}]`
              );

              LengthHddSection.removeClass("d-none");
              UnitHddSection.removeClass("d-none");
              // Hide the nested sections by default
              LengthHddSection.hide();
              UnitHddSection.hide();

              $(this)
                .find(`[data-rp-hdd-repeater-${element.id}="select2"]`)
                .on("change", function () {
                  var selectedValue = $(this).val();

                  LengthHddSection.find("input").val("");
                  UnitHddSection.find("input").val("");

                  LengthHddSection.find("select").val("");
                  UnitHddSection.find("select").val("");

                  // Show/hide nested sections based on selected value
                  if (
                    selectedValue === "Merentangi Jalan" ||
                    selectedValue === "Sepanjang Bahu Jalan"
                  ) {
                    LengthHddSection.slideDown();
                    UnitHddSection.slideUp();
                  } else if (selectedValue === "Pit") {
                    LengthHddSection.slideUp();
                    UnitHddSection.slideDown();
                  }
                });
            },

            hide: function (deleteElement) {
              $(this).slideUp(deleteElement);
            },

            ready: function () {
              // Init select2
              $(`[data-rp-hdd-repeater-${element.id}="select2"]`).select2({
                data: formattedHDD,
              });
            },
          },
          {
            selector: `.inner-repeater-gv-${element.id}`,

            show: function () {
              $(this).slideDown();

              $(this)
                .find(`[data-rp-gv-repeater-${element.id}="select2"]`)
                .select2({ data: formattedGV });
            },

            hide: function (deleteElement) {
              $(this).slideUp(deleteElement);
            },

            ready: function () {
              // Init select2
              $(`[data-rp-gv-repeater-${element.id}="select2"]`).select2({
                data: formattedGV,
              });
            },
          },
          {
            selector: `.inner-repeater-cw-${element.id}`,

            show: function () {
              $(this).slideDown();

              $(this)
                .find(`[data-rp-cw-repeater-${element.id}="select2"]`)
                .select2({ data: formattedCW });

              var LengthCWSection = $(this).find(
                `[data-rp-length-cw-${element.id}]`
              );
              var UnitCWSection = $(this).find(
                `[data-rp-unit-cw-${element.id}]`
              );

              LengthCWSection.removeClass("d-none");
              UnitCWSection.removeClass("d-none");
              // Hide the nested sections by default
              LengthCWSection.hide();
              UnitCWSection.hide();

              $(this)
                .find(`[data-rp-cw-repeater-${element.id}="select2"]`)
                .on("change", function () {
                  var selectedValue = $(this).val();

                  LengthCWSection.find("input").val("");
                  UnitCWSection.find("input").val("");

                  LengthCWSection.find("select").val("");
                  UnitCWSection.find("select").val("");

                  // Show/hide nested sections based on selected value
                  if (
                    selectedValue === "Diameter Utiliti < 500mm" ||
                    selectedValue === "Diameter Utiliti > 500mm"
                  ) {
                    LengthCWSection.slideDown();
                    UnitCWSection.slideUp();
                  } else if (selectedValue === "Pit") {
                    LengthCWSection.slideUp();
                    UnitCWSection.slideDown();
                  }
                });
            },

            hide: function (deleteElement) {
              $(this).slideUp(deleteElement);
            },

            ready: function () {
              // Init select2
              $(`[data-rp-cw-repeater-${element.id}="select2"]`).select2({
                data: formattedCW,
              });
            },
          },
          {
            selector: `.inner-repeater-oh-${element.id}`,

            show: function () {
              $(this).slideDown();

              $(this)
                .find(`[data-rp-oh-repeater-${element.id}="select2"]`)
                .select2({ data: formattedOH });

              var LengthOHSection = $(this).find(
                `[data-rp-length-oh-${element.id}]`
              );
              var UnitOHSection = $(this).find(
                `[data-rp-unit-oh-${element.id}]`
              );

              LengthOHSection.removeClass("d-none");
              UnitOHSection.removeClass("d-none");
              // Hide the nested sections by default
              LengthOHSection.hide();
              UnitOHSection.hide();

              $(this)
                .find(`[data-rp-oh-repeater-${element.id}="select2"]`)
                .on("change", function () {
                  var selectedValue = $(this).val();

                  LengthOHSection.find("input").val("");
                  UnitOHSection.find("input").val("");

                  LengthOHSection.find("select").val("");
                  UnitOHSection.find("select").val("");

                  // Show/hide nested sections based on selected value
                  if (
                    selectedValue === "Merentangi Jalan" ||
                    selectedValue === "Sepanjang Bahu Jalan"
                  ) {
                    LengthOHSection.slideDown();
                    UnitOHSection.slideUp();
                  } else if (selectedValue === "Pit") {
                    LengthOHSection.slideUp();
                    UnitOHSection.slideDown();
                  }
                });
            },

            hide: function (deleteElement) {
              $(this).slideUp(deleteElement);
            },

            ready: function () {
              // Init select2
              $(`[data-rp-oh-repeater-${element.id}="select2"]`).select2({
                data: formattedOH,
              });
            },
          },
        ],

        show: function () {
          // alert('new repeater item added');
          $(this).slideDown();

          // Init select2
          $(this).find(`[data-rp-repeater-${element.id}="select2"]`).select2();
          // $(this)
          //   .find(`[data-rp-repeater-${element.id}="select2"]`)
          //   .select2({ data: formattedDataSelect });

          var nestedHddSection = $(this).find(
            `[data-rp-nested-hdd-${element.id}]`
          );
          var nestedGvSection = $(this).find(
            `[data-rp-nested-gv-${element.id}]`
          );
          var nestedCwSection = $(this).find(
            `[data-rp-nested-cw-${element.id}]`
          );
          var nestedOhSection = $(this).find(
            `[data-rp-nested-oh-${element.id}]`
          );
          var lengthSection = $(this).find(`[data-rp-length-${element.id}]`);
          var unitSection = $(this).find(`[data-rp-unit-${element.id}]`);

          nestedHddSection.removeClass("d-none");
          nestedGvSection.removeClass("d-none");
          lengthSection.removeClass("d-none");
          unitSection.removeClass("d-none");
          nestedCwSection.removeClass("d-none");
          nestedOhSection.removeClass("d-none");
          // Hide the nested sections by default
          nestedHddSection.hide();
          nestedGvSection.hide();
          lengthSection.hide();
          unitSection.hide();
          nestedCwSection.hide();
          nestedOhSection.hide();

          // Add event listener to parent select element
          $(this)
            .find(`[data-rp-repeater-${element.id}="select2"]`)
            .on("change", function () {
              var selectedValue = $(this).val();
              console.log({ selectedValue: selectedValue });

              nestedHddSection.find("input").val("");
              nestedGvSection.find("input").val("");
              nestedCwSection.find("input").val("");
              nestedOhSection.find("input").val("");
              lengthSection.find("input").val("");
              unitSection.find("input").val("");
              nestedHddSection.find("select").val("");
              nestedGvSection.find("select").val("");
              nestedCwSection.find("select").val("");
              nestedOhSection.find("select").val("");
              lengthSection.find("select").val("");
              unitSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "HDD" ||
                selectedValue === "MT" ||
                selectedValue === "PJ" ||
                selectedValue === "TB"
              ) {
                nestedHddSection.slideDown();
                nestedGvSection.slideUp();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              } else if (selectedValue === "GV") {
                nestedHddSection.slideUp();
                nestedGvSection.slideDown();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              } else if (selectedValue === "CW") {
                nestedCwSection.slideDown();
                nestedOhSection.slideUp();
                nestedGvSection.slideUp();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedHddSection.slideUp();
              } else if (selectedValue === "OH") {
                nestedCwSection.slideUp();
                nestedOhSection.slideDown();
                nestedGvSection.slideUp();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedHddSection.slideUp();
              } else if (
                selectedValue === "CP" ||
                selectedValue === "ID" ||
                selectedValue === "ED" ||
                selectedValue === "GI"
              ) {
                lengthSection.slideDown();
                unitSection.slideUp();
                nestedHddSection.slideUp();
                nestedGvSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              } else if (
                selectedValue === "Bilangan Manhole sedia ada" ||
                selectedValue === "MS" ||
                selectedValue === "Bilangan tiang yang dicadangkan" ||
                selectedValue === "Bilangan Excavation Pit" ||
                selectedValue === "Bilangan Manhole Baru yang dicadangkan" ||
                selectedValue === "Pelan Kawalan Trafik (TCP)" ||
                selectedValue === "Permbersihan Tapak"
              ) {
                lengthSection.slideUp();
                unitSection.slideDown();
                nestedHddSection.slideUp();
                nestedGvSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              }
            });

          let thisSelectEl = $(this).find(
            `[data-rp-repeater-${element.id}="select2"]`
          );
          if (thisSelectEl.val() != "") {
            thisSelectEl.trigger("change");
          }

          var LengthHddSection = $(this).find(
            `[data-rp-length-hdd-${element.id}]`
          );

          var UnitHddSection = $(this).find(`[data-rp-unit-hdd-${element.id}]`);

          LengthHddSection.removeClass("d-none");
          UnitHddSection.removeClass("d-none");
          // Hide the nested sections by default
          LengthHddSection.hide();
          UnitHddSection.hide();

          $(this)
            .find(`[data-rp-hdd-repeater-${element.id}="select2"]`)
            .on("change", function () {
              var selectedValue = $(this).val();

              LengthHddSection.find("input").val("");
              UnitHddSection.find("input").val("");

              LengthHddSection.find("select").val("");
              UnitHddSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "Merentangi Jalan" ||
                selectedValue === "Sepanjang Bahu Jalan"
              ) {
                LengthHddSection.slideDown();
                UnitHddSection.slideUp();
              } else if (selectedValue === "Pit") {
                LengthHddSection.slideUp();
                UnitHddSection.slideDown();
              }
            });

          var LengthCWSection = $(this).find(
            `[data-rp-length-cw-${element.id}]`
          );
          var UnitCWSection = $(this).find(`[data-rp-unit-cw-${element.id}]`);

          LengthCWSection.removeClass("d-none");
          UnitCWSection.removeClass("d-none");
          // Hide the nested sections by default
          LengthCWSection.hide();
          UnitCWSection.hide();

          $(this)
            .find(`[data-rp-cw-repeater-${element.id}="select2"]`)
            .on("change", function () {
              var selectedValue = $(this).val();

              LengthCWSection.find("input").val("");
              UnitCWSection.find("input").val("");

              LengthCWSection.find("select").val("");
              UnitCWSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "Diameter Utiliti < 500mm" ||
                selectedValue === "Diameter Utiliti > 500mm"
              ) {
                LengthCWSection.slideDown();
                UnitCWSection.slideUp();
              } else if (selectedValue === "Pit") {
                LengthCWSection.slideUp();
                UnitCWSection.slideDown();
              }
            });

          var LengthOHSection = $(this).find(
            `[data-rp-length-oh-${element.id}]`
          );
          var UnitOHSection = $(this).find(`[data-rp-unit-oh-${element.id}]`);

          LengthOHSection.removeClass("d-none");
          UnitOHSection.removeClass("d-none");
          // Hide the nested sections by default
          LengthOHSection.hide();
          UnitOHSection.hide();

          $(this)
            .find(`[data-rp-oh-repeater-${element.id}="select2"]`)
            .on("change", function () {
              var selectedValue = $(this).val();

              LengthOHSection.find("input").val("");
              UnitOHSection.find("input").val("");

              LengthOHSection.find("select").val("");
              UnitOHSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "Merentangi Jalan" ||
                selectedValue === "Sepanjang Bahu Jalan"
              ) {
                LengthOHSection.slideDown();
                UnitOHSection.slideUp();
              } else if (selectedValue === "Pit") {
                LengthOHSection.slideUp();
                UnitOHSection.slideDown();
              }
            });
        },

        hide: function (deleteElement) {
          // Show confirmation popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
          // Swal.fire({
          //   text: "Padam Kaedah Pemasangan ini?",
          //   icon: "warning",
          //   showCancelButton: true,
          //   buttonsStyling: false,
          //   confirmButtonText: "Ya, Padam!",
          //   cancelButtonText: "Tidak, Kembali!",
          //   customClass: {
          //     confirmButton: "btn btn-primary",
          //     cancelButton: "btn btn-active-light",
          //   },
          // }).then((result) => {
          //   if (result.isConfirmed) {
          $(this).slideUp(deleteElement);
          // }
          // });
        },

        ready: function () {
          // Init select2
          $(`[data-rp-repeater-${element.id}="select2"]`).select2();
          // $(`[data-rp-repeater-${element.id}="select2"]`).select2({
          //   data: formattedDataSelect,
          // });

          // $('[data-rp-nested-hdd]').hide();
          // $('[data-rp-nested-gv]').hide();

          var lengthSection = $(`[data-rp-length-${element.id}]`);
          var unitSection = $(`[data-rp-unit-${element.id}]`);
          var nestedHddSection = $(`[data-rp-nested-hdd-${element.id}]`);
          var nestedGvSection = $(`[data-rp-nested-gv-${element.id}]`);
          var nestedCwSection = $(`[data-rp-nested-cw-${element.id}]`);
          var nestedOhSection = $(`[data-rp-nested-oh-${element.id}]`);

          // Hide the nested sections by default
          nestedHddSection.removeClass("d-none");
          nestedGvSection.removeClass("d-none");
          lengthSection.removeClass("d-none");
          unitSection.removeClass("d-none");
          nestedCwSection.removeClass("d-none");
          nestedOhSection.removeClass("d-none");
          nestedHddSection.hide();
          nestedGvSection.hide();
          lengthSection.hide();
          unitSection.hide();
          nestedCwSection.hide();
          nestedOhSection.hide();

          $(`[data-rp-repeater-${element.id}="select2"]`).on(
            "change",
            function () {
              var selectedValue = $(this).val();

              nestedHddSection.find("input").val("");
              nestedGvSection.find("input").val("");
              nestedCwSection.find("input").val("");
              nestedOhSection.find("input").val("");
              lengthSection.find("input").val("");
              unitSection.find("input").val("");
              nestedHddSection.find("select").val("");
              nestedGvSection.find("select").val("");
              nestedCwSection.find("select").val("");
              nestedOhSection.find("select").val("");
              lengthSection.find("select").val("");
              unitSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "HDD" ||
                selectedValue === "MT" ||
                selectedValue === "PJ" ||
                selectedValue === "TB"
              ) {
                nestedHddSection.slideDown();
                nestedGvSection.slideUp();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              } else if (selectedValue === "GV") {
                nestedHddSection.slideUp();
                nestedGvSection.slideDown();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              } else if (selectedValue === "CW") {
                nestedCwSection.slideDown();
                nestedOhSection.slideUp();
                nestedGvSection.slideUp();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedHddSection.slideUp();
              } else if (selectedValue === "OH") {
                nestedCwSection.slideUp();
                nestedOhSection.slideDown();
                nestedGvSection.slideUp();
                lengthSection.slideUp();
                unitSection.slideUp();
                nestedHddSection.slideUp();
              } else if (
                selectedValue === "CP" ||
                selectedValue === "ID" ||
                selectedValue === "ED" ||
                selectedValue === "GI"
              ) {
                lengthSection.slideDown();
                unitSection.slideUp();
                nestedHddSection.slideUp();
                nestedGvSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              } else if (
                selectedValue === "Bilangan Manhole sedia ada" ||
                selectedValue === "MS" ||
                selectedValue === "Bilangan tiang yang dicadangkan" ||
                selectedValue === "Bilangan Excavation Pit" ||
                selectedValue === "Bilangan Manhole Baru yang dicadangkan" ||
                selectedValue === "Pelan Kawalan Trafik (TCP)" ||
                selectedValue === "Permbersihan Tapak"
              ) {
                lengthSection.slideUp();
                unitSection.slideDown();
                nestedHddSection.slideUp();
                nestedGvSection.slideUp();
                nestedCwSection.slideUp();
                nestedOhSection.slideUp();
              }
            }
          );

          var LengthHddSection = $(`[data-rp-length-hdd-${element.id}]`);
          var UnitHddSection = $(`[data-rp-unit-hdd-${element.id}]`);

          LengthHddSection.removeClass("d-none");
          UnitHddSection.removeClass("d-none");
          // Hide the nested sections by default
          LengthHddSection.hide();
          UnitHddSection.hide();

          $(`[data-rp-hdd-repeater-${element.id}="select2"]`).on(
            "change",
            function () {
              var selectedValue = $(this).val();

              LengthHddSection.find("input").val("");
              UnitHddSection.find("input").val("");

              LengthHddSection.find("select").val("");
              UnitHddSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "Merentangi Jalan" ||
                selectedValue === "Sepanjang Bahu Jalan"
              ) {
                LengthHddSection.slideDown();
                UnitHddSection.slideUp();
              } else if (selectedValue === "Pit") {
                LengthHddSection.slideUp();
                UnitHddSection.slideDown();
              }
            }
          );

          var LengthCWSection = $(`[data-rp-length-cw-${element.id}]`);
          var UnitCWSection = $(`[data-rp-unit-cw-${element.id}]`);

          LengthCWSection.removeClass("d-none");
          UnitCWSection.removeClass("d-none");
          // Hide the nested sections by default
          LengthCWSection.hide();
          UnitCWSection.hide();

          $(`[data-rp-cw-repeater-${element.id}="select2"]`).on(
            "change",
            function () {
              var selectedValue = $(this).val();

              LengthCWSection.find("input").val("");
              UnitCWSection.find("input").val("");

              LengthCWSection.find("select").val("");
              UnitCWSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "Diameter Utiliti < 500mm" ||
                selectedValue === "Diameter Utiliti > 500mm"
              ) {
                LengthCWSection.slideDown();
                UnitCWSection.slideUp();
              } else if (selectedValue === "Pit") {
                LengthCWSection.slideUp();
                UnitCWSection.slideDown();
              }
            }
          );

          var LengthOHSection = $(`[data-rp-length-oh-${element.id}]`);
          var UnitOHSection = $(`[data-rp-unit-oh-${element.id}]`);

          LengthOHSection.removeClass("d-none");
          UnitOHSection.removeClass("d-none");
          // Hide the nested sections by default
          LengthOHSection.hide();
          UnitOHSection.hide();

          $(`[data-rp-oh-repeater-${element.id}="select2"]`).on(
            "change",
            function () {
              var selectedValue = $(this).val();

              LengthOHSection.find("input").val("");
              UnitOHSection.find("input").val("");

              LengthOHSection.find("select").val("");
              UnitOHSection.find("select").val("");

              // Show/hide nested sections based on selected value
              if (
                selectedValue === "Merentangi Jalan" ||
                selectedValue === "Sepanjang Bahu Jalan"
              ) {
                LengthOHSection.slideDown();
                UnitOHSection.slideUp();
              } else if (selectedValue === "Pit") {
                LengthOHSection.slideUp();
                UnitOHSection.slideDown();
              }
            }
          );
        },

        isFirstItemUndeletable: false,
      });

      console.log({ repeaterVal: repeat.repeaterVal() });
      console.log({ repeaterEl: repeat });

      // var repeat = $(`#method-involved-${element.id}`).repeater();

      // var datata = [];

      // datata.push(

      var datata = [{ "road-method-4": `HDD` }];
      // var datata = [{ "rp-list-4": [{ "road-method-4": `HDD` }] }];
      console.log({ datata: datata });
      // var datata = [{
      // //   // "application-length-4": "",
      // //   // "rp-list-4": {
      //     'road-method-4': "HDD",
      //   'inner-repeater-cw-4': [{
      //     'road-length-cw-4': '',
      //     'road-method-cw-4': '',
      //     'road-unit-cw-4': ''
      //   }],
      //   'inner-repeater-gv-4': [{
      //     'road-length-gv-4': '',
      //     'road-method-gv-4': ''
      //   }],
      //   'inner-repeater-hdd-4': [{
      //     'road-length-hdd-4': '',
      //     'road-method-hdd-4': 'Merentangi Jalan',
      //     'road-unit-hdd-4': ''
      //   }],
      //   'inner-repeater-oh-4': [{
      //     'road-length-oh-4': '',
      //     'road-method-oh-4': '',
      //     'road-unit-oh-4': ''
      //   }],
      //   'road-length-4': '',
      //   'road-unit-4': ''
      //   // }

      // },
      // {
      //   // "application-length-4": "",
      //   // "rp-list-4": {
      //     'road-method-4': 'OH',
      //   'inner-repeater-cw-4': {
      //     'road-length-cw-4': '',
      //     'road-method-cw-4': '',
      //     'road-unit-cw-4': ''
      //   },
      //   'inner-repeater-gv-4': {
      //     'road-length-gv-4': '',
      //     'road-method-gv-4': ''
      //   },
      //   'inner-repeater-hdd-4': {
      //     'road-length-hdd-4': '',
      //     'road-method-hdd-4': '',
      //     'road-unit-hdd-4': ''
      //   },
      //   'inner-repeater-oh-4': {
      //     'road-length-oh-4': '',
      //     'road-method-oh-4': 'Merentangi Jalan',
      //     'road-unit-oh-4': ''
      //   },
      //   'road-length-4': '',
      //   'road-unit-4': ''
      //   // }

      // }];
      // // );

      console.log(datata);

      repeat.setList(datata);

      // $(`#method-involved-${element.id} select`).each(function () {
      //   var selectedValue = $(this).val(); // Get the selected value from your data
      //   $(this).val(selectedValue); // Set the dropdown selection
      // });

      //     var rpData = [];
      //     var rawRPData;

      // api.get(`wayleave/deposit/index/${systemID}?item=rpitem&aid=${element.id}`)
      //     .then((response) => {
      //     console.log({ guests: response.work_method});
      //     rawRPData = { method: response };

      //     console.log(rawRPData);

      // // Loop through the JSON data and add each item to the repeater
      // $.each(rawRoadData.roads, function (index, road) {

      //     // Push roads info as a separate object into the array
      // $.each(rawRPData.method, function (index, method) {
      //     console.log(method['authority_id']);
      //     // Push roads info as a separate object into the array
      //     rpData.push({
      //         ["rp-list-" + '1']: {
      //             ["road-method-" + '1']: "HDD",
      //             ["road-length-" + '1']: 100,
      //             ["road-unit-"+ '1'] : "",
      //             ["inner-repeater-hdd-" + '1']: {
      //                 ["road-method-hdd-" + '1']: "Merentangi Jalan",
      //                 ["road-length-hdd-" + '1']: 1000,
      //                 ["road-unit-hdd-"+'1']: ""
      //             },
      //             ["inner-repeater-gv-" + '1']: {
      //                   ["road-method-gv-"+ '1']: "",
      //                   ["road-length-gv-"+ '1']: ""
      //             },
      //             ["inner-repeater-cw-" + '1']: {
      //                   ["road-method-cw-" + '1']: "",
      //                   ["road-length-cw-"+ '1']: "",
      //                   ["road-unit-cw-"+ '1']: ""
      //             },
      //             ["inner-repeater-oh-" + '1']: {
      //                   ["road-method-oh-"+ '1']: "",
      //                   ["road-length-oh-"+ '1']: "",
      //                   ["road-unit-oh-"+ '1']: ""

      //               }

      //         },

      //     });
      // });

      // const rpData = rawRPData.method.map(method => ({
      //     ["rp-list-" + method.authority_id]: {
      //         ["road-method-" + method.authority_id]: "HDD",
      //         ["road-length-" + method.authority_id]: 100,
      //         ["road-unit-" + method.authority_id]: "",
      //         ["inner-repeater-hdd-" + method.authority_id]: [{
      //             ["road-method-hdd-" + method.authority_id]: "Merentangi Jalan",
      //             ["road-length-hdd-" + method.authority_id]: 1000,
      //             ["road-unit-hdd-" + method.authority_id]: ""
      //         }],
      //         ["inner-repeater-gv-" + method.authority_id]: [{
      //             ["road-method-gv-" + method.authority_id]: "",
      //             ["road-length-gv-" + method.authority_id]: ""
      //         }],
      //         ["inner-repeater-cw-" + method.authority_id]: [{
      //             ["road-method-cw-" + method.authority_id]: "",
      //             ["road-length-cw-" + method.authority_id]: "",
      //             ["road-unit-cw-" + method.authority_id]: ""
      //         }],
      //         ["inner-repeater-oh-" + method.authority_id]: [{
      //             ["road-method-oh-" + method.authority_id]: "",
      //             ["road-length-oh-" + method.authority_id]: "",
      //             ["road-unit-oh-" + method.authority_id]: ""
      //         }]
      //     }
      // }));
      // console.log(rpData);

      // $(`#method-involved-${element.id}`).repeater().setList(rpData);

      // console.log({ roadData: roadData });

      // // set the list to repeater items
      // roadRepeater.setList(roadData);

      // // decide to run totallengthfunction with calculation or not based on roadData value
      // if (roadData.length > 0) {
      //     totalLengthRepeaterInit().initCalculate();
      // } else {
      //     totalLengthRepeaterInit()
      // }
      // }).catch((error) => {
      // console.log(error);
      // });

      // var $repeater = $('.repeater').repeater();
      // $(`#method-involved-${element.id}`).repeater().setList([
      //     {
      //         'text-input': 'set-a',
      //         'inner-group': [{ 'inner-text-input': 'set-b' }]
      //     },
      //     { 'text-input': 'set-foo' }
      // ]);
    });

    // // manipulate repeater
    // var roadData = [];
    // var rawRoadData = {
    //   roads: [
    //     { name: "jalan-1", method: "HDD", length: 200 },
    //     { name: "jalan-2", method: ["HDD", "CW"], length: 200 },
    //   ],
    // };

    // // Loop through the JSON data and add each item to the repeater
    // $.each(rawRoadData.roads, function (index, road) {
    //   // Push roads info as a separate object into the array
    //   roadData.push({
    //     "road-name": road.name,
    //     "road-method": road.method,
    //     "start-coord":
    //       road.latitude_start + ", " + road.longitude_start,
    //     "end-coord": road.latitude_end + ", " + road.longitude_end,
    //     "road-length": road.length,
    //   });
    // });
    // roadRepeater.setList(roadData);
  }

  function totalLengthRepeaterInit(authoirtyId) {
    authoirtyId.forEach((element) => {
      let totalSum;
      let totalLength = document.getElementById(
        `application-length-${element.id}`
      );

      function calculateLength() {
        totalSum = 0;
        $(
          "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item]):not(#nested_oh [data-repeater-item])"
        ).each(function () {
          var $input = $(this).find(`.repeater-value-${element.id}`);
          if ($input.length > 0) {
            for (var i = 0; i < $input.length; i++) {
              var value = parseFloat($input[i].value);
              if (!isNaN(value)) {
                totalSum += value;
              }
            }
          }
        });
        return totalSum;
      }

      // Loop through all the input fields with class "repeater-value" and initialize the totalSum variable
      totalSum = 0;
      $(`.repeater-value-${element.id}`).each(function () {
        var value = parseFloat($(this).val());
        if (!isNaN(value)) {
          totalSum += value;
        }
      });
      $(totalLength).val(totalSum);

      // Add event listener to each input element
      $(
        "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item]):not(#nested_oh [data-repeater-item])"
      ).on("input", `.repeater-value-${element.id}`, function () {
        totalSum = calculateLength();
        $(totalLength).val(totalSum);
      });

      // Add event listener to #method-involved element to clear input
      document
        .getElementById(`method-involved-${element.id}`)
        .addEventListener("keyup", function (event) {
          if (
            event.target &&
            event.target.classList.contains(`repeater-value-${element.id}`)
          ) {
            if (event.target.value === "") {
              event.target.value = 0;
              event.target.dispatchEvent(new Event("input"));
            }
          }
        });

      // Add event listener to "repeater-add" button
      $(document).ready(function () {
        $("button[data-repeater-create]").on("click", function () {
          // Add event listener to each input element
          $(
            "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item]):not(#nested_oh [data-repeater-item])"
          ).on("input", `.repeater-value-${element.id}`, function () {
            totalSum = calculateLength();
            $(totalLength).val(totalSum);
          });
        });
      });

      $(document).ready(function () {
        // Add event listener to "repeater-delete" button
        $("div[data-repeater-list]").on(
          "click",
          "button[data-repeater-delete]",
          function () {
            $(this)
              .closest(
                "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item]):not(#nested_oh [data-repeater-item])"
              )
              .find(`.repeater-value-${element.id}`)
              .each(function () {
                $(this).val("");
                $(this).trigger("input");
                totalSum = calculateLength();
                $(totalLength).val(totalSum);
              });
          }
        );
      });
    });
  }

  let formId = "#form-deposit-return";
  let form = document.querySelector(formId);
  // let formData = JSON.stringify(form);

  const submitButton = document.querySelector("#submit-deposit-return");

  submitButton.addEventListener("click", function (e) {
    // Prevent button default action
    e.preventDefault();

    // Show loading indication
    submitButton.setAttribute("data-kt-indicator", "on");

    // Disable button to avoid multiple click
    submitButton.disabled = true;

    // Show confirmation message using SweetAlert
    Swal.fire({
      title: "Jana Wang Cagaran?",
      text: "Pastikan Anda Semak Sebelum Jana!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Jana!",
      cancelButtonText: "Batal",
      customClass: {
        confirmButton: "btn fw-bold btn-primary",
        cancelButton: "btn fw-bold btn-light btn-active-light-primary",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        submitButton.setAttribute("data-kt-indicator", "on");

        const formData = new FormData(form);
        // formData.append('subId', code);

        console.log(formData);

        const result = {};

        for (const [key, value] of formData.entries()) {
          const keys = key.split(/\]\[|\[|\]/).filter(Boolean);
          let obj = result;

          for (let i = 0; i < keys.length - 1; i++) {
            const currentKey = keys[i];
            const nextKey = keys[i + 1];
            const isArray = nextKey === "";

            if (!obj[currentKey]) {
              obj[currentKey] = isArray ? [] : {};
            }

            if (isArray && !obj[currentKey].length) {
              obj[currentKey].push({});
            }

            obj = isArray ? obj[currentKey][0] : obj[currentKey];
          }

          const lastKey = keys[keys.length - 1];
          if (Array.isArray(obj[lastKey])) {
            obj[lastKey].push(value);
          } else {
            obj[lastKey] = value;
          }
        }

        console.log(JSON.stringify(result, null, 2));

        api
          .post(`wayleave/deposit/index`, JSON.stringify(result))
          .then((response) => {
            // Hide loading indication
            submitButton.removeAttribute("data-kt-indicator");

            // Enable button
            submitButton.disabled = false;

            // toastr.success("Berjaya! 🎉");
            console.log(response.systemId);
            console.log(response.authId);

            toastr.success(response.message);
            setTimeout(function () {
              location.href =
                `/projects/wayleave/deposit/` +
                response.systemId +
                `?aid=` +
                flw_auth_id;
            }, 2500);
          })
          .catch((error) => {
            // Hide loading indication
            submitButton.removeAttribute("data-kt-indicator");

            // Enable button
            submitButton.disabled = false;

            // Show error popup
            Swal.fire({
              text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
              icon: "error",
              buttonsStyling: false,
              confirmButtonText: "Ok, faham",
              customClass: {
                confirmButton: "btn btn-primary",
              },
              allowOutsideClick: false,
            });
          });
      } else {
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");

        // Enable button
        submitButton.disabled = false;
      }
    });
  });

  return {
    init: function () {},
  };
})();

KTUtil.onDOMContentLoaded(function () {
  createDeposit.init();
});
