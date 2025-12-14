
//Init Map View
const originalInitTile = L.GridLayer.prototype._initTile;
if (!originalInitTile.isPatched) {
  L.GridLayer.include({
    _initTile: function (tile) {
      originalInitTile.call(this, tile);

      const tileSize = this.getTileSize();

      tile.style.width = tileSize.x + 1 + 'px';
      tile.style.height = tileSize.y + 1 + 'px';
    },
  });

  L.GridLayer.prototype._initTile.isPatched = true;
}

// Map Initial Setup
var mapGis = L.map('map', {
  minZoom: 0,
  maxZoom: 21
}).setView([3.559406934490606, 102.75038143762556], 9);

var street = L.tileLayer('https://{s}.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}', {
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
  label: 'Google Roadmap',
  minZoom: 0,
  maxZoom: 21
});

var satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}', {
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
  label: 'Google Roadmap',
  minZoom: 0,
  maxZoom: 21
});

mapGis.addControl(
  L.control.basemaps({
    basemaps: [street, satellite],
    tileX: 0,
    tileY: 0,
    tileZ: 1,
  }),
);

mapGis.zoomControl.setPosition('topright');

// Data Extractor
let geoPackage;
let tableLayers;
let featureLayers;
let imageOverlay;
let currentTile = {};
let tableInfos;
let fileName;
let closestLayer;
let extractorActionEl;
let publicRepeaterVar; // this variable is used for assigning private repeater to public scope
let workMethodList;
// let totalLengthInputEl;

// Create a custom event for repeater reordering
const repeaterReorder = new CustomEvent('repeaterReorder', {
  detail: { function: 'Monitor Repeater Reorder Function' },
});

function toRadians(degrees) {
  return degrees * (Math.PI / 180.0);
}

function getTileFromPoint(latlng) {
  const xtile = parseInt(Math.floor(((latlng.lng + 180) / 360) * (1 << mapGis.getZoom())));
  const ytile = parseInt(
    Math.floor(
      ((1 - Math.log(Math.tan(toRadians(latlng.lat)) + 1 / Math.cos(toRadians(latlng.lat))) / Math.PI) / 2) *
      (1 << mapGis.getZoom()),
    ),
  );
  return {
    z: mapGis.getZoom(),
    x: xtile,
    y: ytile,
  };
}

function mapClickEventHandler(event) {
  if (closestLayer) {
    mapGis.removeLayer(closestLayer);
  }
  const latitude = event.latlng.lat;
  const longitude = event.latlng.lng;
  const {
    x,
    y,
    z
  } = getTileFromPoint(event.latlng);
  const closestFeatures = [];
  for (const featureTable in featureLayers) {
    const cf = geoPackage.getClosestFeatureInXYZTile(featureTable, x, y, z, latitude, longitude);
    if (cf) closestFeatures.push(cf);
  }
  console.log('closest', closestFeatures);
  closestFeatures.sort(function (first, second) {
    if (first.coverage && second.coverage) return 0;
    if (first.coverage) return 1;
    if (second.coverage) return -1;
    return first.distance - second.distance;
  });
  if (closestFeatures.length) {
    let popup;
    closestLayer = L.geoJSON(closestFeatures[0], {
      style: function (f) {
        switch (f.properties.jns) {
          case 'HDD': return { color: "#0e6fc9", weight: 6 };
          case 'CW': return { color: "#c90e3d", weight: 6 };
          case 'GV': return { color: "#05ab6e", weight: 6 };
          case 'GI': return { color: "#dbbf07", weight: 6 };
          case 'OH': return { color: "#07dbd1", weight: 6 };
          default: return { color: "#422805", weight: 6 };
        }
      },
      onEachFeature: function (f, l) {
        let color;
        switch (f.properties.jns) {
          case 'HDD': color = "primary"; break;
          case 'CW': color = "danger"; break;
          case 'GV': color = "success"; break;
          case 'GI': color = "warning"; break;
          case 'OH': color = "dark"; break;
          default: color = "secondary";
        }
        let dataPopup = `
        <div>
          <h6 class="fw-semibold">Butiran Laluan Surihan</h6>
          <table class="table table-bordered">
            <tr>
              <th>Jarak</th>
              <td class="fw-semibold">`+ f.properties.Shape_Leng + `</td>
            </tr>
            <tr>
              <th>Kaedah</th>
              <td><span class="badge badge-light-`+ color + ` fs-6">` + f.properties.jns + `</span></td>
            </tr>
          </table>
        </div>`;

        popup = l.bindPopup(dataPopup, {
          maxHeight: 300,
          maxWidth: 500,
          closeButton: false,
        });
      },
    });
    mapGis.addLayer(closestLayer);
    popup.openPopup();
  }
  return closestFeatures;
}

