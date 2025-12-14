"use strict";
var handleEditButiran = (function (e) {
  let EditButiranButton;

  function CreateDateInput() {
    var DateSelector = document.querySelector(
      '[data-application="date"]'
    );
    flatpickr(DateSelector, {
      enableTime: false,
      dateFormat: "d M Y",
      defaultDate: DateSelector.textContent,
    });
  }

  function CreateSave(id) {
    const input = document.getElementById(`${"application_" + id}`);
    const span = document.createElement("span");

    const attributes = {
      "data-application": id,
    };

    Object.entries(attributes).forEach(([key, value]) => {
      span.setAttribute(key, value);
    });

    span.textContent = input.value;
    input.parentNode.replaceChild(span, input);
  }

  function CreateInput(selector) {
    const span = document.querySelector(selector);
    const input = document.createElement("input");

    const attributes = {
      "data-application": span.getAttribute("data-application"),
      type: "text",
      id: "application_" + span.getAttribute("data-application"),
      name: "application_" + span.getAttribute("data-application"),
      class: "form-control p-2",
    };

    Object.entries(attributes).forEach(([key, value]) => {
      input.setAttribute(key, value);
    });
    // input.classList.add("form-control");
    input.value = span.textContent;
    span.parentNode.replaceChild(input, span);
  }
  var HandleEditButiran = function (e) {

    const systemId = window.location.pathname.split("/").pop();
    const dataAttributes = [
      "title",
      "site_start",
      "project_costs",
      "link_id",
      "length",
      "site_end",
      "date",
    ];

    EditButiranButton.addEventListener("click", function (e) {
      const iconElement = this.firstElementChild;
      
      if (iconElement.classList.contains("fa-pen-to-square")) {
        iconElement.classList.remove("fa-pen-to-square");
        iconElement.classList.add("fa-floppy-disk"); // Save icon class
        this.classList.remove("btn-light");
        this.classList.add("btn-primary");
        CreateEdit(); 
      } else {
        iconElement.classList.remove("fa-floppy-disk");
        iconElement.classList.add("fa-pen-to-square");
        this.classList.remove("btn-primary");
        this.classList.add("btn-light");
        SaveApiRequest();
        SaveInput();
      }

      function getApplicationInputEdit() {
        const formData = new FormData();
        Object.entries(dataAttributes).forEach(([key, value]) => {
            const input = document.querySelector(
              '[data-application="' + value + '"]'
            );
            let inputValue = input.value;
            if (value == "date") {
              const [day, month, year] = inputValue.split(" ");

              // Convert month name to a number
              const monthMap = {
                Jan: "01",
                Feb: "02",
                Mar: "03",
                Apr: "04",
                May: "05",
                Jun: "06",
                Jul: "07",
                Aug: "08",
                Sep: "09",
                Oct: "10",
                Nov: "11",
                Dec: "12",
              };

              // Format the date in "Y-m-d"
              inputValue = `${year}-${monthMap[month]}-${day}`;  
            }
            formData.append(value, inputValue);
        });
        
        return Object.fromEntries(formData.entries());;
      }

      function SaveApiRequest() {
        // const data = {};
        
        const json = getApplicationInputEdit();
        api
          .post(`projects/wayleaves/roads/update/${systemId}`, json)
          .then((res) => {
            if (res.status === 200) {
              toastr.success(res.message);
            } else { 
              toastr.error(res.message);
            }
          });
      }

      function CreateEdit(){

        Object.entries(dataAttributes).forEach(([key, value]) => {
          CreateInput('[data-application="' + value + '"]');
          if (value == "date") {
            CreateDateInput();
          }
        });
      }

      function SaveInput(){
        Object.entries(dataAttributes).forEach(([key, value]) => {
          CreateSave(value);
        });
      }
      
    });
  };

  return {
    init: function () {
      EditButiranButton = document.querySelector('[data-edit="butiran"]');
      HandleEditButiran();
    },
  };
})();

