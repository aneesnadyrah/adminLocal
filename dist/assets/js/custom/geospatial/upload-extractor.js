// Data Extractor
let geoPackage;
let workMethodList;
let uploadedCount = 0;
let publicRepeaterVar;

let geoPackageData = {
  line: [],
  point: []
};
let drawnLayers = [];

toastr.options = {
  closeButton: false,
  debug: false,
  newestOnTop: false,
  progressBar: false,
  positionClass: "toastr-bottom-right",
  preventDuplicates: true,
  onclick: null,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "2000",
  extendedTimeOut: "1000",
  showEasing: "swing",
  hideEasing: "linear",
  showMethod: "fadeIn",
  hideMethod: "fadeOut",
};

function readGeoPackage(geoPackage, type) {
  const featureTables = geoPackage.getFeatureTables();
  featureTables.forEach(function (table) {
    try {
      if ((type === 'line' && table.startsWith("LINE")) || (type === 'point' && table.startsWith("POINT"))) {
        saveGeoJSON(table, geoPackage, type);
      }
    } catch (err) {
      console.log(`Error opening ${type} table ${table}`, err);
    }
  });
}

function loadByteArray(array, fileName) {
  return window.GeoPackage.GeoPackageAPI.open(array).then(function (gp) {
    geoPackage = gp;
    // console.log("GeoPackage loaded:", fileName);
    // Determine the type of GeoPackage based on its filename
    var type = '';
    if (fileName.startsWith("LINE")) {
      type = 'line';
    } else if (fileName.startsWith("POINT")) {
      type = 'point';
    } else {
      toastr.error("Sila Masukkan Fail 'LINE.GPKG' atau POINT.gpkg sahaja 😒");
      return; // Exit the function early if the file name is invalid
    }

    // Read the GeoPackage
    readGeoPackage(gp, type);

  }).catch(function (error) {
    console.error("Error loading GeoPackage:", error);
    toastr.error("Error loading GeoPackage");
  });
}

window.loadGeoPackage = function (files) {
  const f = files[0];

  // TODO: ScriptCheck:: check if the file upload have a valid name & extension
  if (f.name.startsWith("LINE") || f.name.startsWith("POINT")) {

    const r = new FileReader();
    r.onload = function () {
      const array = new Uint8Array(r.result);

      loadByteArray(array, f.name).then(function () { });
    };
    r.readAsArrayBuffer(f);

  } else {
    console.log("Invalid file name:", f.name); // Log the invalid file name
    toastr.error("Sila Masukkan Fail 'LINE.GPKG' atau POINT.gpkg sahaja! 😒");
  }
};

function saveGeoJSON(tableName, geoPackage, type) {
  const converter = new GeoJSONToGeoPackage();
  converter.extract(geoPackage, tableName).then(function (geoJson) {
    const data = {
      action: "gpkg",
      type: type, // 'line' or 'point'
      geom: geoJson.features,
      systemID: systemID
    };

    api.post('geospatial/conversion', JSON.stringify(data)).then(response => {
      // Store the response data
      if (type === 'line' && response.line) {
        geoPackageData.line = response.line;
      } else if (type === 'point' && response.point) {
        geoPackageData.point = response.point;
      }

      toastr.success("Surihan Laluan anda berjaya disimpan!🎉");

      // Disable indicator after 3 seconds
      setTimeout(function () {
        uploadedCount++;
        if (uploadedCount === 4) {
          document.getElementById("road-info").style.display = "block";
        }
      }, 3000);

    }).catch(error => {
      console.error('Error saving GeoJSON:', error);
      toastr.error("Error saving GeoJSON");
    });

  }).catch(error => {
    console.error('Error extracting GeoJSON:', error);
    toastr.error("Error extracting GeoJSON");
  });
}