function getIdFromShortcode(array, shortcode) {
  for (let i = 0; i < array.length; i++) {
    if (array[i].shortcode === shortcode) {
      return array[i].id;
    }
  }
  // Return null or a default value if the shortcode is not found
  return null;
}

// function updateInputValue(inputElement, newValue) {
//   if (inputElement && inputElement instanceof HTMLInputElement) {
//     inputElement.value = newValue;
//   } else {
//     console.error("Invalid input element or element not found.");
//   }
// }

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

mapGis.on('click', mapClickEventHandler);

// modal for route list
let routeModalEl = document.getElementById('gis-plan-route-modal');
let routeModal = new bootstrap.Modal(routeModalEl);
let routeDetailsBtn = document.getElementById('route-details-btn');
let validators;
let form = document.querySelector("#form-gis-plan");

validators = FormValidation.formValidation(form, {
  fields: {
    "density": {
      validators: {
        notEmpty: {
          message: "Sila Pilih Kepadatan",
        },
      },
    },
  },
  plugins: {
    trigger: new FormValidation.plugins.Trigger(),
    bootstrap: new FormValidation.plugins.Bootstrap5({
      rowSelector: ".fv-row",
      eleInvalidClass: "", // comment to enable invalid state icons
      eleValidClass: "", // comment to enable valid state icons
    }),
  },
});

$(document).ready(function () {
  $("#density-select").select2().on('change.select2', function () {
    // Revalidate the color field when an option is chosen
    validators.revalidateField('density');
  });
});

routeDetailsBtn.addEventListener('click', function (e) {
  // Prevent button default action
  e.preventDefault();

  if (validators) {
    validators.validate().then(function (status) {
      if (status == "Valid") {
        // Show modal
        routeModal.show();

      } else {
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

        toastr.error(
          "Maaf, Isi Semua Butiran."
        );
      }
    });
  }

});

// Save Extracted features into geom table
window.saveGeoJSON = function (tableName) {
  const saveButton = document.querySelector('[data-action="save"]');
  const routeViewToggle = document.querySelector('[data-map-extractor="view-toggle"]');
  const routeZoomToggle = document.querySelector('[data-map-extractor="view-zoom"]');

  const converter = new GeoJSONToGeoPackage();
  converter.extract(geoPackage, tableName).then(function (geoJson) {
    // Show loading indication
    saveButton.setAttribute("data-kt-indicator", "on");

    // Disable button to avoid multiple click
    saveButton.disabled = true;
    const geom = geoJson.features;

    const data = {
      action: "gpkg",
      type: "line",
      geom: geom,
      systemID: systemID
    }

    let coordinateCol = [];
    let repeaterPush = [];
    let coordStart = [102.55565237965116, 5.8220829644862535];

    geom.forEach(function (element) {
      // TODO: commented for now
      // console.log({ "geomObject": element });
      let objectToPush = {};
      objectToPush.method = element.properties.jns;
      objectToPush.length = parseFloat(element.properties.Shape_Leng);
      objectToPush.geomtype = element.geometry.type;
      objectToPush.coordinates = (element.geometry.coordinates.length == 1 ? element.geometry.coordinates[0] : element.geometry.coordinates);
      objectToPush.coorStrEnd = getFirstAndLast(objectToPush.coordinates);
      objectToPush.authority = (element.properties.PBM !== undefined ? element.properties.PBM : null);
      if (objectToPush.authority == null) {
        objectToPush.authority = (element.properties.PBT !== undefined ? element.properties.PBT : null)
      }
      coordinateCol.push(objectToPush);

      var startInput = objectToPush.coorStrEnd[0];
      var endInput = objectToPush.coorStrEnd[1];

      // console.log(startInput[0]);
      // console.log(startInput[1]);

      var startCoord = [startInput[1], startInput[0]];
      var endCoord = [endInput[1], endInput[0]];

      repeaterPush.push({
        // "method": getIdFromShortcode(workMethodList, objectToPush.method),
        // "route-length": objectToPush.length,
        // "coor_start": objectToPush.coorStrEnd[0],
        // "coor_end": objectToPush.coorStrEnd[1]
        "coor_start": startCoord,
        "coor_end": endCoord
      })
    });

    // publicRepeaterVar.setList(repeaterPush);

    // TODO: commented for now
    // console.log({ "coordinateCol": coordinateCol });

    let sortedCoordinate = sortObjectsByDistance(coordinateCol, coordStart);

    // TODO: commented for now
    // console.log({ "sortedCoordinate": sortedCoordinate });

    api.post('geospatial/conversion', JSON.stringify(data)).then(response => {

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

      toastr.success("Surihan Laluan anda berjaya disimpan!🎉");

      // show route list modal
      // routeModal.show();
      const information = document.querySelector('[data-form="metadata"]');
      information.classList.remove("d-none");
      information.classList.add("d-inline-block");
      saveButton.classList.add("d-none");

      // side panel reference
      // $("#side-length").val(response.length);
      // $("#side-revision").val(response.revision);

      $("#gpkg-length").val(response.length);
      $("#data-revision").val(response.revision);

      if (routeViewToggle.checked == false) {
        // Set the checked property to true
        routeViewToggle.checked = true;
        // Dispatch a 'change' event on the checkbox element
        routeViewToggle.dispatchEvent(new Event('change'));
      }

      // Trigger a click event on the element
      routeZoomToggle.click();

    }).catch(error => {

    })


    api.get(`geospatial/conversion?action=route-entry&systemId=${systemID}`).then(response => {
      console.log(response.road_data);

      let repeaterPush = [];

      var roadData = response.road_data;

      roadData.forEach(function (item) {

        let coorStart = [item.latitude_start, item.longitude_start];
        let coorEnd = [item.latitude_end, item.longitude_end];

        repeaterPush.push({
          "road-id": item.id,
          "road_name": item.road_name,
          "district": item.districts,
          "coor_start": coorStart,
          "coor_end": coorEnd
        })
      })

      publicRepeaterVar.setList(repeaterPush);

    }).catch(error => {

    })

  });
};