var handleEditRoads = (function () {
  let roadsElement;
  const systemId = window.location.pathname.split("/").pop();

  const target = document.querySelector("#road-modal-body");
  const ModalBlockUI2 = new KTBlockUI(target, {
    message:
      '<div class="blockui-message"><span class="spinner-border text-primary fs-2"></span> Sedang memuat butiran jalan...</div>',
  });

  function fetchRoadData() {
    ModalBlockUI2.block();
    api
      .get(`projects/wayleaves/roads/data/${systemId}`)
      .then((response) => {
        if (response.status === 200) {
          renderRoads(response.data);
          ModalBlockUI2.release();
          handleSaveBtn.init();
        }
      })
      .catch((error) => {
        console.error("Error fetching road data:", error);
        ModalBlockUI2.release();
      });
  }

  function createSelect(id, options, isMultiple = false) {
    const select = document.createElement("select");
    select.id = id;
    select.name = id.replace(/-\d+$/, "");
    select.className = "form-select";
    if (isMultiple) select.multiple = true;

    options.forEach(([value, text]) => {
      const option = document.createElement("option");
      option.value = value;
      option.textContent = text;
      select.appendChild(option);
    });

    return select;
  }

  function renderRoads(data) {
    roadsElement = document.querySelector("#road-involved");
    roadsElement.innerHTML = ""; // Clear existing content

    data.roads.forEach((road, index) => {
      const roadDiv = document.createElement("div");
      roadDiv.className = "form-group";
      roadDiv.innerHTML = `
        <div data-repeater-list="road-list">
          <h2>Butiran Jalan ${index + 1}</h2>
          <div class="separator separator-dashed my-5"></div>
          <form data-road-form="road">
            <div class="form-group row mb-5">
              <input type="text" name="road-id" value="${road.id}" class="d-none"/>
              <div class="col-md-6 mb-2 fv-row">
                <label class="form-label required">Koordinat Mula</label>
                <input type="text" name="start-coord" class="form-control" 
                  value="${
                    road.start_coordinates
                  }" placeholder="5.32891701, 103.14635436" />
              </div>
              <div class="col-md-6 mb-2 fv-row">
                <label class="form-label required">Koordinat Akhir</label>
                <input type="text" name="end-coord" class="form-control"
                  value="${
                    road.end_coordinates
                  }" placeholder="5.33046594, 103.14851092" />
              </div>
              <div class="col-md-4 mb-2 fv-row">
                <label class="form-label required">Jarak (m)</label>
                <input type="text" name="road-length" class="form-control repeater-value"
                  value="${road.distance}" placeholder="Jarak" />
              </div>
              <div class="col-md-8 mb-2 fv-row">
                <label class="form-label required">Nama Jalan</label>
                <input type="text" name="road-name" class="form-control"
                  value="${road.name}" placeholder="Nama Jalan" />
              </div>
              <div class="col-md-4 mb-2 fv-row">
                <label class="form-label required">Daerah</label>
                <div id="district-container-${index}"></div>
              </div>
              <div class="col-md-8 mb-2 fv-row">
                <label class="form-label required">Kaedah Kerja</label>
                <div id="method-container-${index}"></div>
              </div>
              <div class="col-12">
                <div class="separator separator-dashed my-5"></div>
              </div>
            </div>
          </form>
        </div>
      `;

          if (index === data.roads.length - 1) {
            roadDiv.innerHTML += `
          <div class="d-flex justify-content-end mt-3">
            <button type="button" id="road-save-btn" class="btn btn-light-primary">
              <i class="fad fa-floppy-disk fs-2"></i> Simpan
            </button>
          </div>
        `;
      }

      roadsElement.appendChild(roadDiv);

      const districtSelect = createSelect(
        `road-district-${index}`,
        Object.entries(data.districts).map(([key, value]) => [
          value.district_code,
          value.district_name,
        ])
      );
      document
        .getElementById(`district-container-${index}`)
        .appendChild(districtSelect);

      const methodSelect = createSelect(
        `road-method-${index}`,
        Object.entries(data.work_methods),
        true
      );
      document
        .getElementById(`method-container-${index}`)
        .appendChild(methodSelect);

      // Initialize select2 for district
      $(`#road-district-${index}`)
        .select2({
          placeholder: "Sila pilih daerah",
          allowClear: false,
        })
        .val(road.district)
        .trigger("change");

      // Initialize select2 for method
      $(`#road-method-${index}`).select2({
        placeholder: "Sila pilih kaedah kerja",
        allowClear: false,
        multiple: false,
      });

      // Set method values - Fixed for object format
      let methodValues = [];
      if (typeof road.methods === "object" && road.methods !== null) {
        methodValues = Object.keys(road.methods).map(Number);
      } else if (typeof road.methods === "string") {
        methodValues = road.methods
          .replace(/[{}]/g, "")
          .split(",")
          .map((value) => value.trim())
          .map(Number);
      }
      $(`#road-method-${index}`).val(methodValues).trigger("change");
    });
  }

  function handleModalEvents() {
    $("#kt_roads_modal").on("shown.bs.modal", fetchRoadData);
    $("#kt_roads_modal").on("hidden.bs.modal", () => {
      if (roadsElement) roadsElement.innerHTML = "";
    });
  }

  return {
    init: function () {
      handleModalEvents();
    },
  };
})();