var uploaddropzonePIL = (function () {
  var dropzones = [];

  var handleDropzone = () => {
    var elements = [].slice.call(document.querySelectorAll('[data-upload]'));
    elements.map(function (element) {
      var secret = element.getAttribute("data-upload");
      var type = element.getAttribute("data-type");
      var chunks = secret.split('-');
      var statusId = chunks[2];
      var folder = chunks[0];
      var systemId = chunks[1];

      var dropzoneConfig = {
        url: `${apps}/api/tasks/${statusId}/upload`,
        paramName: "file",
        maxFiles: 1,
        autoProcessQueue: true,
        addRemoveLinks: true,

        sending: function (file, xhr, formData) {
          // Check if the file type is .gpkg
          if (file.name.endsWith('.gpkg')) {
            window.loadGeoPackage([file]); // Pass the file as an array to match the expected parameter of loadGeoPackage
          } else {
            // If it's not a .gpkg file, append additional form data to the FormData object
            formData.append("systemId", systemId);
            formData.append("folder", folder);
          }
        },
        accept: function (file, done) {
          // console.log("Type:", type);
          // console.log("File name:", file.name);

          if (type === ".pdf" && file.type === "application/pdf") {
            done();
          } else if (type === ".zip") {
            done();
          } else if (type === ".gpkg" && (file.name.startsWith("LINE") || file.name.startsWith("POINT"))) {
            done();
          } else {
            done('Invalid file type or name');
          }
        },
        // Set acceptedFiles to null initially, will be overridden based on file type
        acceptedFiles: null
      };

      // Set acceptedFiles based on file type
      if (type === ".pdf") {
        dropzoneConfig.acceptedFiles = "application/pdf";
      } else if (type === ".zip") {
        dropzoneConfig.acceptedFiles = ".zip";
      }
      else if (type === ".gpkg") {
        dropzoneConfig.acceptedFiles = ".gpkg";
      }

      var dropzoneInstance = new Dropzone(element, dropzoneConfig);

      dropzoneInstance.on("success", function () {
        uploadedCount++;
        // console.log(uploadedCount);
        // if (type != ".gpkg") {
        //     uploadedCount++;
        // }
        if (uploadedCount === 4) {
          document.getElementById("road-info").style.display = "block";
        }
      });

      dropzoneInstance.on("error", function (file, errorMessage, xhr) {
        // Check if the error is due to the file type not being accepted
        if (xhr && xhr.status === 415) {
          console.error("Error: File type not supported. Please upload a GeoPackage file.");
        } else {
          // console.error(errorMessage);
          toastr.error("Sila Masukkan Fail 'LINE.GPKG' atau POINT.gpkg sahaja! 😒");
        }
      });

      dropzoneInstance.on("removedfile", function () {
        // Handle file removal for both types
        // submitButton.classList.add("d-none");
      });

      dropzones.push(dropzoneInstance);
    });
  }

  return {
    init: function () {
      workMethodList = [
        {
          id: 1,
          shortcode: "HDD"
        },
        {
          id: 2,
          shortcode: "GV"
        },
        {
          id: 3,
          shortcode: "CW"
        },
        {
          id: 4,
          shortcode: "PJ"
        },
        {
          id: 5,
          shortcode: "CP"
        },
        {
          id: 6,
          shortcode: "OH"
        },
        {
          id: 7,
          shortcode: "ID"
        },
        {
          id: 8,
          shortcode: "ED"
        },
        {
          id: 9,
          shortcode: "GI"
        },
        {
          id: 10,
          shortcode: "MT"
        },
        {
          id: 11,
          shortcode: "TB"
        },
        {
          id: 12,
          shortcode: "MS"
        }
      ];

      handleDropzone();
    },
  };
})();

function formDataToJson(formData) {
  const jsonObject = {};

  for (const [key, value] of formData) {
    const keys = key.split(/\]\[|\[|\]/).filter(Boolean);

    let currentObject = jsonObject;
    for (let i = 0; i < keys.length - 1; i++) {
      const currentKey = keys[i];
      if (!currentObject.hasOwnProperty(currentKey)) {
        // If the key doesn't exist, create an object
        if (/^\d+$/.test(keys[i + 1])) {
          currentObject[currentKey] = []; // Use an array if the next key is a number
        } else {
          currentObject[currentKey] = {}; // Use an object if the next key is not a number
        }
      }
      currentObject = currentObject[currentKey];
    }

    // Handle the last key (leaf)routeDetailsBtnvalidation
    const lastKey = keys[keys.length - 1];
    if (lastKey === "" || /\[\]$/.test(key)) {
      // If the last key is empty or ends with '[]', treat the value as an array
      if (!Array.isArray(currentObject[lastKey])) {
        currentObject[lastKey] = [value];
      } else {
        currentObject[lastKey].push(value);
      }
    } else {
      currentObject[lastKey] = value;
    }
  }

  return jsonObject;
}