function clearInfo() {
  const featureTableNode = $('#feature-tables');
  featureTableNode.empty();

  for (layerName in tableLayers) {
    mapGis.removeLayer(tableLayers[layerName]);
  }
  tableLayers = {};
  featureLayers = {};
  if (imageOverlay) {
    mapGis.removeLayer(imageOverlay);
  }
}

function readGeoPackage(geoPackage) {
  tableInfos = {};
  const featureTableTemplate = $('#feature-table-template').html();
  Mustache.parse(featureTableTemplate);

  const featureTableNode = $('#feature-tables');

  const featureTables = geoPackage.getFeatureTables();
  featureTables.forEach(function (table) {
    try {
      const featureDao = geoPackage.getFeatureDao(table);
      const info = geoPackage.getInfoForTable(featureDao);
      tableInfos[table] = info;
      const rendered = Mustache.render(featureTableTemplate, info);
      featureTableNode.append(rendered);

      // assign action buttons collection to the global variable
      extractorActionEl = document.querySelector('[data-map-extractor="action1"]');

      const routeViewToggle = document.querySelector('[data-map-extractor="view-toggle"]');
      const routeZoomToggle = document.querySelector('[data-map-extractor="view-zoom"]');

      if (routeViewToggle.checked == false) {
        // Set the checked property to true
        routeViewToggle.checked = true;
        // Dispatch a 'change' event on the checkbox element
        routeViewToggle.dispatchEvent(new Event('change'));
      }

      // Trigger a click event on the element
      routeZoomToggle.click();
    } catch (err) {
      console.log('Error opening table ' + table, err);
    }
  });
}

function loadByteArray(array) {
  clearInfo();

  return window.GeoPackage.GeoPackageAPI.open(array).then(function (gp) {
    geoPackage = gp;
    readGeoPackage(gp);
  });
}

// TODO: ScriptCheck::Dropzone element grabbed here
document.querySelector('.dropzone').addEventListener('click', function () {
  document.querySelector('#file').click();
  // TODO: ScriptCheck::on ('#file') this element change, run loadGeoPackage(this.files)
});
const target = document.querySelector("#upload-block");
const blockUI = new KTBlockUI(target, {
  message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Translasi...</div>',
  overlayClass: "rounded bg-secondary bg-opacity-75",
});