var handleSaveBtn = (function () {

  function serializeForm(form) {
    const formData = new FormData(form);
    const serializedData = {};

    // Loop through each entry and handle multiple select2 values correctly
    for (const [key, value] of formData.entries()) {
      
      if (serializedData[key]) {
        // If the key exists, convert it to an array (to handle multiple selections)
        if (!Array.isArray(serializedData[key])) {
          serializedData[key] = [serializedData[key]];
        }
        serializedData[key].push(value);
      } else {
        if (key == "road-method") {
          serializedData[key] = [value];
        } else if( key == "road-id" || key == "road-length") {
          serializedData[key] = parseInt(value);
        } else {
          serializedData[key] = value;
        }
        
      }
    }
    return serializedData;
  }
  function initSaveBtn() {
    document
      .getElementById("road-save-btn")
      .addEventListener("click", function () {
        const forms = document.querySelectorAll('[data-road-form="road"]');
        const formDataArray = [];

        forms.forEach((form) => {
          const serializedData = serializeForm(form);
          formDataArray.push(serializedData);
        });
        // console.log(JSON.stringify(formDataArray, null, 2));
        const systemId = window.location.pathname.split("/").pop();
        api
          .put("projects/wayleaves/roads/data/" + systemId, JSON.stringify(formDataArray))
          .then((response) => {
            if (response.status === 200) {
              toastr.success(response.message);
              $("#kt_roads_modal").modal("hide");
              $('[data-road="table-body"]').empty();
              updateCardData(response.data);
            }
          });

      });
  }

  function updateCardData(data) {
    const DE = data.dataentry.districts
      .split(",")
      .map((district) => district.trim());

    const colors = ["primary", "info", "success", "danger", "warning", "dark"];
    const shuffleColors = (array) => array.sort(() => 0.5 - Math.random());

    const createDistrictElement = (district, color) => {
      const span = document.createElement("span");
      span.className = `badge badge-light-${color} me-2`;
      span.textContent = district;
      return span;
    };

    const renderDistricts = (container, districts, colors) => {
      const fragment = document.createDocumentFragment();
      const shuffledColors = shuffleColors([...colors]);

      districts.forEach((district, index) => {
        const color = shuffledColors[index % shuffledColors.length];
        const districtElement = createDistrictElement(district, color);
        fragment.appendChild(districtElement);
      });

      container.innerHTML = ""; // Clear existing content
      container.appendChild(fragment);
    };

    const populateRoadTable = (data) => {
      const tableBody = document.querySelector('[data-road="table-body"]');
      if (!tableBody) {
        console.error("Table body element not found");
        return;
      }

      // Clear existing table content
      tableBody.innerHTML = "";

      // Create table rows
      data.dataroad.forEach((road) => {
        const row = document.createElement("tr");

        // Name
        const nameCell = document.createElement("td");
        const nameLabel = document.createElement("label");
        nameLabel.className = "w-150px";
        nameLabel.textContent = road.name;
        nameCell.appendChild(nameLabel);
        row.appendChild(nameCell);

        // Methods
        const methodsCell = document.createElement("td");
        const methodColors = shuffleColors([...colors]);
        road.methods.split(",").forEach((method, index) => {
          const methodSpan = document.createElement("span");
          const color = methodColors[index % methodColors.length];
          methodSpan.className = `badge badge-light-${color} me-2`;
          methodSpan.textContent = method.trim();
          methodsCell.appendChild(methodSpan);
        });
        row.appendChild(methodsCell);

        // Start coordinates
        const startCoordCell = document.createElement("td");
        startCoordCell.textContent = road.start_coordinates;
        row.appendChild(startCoordCell);

        // End coordinates
        const endCoordCell = document.createElement("td");
        endCoordCell.textContent = road.end_coordinates;
        row.appendChild(endCoordCell);

        // Distance
        const distanceCell = document.createElement("td");
        distanceCell.textContent = road.distance;
        row.appendChild(distanceCell);

        tableBody.appendChild(row);
      });
    };

    const container = document.getElementById("de-districts");
    if (container) {
      renderDistricts(container, DE, colors);
      populateRoadTable(data);
    } else {
      console.error("Elemen 'de-districts' tidak dijumpai");
    }
  }
  
  return {
    init: function () {
      initSaveBtn();
    },
  };
})();

KTUtil.onDOMContentLoaded(function () {
    handleEditButiran.init();
    handleEditRoads.init();
});