// modal for route list
let routeModalEl = document.getElementById('gis-pil-route-modal');
let routeModal = new bootstrap.Modal(routeModalEl);
let routeDetailsBtn = document.getElementById('road-info');

routeDetailsBtn.addEventListener('click', function (e) {
  // Prevent button default action
  e.preventDefault();

  routeModal.show();

});

let repeaterPush = [];

var roadInfo = (function () {
  var handleRoadInfo = () => {
    var roadButtons = [].slice.call(document.querySelectorAll('[data-road]'));

    roadButtons.map(function (roadButton) {
      var secret = roadButton.getAttribute("data-road");
      var chunks = secret.split('-');
      var systemId = chunks[1];

      // Function to handle form submit
      roadButton.addEventListener("click", async (e) => {
        // Prevent button default action
        e.preventDefault();

        let repeaterPush = [];

        api.get(`geospatial/conversion?action=route-pkd&systemId=${systemId}`).then(response => {
          // console.log(response.road_pkd);

          var roadData = response.road_pkd;

          roadData.forEach(function (item) {
            // console.log(item)

            let coorStart = [item.latitude_start, item.longitude_start];
            let coorEnd = [item.latitude_end, item.longitude_end];

            repeaterPush.push({
              "road-id": item.flw_gis_road_id,
              "road_name": item.road_name,
              "district": item.districts,
              "coor_start": coorStart,
              "coor_end": coorEnd,
              "authority": item.authority
            })
          })
          publicRepeaterVar.setList(repeaterPush);

        }).catch(error => {

        })
      })

    })
  }
  return {
    // Initialization
    init: function () {
      handleRoadInfo();
    }
  };
})();