window.loadGeoPackage = function (files) {
  // const dropzoneIndicator = document.getElementById('dropzoneIndicator');
  // dropzoneIndicator.classList.remove('d-none');
  // dropzoneIndicator.style.display = 'block';

  // // Add the 'blurred' class to the dropzone to apply the blur effect
  // const dropzoneUpload = document.getElementById('dropzone');
  // dropzoneUpload.classList.add('blurred');

  const f = files[0];

  // TODO: ScriptCheck:: check if the file upload have a valid name & extension
  // if (f.name === "LINE.gpkg") {
    blockUI.block();
    const separator = document.querySelector('[data-separator="view"]');

    separator.classList.add("separator");
    separator.classList.add("separator-dashed");

    // dropzoneIndicator.classList.add('d-none');
    // dropzoneUpload.classList.remove('blurred');

    const r = new FileReader();
    r.onload = function () {
      const array = new Uint8Array(r.result);

      /* The code below is sending an event to Google Analytics. The event is categorized as
      "GeoPackage" and the action is "load". The event label is set to "File Size" and the event
      value is set to the byte length of an array. */
      // ga('send', {
      //   hitType: 'event',
      //   eventCategory: 'GeoPackage',
      //   eventAction: 'load',
      //   eventLabel: 'File Size',
      //   eventValue: array.byteLength,
      // });
      /* The code below is using JavaScript to load a byte array and then executing a function once the
      loading is complete. */
      loadByteArray(array).then(function () {blockUI.release();});
      /* The code below is calling the `release()` function on the `blockUI` object. This function is
      likely used to release or unblock a user interface element that was previously blocked or
      disabled. */

    };

    /* The code below is using the `readAsArrayBuffer` method to read the contents of a file (`f`) as
    an ArrayBuffer. */
    r.readAsArrayBuffer(f);
    // dropzoneIndicator.classList.add('d-none')
    // dropzoneUpload.classList.remove('blurred');

  // } else {
  //   // dropzoneIndicator.classList.add('d-none');
  //   // dropzoneUpload.classList.remove('blurred');

  //   toastr.options = {
  //     closeButton: false,
  //     debug: false,
  //     newestOnTop: false,
  //     progressBar: false,
  //     positionClass: "toastr-bottom-right",
  //     preventDuplicates: true,
  //     onclick: null,
  //     showDuration: "300",
  //     hideDuration: "1000",
  //     timeOut: "2000",
  //     extendedTimeOut: "1000",
  //     showEasing: "swing",
  //     hideEasing: "linear",
  //     showMethod: "fadeIn",
  //     hideMethod: "fadeOut",
  //   };

  //   toastr.error("Sila Masukkan Fail 'LINE.GPKG' sahaja! 😒");
  // }


};

window.zoomTo = function (minX, minY, maxX, maxY, projection) {
  try {
    const sw = proj4(projection, 'EPSG:4326', [minX, minY]);
    const ne = proj4(projection, 'EPSG:4326', [maxX, maxY]);
    mapGis.fitBounds([
      [sw[1], sw[0]],
      [ne[1], ne[0]],
    ]);
  } catch (e) {
    mapGis.fitBounds([
      [minY, minX],
      [maxY, maxX],
    ]);
  }
};

window.toggleLayer = function (layerType, table) {
  if (tableLayers[table]) {
    mapGis.removeLayer(tableLayers[table]);
    delete tableLayers[table];
    delete featureLayers[table];
    return;
  }

  if (layerType === 'feature') {
    ga('send', {
      hitType: 'event',
      eventCategory: 'Layer',
      eventAction: 'load',
      eventLabel: 'Feature Layer',
    });

    geoPackage.indexFeatureTable(table).then(function () {
      const tableLayer = new L.GridLayer({
        noWrap: true,
        pane: 'overlayPane'
      });
      const featureDao = geoPackage.getFeatureDao(table);
      const ft = new window.GeoPackage.FeatureTiles(featureDao, 256, 256);
      ft.maxFeaturesPerTile = 10000;
      ft.maxFeaturesTileDraw = new window.GeoPackage.NumberFeaturesTile();
      tableLayer.createTile = function (tilePoint, done) {
        const canvas = L.DomUtil.create('canvas', 'leaflet-tile');
        canvas.width = 256;
        canvas.height = 256;
        if (!featureDao) return;
        ft.drawTile(tilePoint.x, tilePoint.y, tilePoint.z, canvas).then(() => {
          done(null, canvas);
        });

        return canvas;
      };
      mapGis.addLayer(tableLayer);
      tableLayers[table] = tableLayer;
      featureLayers[table] = tableLayer;
    });

  }
};

function addRowToLayer(iterator, row, featureDao, srs, layer) {
  return new Promise(function (resolve) {
    setTimeout(function () {
      const currentRow = featureDao.getFeatureRow(row);
      const json = GeoPackage.parseFeatureRowIntoGeoJSON(currentRow, srs);
      layer.addData(json);
      resolve(json);
    });
  }).then(function () {
    const nextRow = iterator.next();
    if (!nextRow.done) {
      return addRowToLayer(iterator, nextRow.value, featureDao, srs, layer);
    }
  });
}

window.zoomToFeature = function (featureId, tableName) {
  window.toggleFeature(featureId, tableName, true, true);
};

// TODO: define functions for coordinate listing
// function to calculate distance between 2 coordinates
function calculateDistance(coord1, coord2) {
  const x1 = coord1[0];
  const y1 = coord1[1];
  const x2 = coord2[0];
  const y2 = coord2[1];

  const dx = x2 - x1;
  const dy = y2 - y1;

  return Math.sqrt(dx * dx + dy * dy);
}

// function to get first and last array value
function getFirstAndLast(array) {
  if (array.length === 0) {
    return null; // Return null for an empty array
  }

  const first = array[0];
  const last = array[array.length - 1];

  return [first, last];
}

// function to sort feature list by distance to starting coordinate
function sortObjectsByDistance(objects, startingCoordinate) {
  // Find the object with the closest coorStrEnd to the startingCoordinate.
  let closestObject = null;
  let shortestDistance = Infinity;
  let startIsCoorStrEnd0 = false; // Indicates if coorStrEnd[0] is the starting coordinate.

  for (const object of objects) {
    const distance0 = calculateDistance(
      object.coorStrEnd[0],
      startingCoordinate
    );
    const distance1 = calculateDistance(
      object.coorStrEnd[1],
      startingCoordinate
    );

    if (distance0 < shortestDistance) {
      shortestDistance = distance0;
      closestObject = object;
      startIsCoorStrEnd0 = true;
    }

    if (distance1 < shortestDistance) {
      shortestDistance = distance1;
      closestObject = object;
      startIsCoorStrEnd0 = false;
    }
  }

  if (!closestObject) {
    // Handle the case when no object is found.
    return [];
  }

  let sequence = [closestObject]; // Store the sequence of objects.
  let remainingObjects = objects.filter(obj => obj !== closestObject); // Remove the closest object.

  // Continue building the sequence.
  while (remainingObjects.length > 0) {
    let nextObject = null;
    shortestDistance = Infinity;
    const lastCoordinate = startIsCoorStrEnd0
      ? sequence[sequence.length - 1].coorStrEnd[0]
      : sequence[sequence.length - 1].coorStrEnd[1];

    // Calculate the distance from the last coordinate to the start of all remaining objects.
    for (const obj of remainingObjects) {
      const nextCoordinate = startIsCoorStrEnd0
        ? obj.coorStrEnd[0]
        : obj.coorStrEnd[1];

      const distance = calculateDistance(lastCoordinate, nextCoordinate);

      if (distance < shortestDistance) {
        shortestDistance = distance;
        nextObject = obj;
      }
    }

    if (!nextObject) {
      // Handle the case when no next object is found.
      break;
    }

    // Determine if the next start coordinate should be coorStrEnd[0] or coorStrEnd[1].
    const nextCoordinate0 = calculateDistance(
      nextObject.coorStrEnd[0],
      lastCoordinate
    );
    const nextCoordinate1 = calculateDistance(
      nextObject.coorStrEnd[1],
      lastCoordinate
    );

    if (nextCoordinate0 < nextCoordinate1) {
      startIsCoorStrEnd0 = true;
    } else {
      startIsCoorStrEnd0 = false;
    }

    // Remove the next object from the remainingObjects array.
    remainingObjects = remainingObjects.filter(obj => obj !== nextObject);
    sequence.push(nextObject);
  }

  return sequence;
}