// Class definition
var GISExtract = (function () {
  // let form;
  let formModal;
  let validator;
  let routeListRepeaterEl;
  let submitButton;

  var extractSubmit = function () {
    // Function to send data
    let sendData = function (data) {
      // console.log({ savingData: data });
      api.post('geospatial/conversion', data).then(response => {
        if (response.status === 200) {
          const formData = {
            systemId: response.systemId,
            notes: response.notes
          };
          const json = JSON.stringify(formData);

          api.post(`tasks/24/submit`, json).then(response2 => {
            if (response2.status === 200) {
              submitButton.removeAttribute("data-kt-indicator");

              toastr.success(response2.message);

              setTimeout(function () {
                location.href = "geospatial/general/tasks/pil";
              }, 2500);
            }
          });
        }
      }).catch(error => {
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");

        // Enable button
        submitButton.disabled = false;

        toastr.error("Maaf, Terdapat ralat di bahagian sistem. Sila cuba sekali lagi");
      })

    };

    // Handle form submit
    submitButton.addEventListener("click", function (e) {
      // Prevent button default action
      e.preventDefault();

      // Validate form
      if (validator) {
        validator.validate().then(function (status) {
          if (status == "Valid") {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            const formModalData = new FormData(formModal);

            // Iterate through formData entries and append them to formModal
            // for (const [key, value] of formData.entries()) {
            //     formModalData.append(key, value);
            // }

            const data = formDataToJson(formModalData);
            data.action = "route-pil";
            let proceedAction = true;
            proceedAction = true;
            sendData(data);
          } else {
            toastr.error(
              "Maaf, Isi Semua Butiran diatas."
            );
          }
        });
      }
    });
  };

  var routeListInit = function () {

    validator = FormValidation.formValidation(
      formModal,
      {
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap: new FormValidation.plugins.Bootstrap5({
            rowSelector: ".fv-row",
            eleInvalidClass: "",
            eleValidClass: ""
          }),
          excluded: new FormValidation.plugins.Excluded({
            excluded: function (field, ele, eles) {
              if (formModal.querySelector('[name="' + field + '"]') === null) {
                return true;
              }
            },
          }),
        }
      }
    );

    const addFields = function (index) {
      const namePrefix = "gis_route_list[" + index + "]";

      // Add validators
      validator.addField(namePrefix + "[road_name]", {
        validators: {
          notEmpty: {
            message: "Nama Jalan perlu diisi."
          }
        }
      });

      validator.addField(namePrefix + "[coor_start]", {
        validators: {
          notEmpty: {
            message: "Koordinat mula perlu diisi."
          }
        }
      });

      validator.addField(namePrefix + "[coor_end]", {
        validators: {
          notEmpty: {
            message: "Koordinat akhir Jalan perlu diisi."
          }
        }
      });

      validator.addField(namePrefix + "[district]", {
        validators: {
          notEmpty: {
            message: 'Sila pilih daerah terlibat.'
          }
        }
      });

      validator.addField(namePrefix + "[authority]", {
        validators: {
          notEmpty: {
            message: 'Sila pilih pihak berkuasa terlibat.'
          }
        }
      });

    };

    // get the repeater element
    let privateRepeaterVar = $(routeListRepeaterEl).repeater({
      initEmpty: false,

      defaultValues: {
        'text-input': 'foo'
      },

      show: function () {
        $(this).slideDown();

        // Re-init select2
        $(this).find('[data-kt-repeater="select2"]').select2({
          dropdownParent: routeModalEl.querySelector('.modal-content'),
          selectOnClose: true
        });

        // Re-init tooltip
        let tooltipEl = $(this).find('[data-bs-toggle="tooltip"]');
        tooltipEl.tooltip(); // Initialize Bootstrap tooltip

        let coorStartInput = $(this).find('[data-route-list="coor-start"]');
        let coorEndInput = $(this).find('[data-route-list="coor-end"]');

        const index = $(this).closest("[data-repeater-item]").index();
        // TODO: commented for now
        // console.log({ repeaterItemIndex: index });
        addFields(index);
        $(this).find('[data-kt-repeater="select2"]').select2().on('change.select2', function () {
          // Revalidate the color field when an option is chosen
          validator.revalidateField("gis_route_list[" + index + "]" + "[authority]");
          validator.revalidateField("gis_route_list[" + index + "]" + "[district]");
        });
      },

      hide: function (deleteElement) {
        const currentElement = $(this);

        currentElement.slideUp(function () {
          deleteElement();
        });
      },

      ready: function (setIndexes) {
        // Init select2
        let select2El = $('[data-kt-repeater="select2"]');
        select2El.select2({
          dropdownParent: routeModalEl.querySelector('.modal-content'),
          selectOnClose: true
        });

        // init tooltip
        let tooltipEl = $('[data-bs-toggle="tooltip"]');
        tooltipEl.tooltip(); // Initialize Bootstrap tooltip

        const index = select2El.closest("[data-repeater-item]").index();

        // Listen to repeater reorder event
        document.addEventListener('repeaterReorder', function (event) {
          // reset repeater index
          setIndexes();
        });

        addFields(index);
        select2El.select2().on('change.select2', function () {
          // Revalidate the color field when an option is chosen
          validator.revalidateField("gis_route_list[" + index + "]" + "[authority]");
          validator.revalidateField("gis_route_list[" + index + "]" + "[district]");
        });
      }
    });

    // assign private repeater to public scope variable
    publicRepeaterVar = privateRepeaterVar;
  };

  $('#gis-pil-route-modal').on('shown.bs.modal', function () {
    initMapRoadView();
  });

  var mapView;
  var markers = [];

  const initMapRoadView = () => {
    if (!mapView) {
      mapView = L.map('map-modal').setView([4.27609737890969, 102.07725116565415], 5);

      var streetView = L.tileLayer('https://{s}.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        label: 'Google Roadmap',
        minZoom: 0,
        maxZoom: 21
      });

      var satelliteView = L.tileLayer('https://{s}.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        label: 'Google Satellite',
        minZoom: 0,
        maxZoom: 21
      });

      mapView.addControl(
        L.control.basemaps({
          basemaps: [streetView, satelliteView],
          tileX: 0,
          tileY: 0,
          tileZ: 1,
          position: 'topright',
        }),
      );
    }

    // Clear previous layers
    function clearDrawnLayers() {
      drawnLayers.forEach(layer => mapView.removeLayer(layer));
      drawnLayers = [];
    }

    // Function to draw lines on map
    function drawLines() {
      if (!geoPackageData.line || geoPackageData.line.length === 0) return;

      geoPackageData.line.forEach(feature => {
        if (feature.geometry && feature.geometry.type === 'MultiLineString') {
          const coordinates = feature.geometry.coordinates[0]; // Get first LineString
          const latLngs = coordinates.map(coord => [coord[1], coord[0]]); // Convert [lng, lat] to [lat, lng]

          // Get properties
          const jns = feature.properties.jns || feature.properties.JNS || '';
          const dist = feature.properties.dist || feature.properties.JRK || '';
          const pbm = feature.properties.pbm || feature.properties.PBM || '';

          // Create polyline
          const polyline = L.polyline(latLngs, {
            color: '#3B82F6',
            weight: 4,
            opacity: 0.8
          }).addTo(mapView);

          // Add popup with info
          const popupContent = `
                    <div>
                        <strong>Jenis:</strong> ${jns}<br>
                        <strong>Jarak:</strong> ${dist}<br>
                        ${pbm ? `<strong>PBM:</strong> ${pbm}` : ''}
                    </div>
                `;
          polyline.bindPopup(popupContent);

          drawnLayers.push(polyline);
        }
      });
    }

    // Function to draw points on map
    function drawPoints() {
      if (!geoPackageData.point || geoPackageData.point.length === 0) return;

      var pointIcon = L.icon({
        iconUrl: '/assets/plugins/custom/leaflet/images/marker-icon.png',
        shadowUrl: '/assets/plugins/custom/leaflet/images/marker-shadow.png',
        iconAnchor: [12.5, 41],
        popupAnchor: [0, -41]
      });

      geoPackageData.point.forEach(feature => {
        if (feature.geometry && feature.geometry.type === 'MultiPoint') {
          const coordinates = feature.geometry.coordinates[0]; // Get first point
          const latLng = [coordinates[1], coordinates[0]]; // Convert [lng, lat] to [lat, lng]

          // Get properties
          const jns = feature.properties.jns || feature.properties.JNS || '';
          const name = feature.properties.name || feature.properties.NAME || 'Tidak bernama';

          // Create marker
          const marker = L.marker(latLng, { icon: pointIcon }).addTo(mapView);

          // Add popup with info
          const popupContent = `
                    <div>
                        <strong>Jenis:</strong> ${jns}<br>
                        <strong>Nama:</strong> ${name}
                    </div>
                `;
          marker.bindPopup(popupContent);

          drawnLayers.push(marker);
        }
      });
    }

    // Function to fit map bounds to all features
    function fitMapBounds() {
      const bounds = L.latLngBounds();
      let hasValidBounds = false;

      // Add line coordinates to bounds
      if (geoPackageData.line && geoPackageData.line.length > 0) {
        geoPackageData.line.forEach(feature => {
          if (feature.geometry && feature.geometry.type === 'MultiLineString') {
            feature.geometry.coordinates[0].forEach(coord => {
              bounds.extend([coord[1], coord[0]]);
              hasValidBounds = true;
            });
          }
        });
      }

      // Add point coordinates to bounds
      if (geoPackageData.point && geoPackageData.point.length > 0) {
        geoPackageData.point.forEach(feature => {
          if (feature.geometry && feature.geometry.type === 'MultiPoint') {
            const coord = feature.geometry.coordinates[0];
            bounds.extend([coord[1], coord[0]]);
            hasValidBounds = true;
          }
        });
      }

      // Fit bounds if we have valid data
      if (hasValidBounds) {
        mapView.flyToBounds(bounds, {
          padding: [50, 50],
          duration: 1.5
        });
      }
    }

    // Main function to render all GeoPackage data
    function renderGeoPackageData() {
      clearDrawnLayers();
      drawLines();
      drawPoints();
      fitMapBounds();
    }

    // Call render function after a short delay to ensure map is ready
    setTimeout(renderGeoPackageData, 500);
  };

  return {
    // Initialization
    init: function () {
      submitButton = document.querySelector("#submit-gis-pil");
      routeListRepeaterEl = routeModalEl.querySelector('#gis_route_repeater');
      formModal = document.querySelector("#form-gis-plan-modal");

      routeListInit();
      extractSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  uploaddropzonePIL.init();
  roadInfo.init();
  GISExtract.init();
});