// Class definition
var GISExtract = (function () {
  // Elements
  let form;
  let formModal;
  let routeListRepeaterEl;
  let validator
  let submitButton;

  var extractSubmit = function () {
    // Function to send data
    let sendData = function (data) {
      console.log({ savingData: data });
      // Your asynchronous code goes here
      api.post('geospatial/conversion', data).then(response => {
        console.log({ responseData: response });
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");

        // Enable button
        submitButton.disabled = false;

        if (response.message === "success") {
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

          toastr.success("Serahan Data Surihan Laluan Permohonan Berjaya! 🥳");

          setTimeout(function () {
            //form.submit(); // submit form
            location.href = "geospatial/general/tasks/new";
          }, 2500);
        }
      }).catch(error => {
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");

        // Enable button
        submitButton.disabled = false;

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

        toastr.error("Maaf, Terdapat ralat di bahagian sistem. Sila cuba sekali lagi");
      })

    };

    // Alert setup
    let lessWarning = {
      html: `Jumlah jarak pada senarai laluan kurang daripada jumlah jarak pada pelan. Anda pasti untuk teruskan?`,
      icon: "error",
      buttonsStyling: false,
      showCancelButton: true,
      confirmButtonText: "Ya, Teruskan!",
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: "btn btn-secondary",
        cancelButton: 'btn btn-primary'
      }
    };
    let exceedWarning = {
      html: `Jumlah jarak pada senarai laluan melebihi jumlah jarak pada pelan. Anda pasti untuk teruskan?`,
      icon: "error",
      buttonsStyling: false,
      showCancelButton: true,
      confirmButtonText: "Ya, Teruskan!",
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: "btn btn-secondary",
        cancelButton: 'btn btn-primary'
      }
    };

    // Function to fire sweetalert with radio
    let selectValidLength = function () {
      return new Promise(async (resolve) => {
        const inputOptions = new Promise((resolve) => {
          setTimeout(() => {
            resolve({
              'gpkg-length': 'GPKG',
              // 'route-length-total': 'Butiran Laluan'
            });
          }, 100);
        });

        const { value: selection } = await Swal.fire({
          title: 'Pilih Satu Jarak',
          html: `Anda dikehendaki untuk memilih jarak yang akan digunakan bagi meneruskan proses kendalian projek walaupun kedua-dua butiran jarak yang disediakan akan disimpan.`,
          input: 'radio',
          inputOptions: await inputOptions,
          allowOutsideClick: false,
          showCancelButton: true,
          confirmButtonText: "Pilih, Teruskan!",
          cancelButtonText: 'Batal',
          customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: 'btn btn-secondary'
          },
          inputValidator: (value) => {
            if (!value) {
              return 'Anda perlu pilih salah satu diantara 2 jarak yang anda sediakan!';
            }
          }
        });

        if (selection) {
          resolve(selection);
        } else {
          resolve(false);
        }
      });
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
            const formData = new FormData(form);

            // Iterate through formData entries and append them to formModal
            for (const [key, value] of formData.entries()) {
              formModalData.append(key, value);
            }

            const data = formDataToJson(formModalData);
            data.action = "route-list";
            let proceedAction = true;
            // check if total length is equal to plan upload or not
            // if (data['route-length-total'] > data['gpkg-length']) {
            //   proceedAction = false;
            //   Swal.fire(exceedWarning).then((result) => {
            //     if (result.isConfirmed) {
            //       proceedAction = true;
            //       selectValidLength().then((selectedOption) => {
            //         if (selectedOption){
            //           data.selectedLength = selectedOption;
            //           sendData(data);
            //         } else {
            //           // Hide loading indication
            //           submitButton.removeAttribute("data-kt-indicator");

            //           // Enable button
            //           submitButton.disabled = false;
            //         }
            //       });
            //     } else {
            //       // Hide loading indication
            //       submitButton.removeAttribute("data-kt-indicator");

            //       // Enable button
            //       submitButton.disabled = false;
            //     }
            //   });
            // } else if (data['route-length-total'] < data['gpkg-length']) {
            //   proceedAction = false;
            //   Swal.fire(lessWarning).then((result) => {
            //     if (result.isConfirmed) {
            //       proceedAction = true;
            //       selectValidLength().then((selectedOption) => {
            //         if (selectedOption){
            //           data.selectedLength = selectedOption;
            //           sendData(data);
            //         } else {
            //           // Hide loading indication
            //           submitButton.removeAttribute("data-kt-indicator");

            //           // Enable button
            //           submitButton.disabled = false;
            //         }
            //       });
            //     } else {
            //       // Hide loading indication
            //       submitButton.removeAttribute("data-kt-indicator");

            //       // Enable button
            //       submitButton.disabled = false;
            //     }
            //   });
            // } else {
              proceedAction = true;
              sendData(data);
            // }
          } else {
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

      // validator.addField(namePrefix + "[method]", {
      //   validators: {
      //     notEmpty: {
      //       message: 'Sila pilih kaedah terlibat.'
      //     }
      //   }
      // });

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

      // validator.addField(namePrefix + "[density]", {
      //   validators: {
      //     notEmpty: {
      //       message: 'Sila pilih kepadatan bagi kawasan.'
      //     }
      //   }
      // });

      // validator.addField(namePrefix + "[route-length]", {
      //   validators: {
      //     notEmpty: {
      //       message: 'Sila masukkan jarak dalam meter.'
      //     }
      //   }
      // });

      // TODO: commented for now
      // console.log({ allFields: validator.getFields() });

    //   $("#authority").select2().on('change.select2', function () {
    //     // Revalidate the color field when an option is chosen
    //     validator.revalidateField(namePrefix + "[authority]");
    // });
    // $("#district").select2().on('change.select2', function () {
    //     // Revalidate the color field when an option is chosen
    //     validator.revalidateField(namePrefix + "[district]");
    // });


    };


    // const removeFields = function (length) {
    //   // remove all available fields
    //   const currentFields = validator.getFields();
    //   for (const key in currentFields) {
    //     if (currentFields.hasOwnProperty(key)) {
    //       validator.removeField(key);
    //     }
    //   }
    //   // adding back the fields for left repeater item
    //   for (let i = 0; i < length; i++) {
    //     // Your code here
    //     addFields(i);
    //   }

    //   console.log({ allFields: validator.getFields() });
    // }
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
        // $(this).find('#authority').select2({
        //   dropdownParent: routeModalEl.querySelector('.modal-content'),
        //   selectOnClose: true
        // });
        // $(this).find('#district').select2({
        //   dropdownParent: routeModalEl.querySelector('.modal-content'),
        //   selectOnClose: true
        // });

        // Re-init tooltip
        let tooltipEl = $(this).find('[data-bs-toggle="tooltip"]');
        tooltipEl.tooltip(); // Initialize Bootstrap tooltip

        // let lengthInput = $(this).find('[data-route-list="length"]');
        // let moveUpButton = $(this).find('[data-route-list="move-up"]');
        // let moveDownButton = $(this).find('[data-route-list="move-down"]');
        // let coorSwapButton = $(this).find('[data-route-list="coor-swap"]');
        let coorStartInput = $(this).find('[data-route-list="coor-start"]');
        let coorEndInput = $(this).find('[data-route-list="coor-end"]');


        // add listener to control the input inserted into
        // lengthInput[0].addEventListener("input", function (event) {
        //   const inputValue = event.target.value;

        //   // Use a regular expression to remove non-numeric characters
        //   const numericValue = inputValue.replace(/[^0-9]/g, '');

        //   // Update the input value with the cleaned numeric value
        //   event.target.value = numericValue;

        //   let lengthInsertToUpdate = 0;
        //   publicRepeaterVar.repeaterVal().gis_route_list.forEach(function (variable) {

        //     if (variable['route-length'] != '') {
        //       lengthInsertToUpdate = lengthInsertToUpdate + parseFloat(variable['route-length'])
        //     };

        //   });
        //   updateInputValue(totalLengthInputEl, lengthInsertToUpdate);
        // });

        // add listener for the move button
        // moveUpButton[0].addEventListener("click", function (event) {
        //   // move the repeater item
        //   let item = $(this).closest("[data-repeater-item]");
        //   item.insertBefore(item.prev());

        //   // dispatch reorder event
        //   document.dispatchEvent(repeaterReorder);
        // });

        // moveDownButton[0].addEventListener("click", function (event) {
        //   // move the repeater item
        //   let item = $(this).closest("[data-repeater-item]");
        //   item.insertAfter(item.next());

        //   // dispatch reorder event
        //   document.dispatchEvent(repeaterReorder);
        // });

        // add listener for the swap button
        // coorSwapButton[0].addEventListener("click", function (event) {
        //   let tempStart = coorStartInput.val();
        //   let tempEnd = coorEndInput.val();
        //   coorStartInput.val(tempEnd);
        //   coorEndInput.val(tempStart);
        // })

        // let lengthToUpdate = parseFloat(totalLengthInputEl.value) + 0;
        // if (lengthInput.val() != '') {
        //   lengthToUpdate = (lengthToUpdate + parseFloat(lengthInput.val()));
        // }
        // updateInputValue(totalLengthInputEl, lengthToUpdate);

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

        // let lengthInput = currentElement.closest("[data-repeater-item]").find('[data-route-list="length"]');
        // let lengthToUpdate = parseFloat(totalLengthInputEl.value) - 0;
        // if (lengthInput.val() != '') {
        //   lengthToUpdate = (lengthToUpdate - parseFloat(lengthInput.val()));
        // }
        // updateInputValue(totalLengthInputEl, lengthToUpdate);
        currentElement.slideUp(function () {
          deleteElement();

          // Get the index of the deleted element within its container
          // const repeaterLength = currentElement.closest("[data-repeater-item]").parent().find('[data-repeater-item]').length;
          // const repeaterLength = $(routeListRepeaterEl).find('[data-repeater-item]').length;

          // Call removeFields to remove the validation for the hidden item
          // removeFields(repeaterLength);
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

          // reset validation
          // const repeaterLength = $(routeListRepeaterEl).find('[data-repeater-item]').length;

          // Call removeFields to remove the validation for the hidden item
          // removeFields(repeaterLength);
        });

        addFields(index);
        select2El.select2().on('change.select2', function () {
            // Revalidate the color field when an option is chosen
            validator.revalidateField("gis_route_list[" + index + "]" + "[authority]");
            validator.revalidateField("gis_route_list[" + index + "]" + "[district]");
        });

      //   $("#authority").select2().on('change.select2', function () {
      //     // Revalidate the color field when an option is chosen
      //     validator.revalidateField(namePrefix + "[authority]");
      // });
      // $("#district").select2().on('change.select2', function () {
      //     // Revalidate the color field when an option is chosen
      //     validator.revalidateField(namePrefix + "[district]");
      // });
      }
    });

    // assign private repeater to public scope variable
    publicRepeaterVar = privateRepeaterVar;
  };

  $('#gis-plan-route-modal').on('shown.bs.modal', function () {


    initMapRoadView();


  });

  // var mapInitialized = false;

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
        label: 'Google Roadmap',
        minZoom: 0,
        maxZoom: 21
      });

      mapView.addControl(
        L.control.basemaps({
          basemaps: [streetView, satelliteView],
          tileX: 0,
          tileY: 0,
          tileZ: 1,
          position : 'topright',
        }),
      );
    }


    var defaultIcon = L.icon({
      iconUrl: '/assets/plugins/custom/leaflet/images/marker-icon.png',
      shadowUrl: '/assets/plugins/custom/leaflet/images/marker-shadow.png',
      iconAnchor: [12.5, 41], // Adjust these values based on your icon's dimensions
      // iconSize: [25, 41] // Actual size of the icon in pixels
  });

    function addMarker(coordinates, inputToUpdate) {
      var marker = L.marker([coordinates[1], coordinates[0]], {draggable: true, icon: defaultIcon}).addTo(mapView);
      marker.on('dragend', function() {
        var newLatLng = marker.getLatLng();
        inputToUpdate.value = `${newLatLng.lat.toFixed(7)}, ${newLatLng.lng.toFixed(7)}`;
      });
      markers.push(marker);
    }

    function removeMarkers() {
      markers.forEach(marker => mapView.removeLayer(marker));
      markers = [];
    }

    async function processRepeaterItems() {
      var bounds = L.latLngBounds();
      removeMarkers();
      var repeaterItems = routeListRepeaterEl.querySelectorAll("[data-repeater-item]");
      repeaterItems.forEach(item => {
        var startInput = item.querySelector('[name$="[coor_start]"]');
        var endInput = item.querySelector('[name$="[coor_end]"]');
        var roadInput = item.querySelector('[name$="[road_name]"]');

        if (startInput && endInput && startInput.value && endInput.value) {
          var startValues = startInput.value.split(",").map(Number);
          var endValues = endInput.value.split(",").map(Number);

          addMarker([startValues[1], startValues[0]], startInput);
          addMarker([endValues[1], endValues[0]], endInput);

          bounds.extend([startValues[0], startValues[1]]);
          bounds.extend([endValues[0], endValues[1]]);
        }
      });

      if (bounds.isValid()) {
        mapView.flyToBounds(bounds, {
          padding: [50, 50],
          duration: 3
        });
      }
    }

    routeListRepeaterEl.addEventListener("input", function () {
      processRepeaterItems();
    });

    $("#gis_route_repeater").on("click", "[data-repeater-delete]", function () {
      setTimeout(function () {
        processRepeaterItems();
      }, 300);
    });

    setTimeout(processRepeaterItems, 500);
  };

  // Public functions
  return {
    // Initialization
    init: function () {
      submitButton = document.querySelector("#submit-gis-plan");
      routeListRepeaterEl = routeModalEl.querySelector('#gis_route_repeater');
      formModal = document.querySelector("#form-gis-plan-modal");
      form = document.querySelector("#form-gis-plan");
      // totalLengthInputEl = document.querySelector('#route-length-total');

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


      extractSubmit();
      routeListInit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  GISExtract.init();
});
