var prints = (function () {
  const urlParams = new URLSearchParams(window.location.search);
  const reportNo = urlParams.get("r");
  console.log(apps);

  // let currentURL = new URL(window.location.href);
  // let urlWithOutParams = currentURL.origin + currentURL.pathname;
  let systemId = sysId;
  let reportData = currentReports;
  let a = authValue;
  // if (urlWithOutParams.includes("prints")) {
  //   let htaccessUrlParams = urlWithOutParams
  //   .replace(apps + "/projects/prints/LLT", "")
  //   .split("/");
  //   // urlParams[0] is ignore as it handle the first / in the trimmed url
  //   systemId = htaccessUrlParams[1];
  // } else if (urlWithOutParams.includes("view")) {
  //   let htaccessUrlParams = urlWithOutParams
  //   .replace(apps + "/projects/view/sitevisit", "")
  //   .split("/");
  //   // urlParams[0] is ignore as it handle the first / in the trimmed url
  //   systemId = htaccessUrlParams[1];
  // }

  $(document).ready(function () {
    const A4Height = 842; // in pixels

    const $mainSection = $('.main-section');
    $mainSection.each(function () {
      let $currentSection = $(this);
      let currentSectionHeight = 0;
      let lltChild = null;
      let lltChildGap = 0;

      $(this).children().each(function () {
        lltChildGap++;
        const $child = $(this);
        const childHeight = $child.outerHeight();

        // check if current child is section header should be follow each page setup
        if ($child.data('section-title') === 'LLT') {
          lltChild = $child;
          lltChildGap = 0;
        }

        if (currentSectionHeight + childHeight > A4Height && lltChild != $child) {
          const $newSection = $('<section class="sheet padding-15mm"></section>');
          $currentSection.after($newSection);
          $currentSection = $newSection;
          currentSectionHeight = 0;
        }

        if (lltChild != $child) {
          if (lltChildGap == 1) {
            $currentSection.append(lltChild);
          }
        $currentSection.append($child);
        currentSectionHeight += childHeight;
        }
      });
    })
  });

  function mapSetup() {
    let mapData = reportData;

    if (a) {
      mapData.forEach(function (mapReportData) {
        if (mapReportData.authority_id == a) {
          // setup variable for uiblock checking
          let uiBlockTotal = { marker: null, polygon: null, polyline: null };
          let uiBlockCount = { marker: 0, polygon: 0, polyline: 0 };

          let mapId = "map-" + mapReportData.authority_id;

          // Define Leaflet map
          var map = L.map(mapId, {
            zoomControl: false,
            scrollWheelZoom: false, // Disable the default zoom control buttons
            dragging: false, //Diable the movepan
            attributionControl: false,
            doubleClickZoom: false,
          }).setView([3.8358838977761702, 103.29957854588842], 16);

          var tiles = [
            "https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibXJoNHMiLCJhIjoiY2poeDh3bmtoMDk5ZTNrcDhkaDBnamFiMiJ9.G-NU8f4IrxF2vr7X43lE0w",
            "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
            // Add more tile URLs here as needed
          ];

          var tileIndex = 0; // Variable to keep track of the current tile index

          // Create a temporary tile layer with the first tile URL
          var tempTileLayer = L.tileLayer(tiles[tileIndex], {
            maxZoom: 21,
            zoomControl: false,
          });

          // Attach the 'tileerror' event to the temporary tile layer
          tempTileLayer.on("tileerror", function (error, tile) {
            console.log("Tile error:", error, tile);

            // Move to the next tile URL
            tileIndex++;
            if (tileIndex < tiles.length) {
              // If there are more tiles, replace the tile layer with the next tile URL
              tempTileLayer.setUrl(tiles[tileIndex]);
            } else {
              // If there are no more tiles, handle the error as desired
              console.log("No more tiles to try.");
            }
          });

          // Add the temporary tile layer to the map
          tempTileLayer.addTo(map);

          /* Creating a new icon object called warningIcon. */
          var warningIcon = L.icon({
            iconUrl: "assets/media/maps/warning-marker.svg",
            shadowUrl: "assets/media/maps/null.png", // Disable shadow
          });

          /* Creating a variable called primaryIcon and assigning it a value of L.icon. */
          var primaryIcon = L.icon({
            iconUrl: "assets/media/maps/primary-marker.svg",
            shadowUrl: "assets/media/maps/null.png", // Disable shadow
          });

          var successIcon = L.icon({
            iconUrl: "assets/media/maps/success-marker.svg",
            shadowUrl: "assets/media/maps/null.png", // Disable shadow
          });

          var infoIcon = L.icon({
            iconUrl: "assets/media/maps/info-marker.svg",
            shadowUrl: "assets/media/maps/null.png", // Disable shadow
          });

          var dangerIcon = L.icon({
            iconUrl: "assets/media/maps/danger-marker.svg",
            shadowUrl: "assets/media/maps/null.png", // Disable shadow
          });

          //LAYER REQUEST
          var chartingLine = L.Geoserver.wfs("https://map.asiadebut.tech/geoserver/wfs", {
            pmIgnore: true,
            layers: geoserverLayer,
            style: function (f) {
              switch (f.properties.method) {
                case "CW":
                  return {
                    color: "#057af6",
                  };
                case "GI":
                  return {
                    color: "#ec7800",
                  };
                case "HDD":
                  return {
                    color: "#f31869",
                  };
                case "PJ":
                  return {
                    color: "#f2dd08",
                  };
                case "GV":
                  return {
                    color: "#00db3e",
                  };
                default:
                  return {
                    color: "#717071",
                  };
              }
            },
            onEachFeature: function (f, l) {
              let color;
              switch (f.properties.method) {
                case "HDD":
                  color = "danger";
                  break;
                case "CW":
                  color = "primary";
                  break;
                case "GV":
                  color = "success";
                  break;
                case "GI":
                  color = "warning";
                  break;
                case "OH":
                  color = "dark";
                  break;
                default:
                  color = "secondary";
              }
            },
            CQL_FILTER: "system_id='" + systemId + "' AND revision = 0",
          });
          chartingLine.addTo(map);

          //MAP CONFIRGURATION
          map.on("zoomend", function () {
            currentZoom = map.getZoom();
            if (currentZoom > 16) {
              chartingLine.setStyle({
                weight: 5,
                dashArray: "20",
              });
            } else if (currentZoom == 16) {
              chartingLine.setStyle({
                weight: 4,
                dashArray: "15",
              });
            } else if (currentZoom < 16) {
              chartingLine.setStyle({
                weight: 3,
                dashArray: "10",
              });
            } else {
              chartingLine.setStyle({
                weight: 2,
                dashArray: "5",
              });
            }
          });

          // TODO: Begin::Do event fetch api to update saved site visit data
          // Marker
          api
            .get(
              `reports/geometry/publicGeom?action=marker&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
            )
            .then((response) => {
              console.log({ "marker-init": response });
              uiBlockTotal.marker = response.markers.length;
              if (
                uiBlockTotal.marker == 0 &&
                uiBlockTotal.polygon == 0 &&
                uiBlockTotal.polyline == 0
              ) {
                // initLoadBlock.release();
              }
              // var marker;
              response.markers.forEach((element) => {
                // create marker
                var marker = L.marker([element.latitude, element.longitude]).addTo(
                  map
                );

                // Do event fetch api to update unique _leaflet_id to the db
                let payload = {
                  action: "update-geom-init",
                  init: "marker",
                  systemId: systemId,
                  reportId: reportNo,
                  authId: mapReportData.authority_id,
                  id: element.id,
                  geomId: element.geom_id,
                  newGeomId: marker._leaflet_id,
                };
                api
                  .put(`reports/geometry/publicGeom`, payload)
                  .then((response) => {
                    // console.log(response.data);
                    console.log({ "marker-each-update-geom": response });

                    // initializing check
                    uiBlockCount.marker++;
                    if (
                      uiBlockTotal.marker -
                      uiBlockCount.marker +
                      (uiBlockTotal.polygon - uiBlockCount.polygon) +
                      (uiBlockTotal.polyline - uiBlockCount.polyline) ==
                      0
                    ) {
                      // initLoadBlock.release();
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });

                console.log({ "marker-each-instance": marker });

                // Create a unique ID for the modal using the marker's L.stamp property
                var modalId = "marker-" + marker._leaflet_id;

                // Create the modal HTML using the unique ID
                var modalHtml =
                  `
          <div class="modal fade" data-bs-backdrop="static" id="` +
                  modalId +
                  `" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                      <form id="form-` +
                  modalId +
                  `">
                          <div class="modal-header">
                              <div class="dropzone">
                                  <!--begin::Message-->
                                  <div class="dz-message needsclick">
                                      <!--begin::Icon-->
                                      <span class="fa-stack">
                                          <i class="fad fa-image fa-stack-1x"
                                          style="font-size:9rem; position: absolute; left:70px; line-height: 250px;" ></i>
                                          <i class="fad fa-expand fa-stack-2x"
                                          style="font-size:20rem; position: absolute; left:15px; line-height:250px;"></i>
                                      </span>
                                      <!--end::Icon-->
                                  </div>
                              </div>
                          </div>
                          <input type="text" id="rowId-` +
                  modalId +
                  `" hidden />
                          <input type="text" id="action-` +
                  modalId +
                  `" value="add" hidden />
                          <div class="modal-body">
                              <div class="mb-0 form-floating fv-row">
                                  <textarea  data-kt-autosize="true" rows="4" class="form-control form-control-flush" placeholder="Nyatakan Keterangan"
                                  id="desc-` +
                  modalId +
                  `" name="description">` +
                  element.description +
                  `</textarea>
                                  <label for="desc-` +
                  modalId +
                  `" class="form-label">Keterangan</label>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>`;

                // Append the modal HTML to the body
                $("body").append(modalHtml);

                // Show the modal when the marker is created
                // $('#' + modalId).modal('show');

                // Set the marker ID as the modal's data attribute
                $("#" + modalId).data("markerId", marker._leaflet_id);

                // Initialize Dropzone for the modal
                const latlng = marker.getLatLng();

                // TODO: request to geom/upload.php
                let dropzone = new Dropzone($("#" + modalId).find(".dropzone")[0], {
                  url: apps + "/api/reports/geometry/upload",
                  paramName: "file",
                  maxFiles: 1,
                  acceptedFiles: "image/*",
                  thumbnailWidth: 600,
                  thumbnailHeight: 600,
                  maxFilesize: 1024,
                  autoProcessQueue: false,
                  addRemoveLinks: true,
                  sending: function (file, xhr, formData) {
                    formData.append("systemId", sid);
                    formData.append("reportId", ref);
                    formData.append("authId", auth);
                    formData.append("markerId", marker._leaflet_id);
                    formData.append("action", $("#action-" + modalId).val());
                    formData.append("id", $("#rowId-" + modalId).val());
                  },
                  accept: function (file, done) {
                    done();
                  },
                });

                console.log({ "marker-init-dropzone-var": dropzone });

                let markerRowId;
                markerRowId = element.id;
                $("#rowId-" + modalId).val(markerRowId);

                // Get the marker ID from the modal's data attribute
                var markerId = $("#" + modalId).data("markerId");

                // Find the marker with the given ID
                var marker = map._layers[markerId];

                // Get the input values from the modal
                var description = element.description;

                // Get current timestamp to be patch with image url
                var currentImgDate = new Date();
                var currentImgTimestamp = currentImgDate.getTime();
                var image = atob(element.url) + "?t=" + currentImgTimestamp;

                $("#img-" + modalId).val(image);
                $("#action-" + modalId).val("edit");
                $("#cancel-" + modalId).removeClass("d-none");
                $("#delete-" + modalId).addClass("d-none");
                $("#save-" + modalId).addClass("d-none");
                $("#update-" + modalId).removeClass("d-none");

                // Set the marker's popup content
                marker.bindPopup(
                  `
              <div class="d-flex flex-column">
                  <img src="` +
                  image +
                  `" class="report-image" />
                  <h4 class="fw-semibold">` +
                  description +
                  `</h4>
              </div>
              `,
                  {
                    minWidth: 400,
                    closeButton: false,
                  }
                );

                // Hide loading indication
                $("#save-" + modalId).removeAttr("data-kt-indicator");

                // Enable button
                $("#save-" + modalId).prop("disabled", false);

                // Hide the modal
                // $('#' + modalId).modal('hide');
                marker.setIcon(primaryIcon);
                map.pm.disableDraw();

                // Handle the "Cancel" button click for edit mode
                $("#cancel-" + modalId).click(function () {
                  // Hide the modal
                  $("#" + modalId).modal("hide");
                });

                // Handle the "Update" button click
                $("#update-" + modalId).click(function () {
                  // TODO: api request to geom/marker.php
                  let payload = {
                    systemId: systemId,
                    reportId: reportNo,
                    authId: mapReportData.authority_id,
                    description: $("#desc-" + modalId).val(),
                    markerId: marker._leaflet_id,
                    latitude: latlng.lat,
                    longitude: latlng.lng,
                    action: $("#action-" + modalId).val(),
                    url: $("#img-" + modalId).attr("src"),
                    id: markerRowId,
                  };
                  api
                    .put(`reports/geometry/marker`, payload)
                    .then((response) => {
                      var queuedFiles = dropzone.getQueuedFiles();
                      if (queuedFiles.length > 0) {
                        dropzone.processQueue();
                        dropzone.on("success", function (file, result) {
                          // Handle success after uploading all files
                          if (
                            dropzone.getQueuedFiles().length === 0 &&
                            dropzone.getUploadingFiles().length === 0
                          ) {
                            handleSuccess(result);
                          }
                        });
                      } else {
                        handleSuccess(response);
                      }
                    })
                    .catch((error) => {
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

                  function handleSuccess(response) {
                    toastr.options = optionToast;
                    toastr.success("Pin Lokasi Berjaya Dikemaskini!");

                    // Get the marker ID from the modal's data attribute
                    var markerId = $("#" + modalId).data("markerId");

                    // Find the marker with the given ID
                    var marker = map._layers[markerId];

                    // Get the input values from the modal
                    var description = $("#desc-" + modalId).val();

                    // Get current timestamp to be patch with image url
                    var currentImgDate = new Date();
                    var currentImgTimestamp = currentImgDate.getTime();
                    var image = response.data.img + "?t=" + currentImgTimestamp;

                    // Set the marker's popup content (on update success)
                    marker
                      .bindPopup(
                        `
                  <div class="d-flex flex-end">
                  </div>
                  <div class="d-flex flex-column">
                  <img src="` +
                        image +
                        `" class="report-image"/>
                  <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` +
                        description +
                        `</h4>
                  </div>
              `,
                        {
                          minWidth: 400,
                          closeButton: false,
                        }
                      )
                      .openPopup();

                    // Hide the modal
                    $("#" + modalId).modal("hide");
                    marker.setIcon(warningIcon);
                  }
                });

                // Add a listener for the "pm:edit" event
                marker.on("pm:edit", function (event) {
                  // alert('edit event for marker');
                  // Get the new marker position
                  var newLatLng = event.layer.getLatLng();
                  let payload = {
                    id: markerRowId,
                    systemId: systemId,
                    authId: mapReportData.authority_id,
                    markerId: marker._leaflet_id,
                    latitude: newLatLng.lat,
                    longitude: newLatLng.lng,
                    reportId: reportNo,
                  };

                  // TODO: api request to geom/marker.php
                  api
                    .put(`reports/geometry/marker`, payload)
                    .then((response) => {
                      toastr.options = optionToast;
                      // Show a success message to the user
                      toastr.success("Lokasi Pin Telah Berubah!");
                      marker.setIcon(infoIcon);
                    })
                    .catch((error) => {
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
                });

                marker.on("pm:remove", function () {
                  // TODO: api request to geom/marker.php
                  api
                    .delete(
                      `reports/geometry/marker?id=${markerRowId}&markerId=${marker._leaflet_id}&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
                    )
                    .then((response) => {
                      toastr.options = optionToast;
                      // Show a success message to the user
                      toastr.error("Pin bergambar Telah dibuang!");
                    })
                    .catch((error) => {
                      console.log(error);
                    });
                });
              });
            })
            .catch((error) => {
              console.log(error);
            });

          // TODO: Polygon
          api
            .get(
              `reports/geometry/publicGeom?action=polygon&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
            )
            .then((response) => {
              console.log({ "polygon-init": response });
              uiBlockTotal.polygon = response.polygon.length;
              if (
                uiBlockTotal.marker == 0 &&
                uiBlockTotal.polygon == 0 &&
                uiBlockTotal.polyline == 0
              ) {
                // initLoadBlock.release();
              }
              response.polygon.forEach((element) => {
                var dbPolygon = JSON.parse(element.geometry).coordinates[0].map(
                  function (coord) {
                    return [coord[1], coord[0]];
                  }
                );
                console.log({ "polygon-each": dbPolygon });
                var polygon = L.polygon(dbPolygon, { color: "blue" }).addTo(map);
                console.log({ "polygon-each-instance": polygon });

                // Do event fetch api to update unique _leaflet_id to the db
                let payload = {
                  action: "update-geom-init",
                  sid: systemId,
                  ref: reportNo,
                  auth: mapReportData.authority_id,
                  id: element.id,
                  geomId: element.geom_id,
                  newGeomId: polygon._leaflet_id,
                };
                api
                  .put(`reports/geometry/polygon`, payload)
                  .then((response) => {
                    // console.log(response.data);
                    console.log({ "polygon-each-update-geom": response });

                    // initializing check
                    uiBlockCount.polygon++;
                    if (
                      uiBlockTotal.marker -
                      uiBlockCount.marker +
                      (uiBlockTotal.polygon - uiBlockCount.polygon) +
                      (uiBlockTotal.polyline - uiBlockCount.polyline) ==
                      0
                    ) {
                      // initLoadBlock.release();
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });

                var polygonModalId = "polygon-" + polygon._leaflet_id;
                let polygonRowId;

                polygon.setStyle({
                  color: "#3E97FF",
                });

                // Create the modal HTML using the unique ID
                var modalHtml =
                  `
          <div class="modal fade" data-bs-backdrop="static" id="` +
                  polygonModalId +
                  `" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                      <form>
                          <div class="modal-body mb-0 form-floating">
                              <div class="form-floating mb-5">
                                  <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                  id="notes-` +
                  polygonModalId +
                  `" style="height: 100px"></textarea>
                                  <label for="notes-` +
                  polygonModalId +
                  `" class="form-label">Catatan</label>
                              </div>
                          </div>
                          <input type="text" id="rowId-` +
                  element.id +
                  `" hidden/>
                          <input type="text" id="action-` +
                  polygonModalId +
                  `" value="edit" hidden/>
                      </form>
                  </div>
              </div>
          </div>`;

                // Append the modal HTML to the body
                $("body").append(modalHtml);

                // insert rowId retrieved from db
                polygonRowId = element.id;
                $("#rowId-" + polygonModalId).val(polygonRowId);

                // insert recorded description from db into modal input
                $("#notes-" + polygonModalId).val(element.description);

                // Show the modal when the marker is created
                // $('#' + polygonModalId).modal('show');

                // Set the marker ID as the modal's data attribute
                $("#" + polygonModalId).data("polygonId", polygon._leaflet_id);

                let geom = polygon.toGeoJSON();
                console.log({ "polygon-togeojson": geom });
                console.log({ "polygon-togeojson-sendToRequest": geom });
                // Handle the "Save" button click
                $("#save-" + polygonModalId).click(function () {
                  // TODO: api request to geom/polygon.php
                  let payload = {
                    sid: sid,
                    ref: ref,
                    auth: auth,
                    notes: $("#notes-" + polygonModalId).val(),
                    polygonId: polygon._leaflet_id,
                    geom: geom,
                    action: $("#action-" + polygonModalId).val(),
                    id: $("#rowId-" + polygonModalId).val(),
                  };
                  api
                    .post("reports/geometry/polygon", payload)
                    .then((response) => {
                      console.log({ "response.data.id": response.data.id });
                      polygonRowId = response.data.id;
                      $("#rowId-" + polygonModalId).val(polygonRowId);

                      toastr.options = optionToast;
                      toastr.success("Poligon Bernota Berjaya Disimpan!");

                      // Get the marker ID from the modal's data attribute
                      var polygonId = $("#" + polygonModalId).data("polygonId");

                      // Find the marker with the given ID
                      var polygon = map._layers[polygonId];

                      // Get the input values from the modal
                      var notes = $("#notes-" + polygonModalId).val();

                      $("#cancel-" + polygonModalId).removeClass("d-none");
                      $("#delete-" + polygonModalId).addClass("d-none");
                      $("#action-" + polygonModalId).val("edit");

                      // Set the marker's popup content
                      polygon
                        .bindPopup(
                          `
                          <div class="d-flex flex-column">
                              <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                          notes +
                          `</h5>
                          </div>
                          `,
                          {
                            minWidth: 350,
                            closeButton: false,
                          }
                        )
                        .openPopup();

                      // Hide the modal
                      $("#" + polygonModalId).modal("hide");
                      polygon.setStyle({
                        color: "#75CC68",
                      });
                    })
                    .catch((error) => {
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
                });

                // initialize bindpopup

                // Get the input values from the modal
                var notes = $("#notes-" + polygonModalId).val();

                $("#cancel-" + polygonModalId).removeClass("d-none");
                $("#delete-" + polygonModalId).addClass("d-none");
                $("#action-" + polygonModalId).val("edit");

                // Set the marker's popup content
                polygon.bindPopup(
                  `
              <div class="d-flex flex-column">
                  <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                  notes +
                  `</h5>
              </div>
              `,
                  {
                    minWidth: 350,
                    closeButton: false,
                  }
                );

                // Handle the "Cancel" button click for new markers
                $("#delete-" + polygonModalId).click(function () {
                  // Get the marker ID from the modal's data attribute
                  var polygonId = $("#" + polygonModalId).data("polygonId");

                  // Find the marker with the given ID and remove it from the map
                  if (polygonId) {
                    map.removeLayer(map._layers[polygonId]);
                  }

                  // Hide the modal
                  $("#" + polygonModalId).modal("hide");
                });

                // Add a listener for the "pm:edit" event
                polygon.on("pm:edit", function (event) {
                  // TODO: api request to geom/polygon.php
                  payload = {
                    id: element.id,
                    sid: sid,
                    auth: auth,
                    polygonId: polygon._leaflet_id,
                    geom: event.layer.toGeoJSON(),
                    ref: ref,
                  };
                  // Send an AJAX request to update the marker in the database
                  api
                    .put("reports/geometry/polygon", payload)
                    .then((response) => {
                      toastr.options = optionToast;
                      // Show a success message to the user
                      toastr.success("Poligon Telah Berubah!");
                      polygon.setStyle({
                        color: "#7239ea",
                      });
                    })
                    .catch((error) => {
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
                });

                polygon.on("pm:remove", function () {
                  // TODO: api request to geom/polygon.php
                  // Send an AJAX request to delete the marker from the database
                  api
                    .delete(
                      `reports/geometry/polygon?sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}&id=${element.id}&polygonId=${polygon._leaflet_id}`
                    )
                    .then((response) => {
                      toastr.options = optionToast;
                      // Show a success message to the user
                      toastr.error("Poligon Telah dibuang!");
                    })
                    .catch((error) => {
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
                });
              });
            })
            .catch((error) => {
              console.log(error);
            });

          // TODO: polyline
          api
            .get(
              `reports/geometry/publicGeom?action=polyline&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
            )
            .then((response) => {
              console.log({ "polyline-init": response });
              uiBlockTotal.polyline = response.polyline.length;
              if (
                uiBlockTotal.marker == 0 &&
                uiBlockTotal.polygon == 0 &&
                uiBlockTotal.polyline == 0
              ) {
                // initLoadBlock.release();
              }
              response.polyline.forEach((element) => {
                var dbLine = JSON.parse(element.geometry).coordinates.map(function (
                  coord
                ) {
                  return [coord[1], coord[0]];
                });
                console.log({ "polyline-each": dbLine });
                var polyline = L.polyline(dbLine, { color: "red" }).addTo(map);
                console.log({ "polyline-each-instance": polyline });

                // Do event fetch api to update unique _leaflet_id to the db
                let payload = {
                  action: "update-geom-init",
                  init: "polyline",
                  sid: systemId,
                  ref: reportNo,
                  auth: mapReportData.authority_id,
                  id: element.id,
                  geomId: element.geom_id,
                  newGeomId: polyline._leaflet_id,
                };
                api
                  .put("reports/geometry/publicGeom", payload)
                  .then((response) => {
                    // console.log(response.data);
                    console.log({ "polyline-each-update-geom": response });

                    // initializing check
                    uiBlockCount.polyline++;
                    if (
                      uiBlockTotal.marker -
                      uiBlockCount.marker +
                      (uiBlockTotal.polygon - uiBlockCount.polygon) +
                      (uiBlockTotal.polyline - uiBlockCount.polyline) ==
                      0
                    ) {
                      // initLoadBlock.release();
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });

                var polylineModalId = "line-" + polyline._leaflet_id;

                polyline.setStyle({
                  color: "#3E97FF",
                });

                // Create the modal HTML using the unique ID
                var modalHtml =
                  `
          <div class="modal fade" data-bs-backdrop="static" id="` +
                  polylineModalId +
                  `" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                      <form>
                          <div class="modal-body mb-0 form-floating">
                              <div class="form-floating mb-5">
                                  <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                  id="notes-` +
                  polylineModalId +
                  `" style="height: 100px"></textarea>
                                  <label for="notes-` +
                  polylineModalId +
                  `" class="form-label">Catatan</label>
                              </div>
                          </div>
                          <input type="text" id="rowId-` +
                  element.id +
                  `" hidden/>
                          <input type="text" id="action-` +
                  polylineModalId +
                  `" value="edit" hidden/>
                      </form>
                  </div>
              </div>
          </div>`;

                // Append the modal HTML to the body
                $("body").append(modalHtml);

                // insert recorded description from db into modal input
                $("#notes-" + polylineModalId).val(element.description);

                let polylineRowId;
                polylineRowId = element.id;
                $("#rowId-" + polylineModalId).val(polylineRowId);

                // Show the modal when the marker is created
                // $('#' + polylineModalId).modal('show');

                // Set the marker ID as the modal's data attribute
                $("#" + polylineModalId).data("lineId", polyline._leaflet_id);

                const geoJSON = polyline.toGeoJSON();
                let geom = geoJSON.geometry.coordinates;
                // Handle the "Save" button click
                $("#save-" + polylineModalId).click(function () {
                  // TODO: api request to geom/polyline.php
                  let payload = {
                    sid: sid,
                    ref: ref,
                    auth: auth,
                    notes: $("#notes-" + polylineModalId).val(),
                    polylineId: polyline._leaflet_id,
                    geom: geom,
                    action: $("#action-" + polylineModalId).val(),
                    id: element.id,
                  };
                  api
                    .post("reports/geometry/polyline", payload)
                    .then((response) => {
                      toastr.options = optionToast;
                      toastr.success("Lukisan Bernota Berjaya Disimpan!");

                      // Get the marker ID from the modal's data attribute
                      var polylineId = $("#" + polylineModalId).data("lineId");

                      // Find the marker with the given ID
                      var polyline = map._layers[polylineId];

                      // Get the input values from the modal
                      var notes = $("#notes-" + polylineModalId).val();

                      $("#cancel-" + polylineModalId).removeClass("d-none");
                      $("#delete-" + polylineModalId).addClass("d-none");
                      $("#action-" + polylineModalId).val("edit");

                      // Set the marker's popup content
                      polyline
                        .bindPopup(
                          `
                      <div class="d-flex flex-column">
                          <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                          notes +
                          `</h5>
                      </div>
                      `,
                          {
                            minWidth: 350,
                            closeButton: false,
                          }
                        )
                        .openPopup();

                      // Hide the modal
                      $("#" + polylineModalId).modal("hide");
                      polyline.setStyle({
                        color: "#75CC68",
                      });
                    })
                    .catch((error) => {
                      console.log(error);
                    });
                });

                // initiate bind popup

                // Get the marker ID from the modal's data attribute
                var polylineId = $("#" + polylineModalId).data("lineId");

                // Find the marker with the given ID
                var polyline = map._layers[polylineId];

                // Get the input values from the modal
                var notes = $("#notes-" + polylineModalId).val();

                $("#cancel-" + polylineModalId).removeClass("d-none");
                $("#delete-" + polylineModalId).addClass("d-none");
                $("#action-" + polylineModalId).val("edit");

                // Set the marker's popup content
                polyline.bindPopup(
                  `
              <div class="d-flex flex-column">
                  <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                  notes +
                  `</h5>
              </div>
              `,
                  {
                    minWidth: 350,
                    closeButton: false,
                  }
                );

                // Handle the "Cancel" button click for new markers
                $("#delete-" + polylineModalId).click(function () {
                  // Get the marker ID from the modal's data attribute
                  var polylineId = $("#" + polylineModalId).data("lineId");

                  // Find the marker with the given ID and remove it from the map
                  if (polylineId) {
                    map.removeLayer(map._layers[polylineId]);
                  }

                  // Hide the modal
                  $("#" + polylineModalId).modal("hide");
                });

                // Add a listener for the "pm:edit" event
                polyline.on("pm:edit", function (event) {
                  // TODO: api request to geom/polyline.php
                  let payload = {
                    id: element.id,
                    sid: sid,
                    auth: auth,
                    polylineId: polyline._leaflet_id,
                    geom: event.layer.toGeoJSON().geometry.coordinates,
                    ref: ref,
                  };
                  // Send an AJAX request to update the marker in the database
                  api
                    .put("reports/geometry/polyline", payload)
                    .then((response) => {
                      toastr.options = optionToast;
                      // Show a success message to the user
                      toastr.success("Garisan Telah Berubah!");
                      polyline.setStyle({
                        color: "#7239ea",
                      });
                    })
                    .catch((error) => {
                      console.log(error);
                    });
                });

                polyline.on("pm:remove", function () {
                  // TODO: api request to geom/polyline.php
                  // Send an AJAX request to delete the marker from the database
                  api
                    .delete(
                      `reports/geometry/polyline?sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}&id=${element.id}&polylineId=${polyline._leaflet_id}`
                    )
                    .then((response) => {
                      toastr.options = optionToast;
                      // Show a success message to the user
                      toastr.error("Garisan Telah dibuang!");
                    })
                    .catch((error) => {
                      console.log(error);
                    });
                });
              });
            })
            .catch((error) => {
              console.log(error);
            });

        }
      });
    } else {
          mapData.forEach(function (mapReportData) {
      // setup variable for uiblock checking
      let uiBlockTotal = { marker: null, polygon: null, polyline: null };
      let uiBlockCount = { marker: 0, polygon: 0, polyline: 0 };

      let mapId = "map-" + mapReportData.authority_id;

      // Define Leaflet map
      var map = L.map(mapId, {
        zoomControl: false,
        scrollWheelZoom: false, // Disable the default zoom control buttons
        dragging: false, //Diable the movepan
        attributionControl: false,
        doubleClickZoom: false,
      }).setView([3.8358838977761702, 103.29957854588842], 16);

      var tiles = [
        "https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibXJoNHMiLCJhIjoiY2poeDh3bmtoMDk5ZTNrcDhkaDBnamFiMiJ9.G-NU8f4IrxF2vr7X43lE0w",
        "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
        // Add more tile URLs here as needed
      ];

      var tileIndex = 0; // Variable to keep track of the current tile index

      // Create a temporary tile layer with the first tile URL
      var tempTileLayer = L.tileLayer(tiles[tileIndex], {
        maxZoom: 21,
        zoomControl: false,
      });

      // Attach the 'tileerror' event to the temporary tile layer
      tempTileLayer.on("tileerror", function (error, tile) {
        console.log("Tile error:", error, tile);

    // Check if map container exists
    const mapContainer = document.getElementById(mapId);
    if (!mapContainer) {
      console.error('Map container not found:', mapId);
      return;
    }

    // Initialize Leaflet map
    var map = L.map(mapId, {
      zoomControl: false,        // Remove zoom buttons
      scrollWheelZoom: false,    // Disable scroll wheel zoom
      dragging: false,           // Disable dragging/panning
      attributionControl: false,
      doubleClickZoom: false,    // Disable double-click zoom
      touchZoom: false,          // Disable touch zoom on mobile
      boxZoom: false,            // Disable box zoom
      keyboard: false            // Disable keyboard zoom shortcuts
    }).setView([5.329882778232054, 103.14758516975338], 17);

    // Create a feature group to collect all map features for bounds calculation
    var featureGroup = L.featureGroup().addTo(map);

    console.log('Map initialized with all interactions disabled');

    // Tile layers with fallback
    var tiles = [
      "https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}",
      "https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibXJoNHMiLCJhIjoiY2poeDh3bmtoMDk5ZTNrcDhkaDBnamFiMiJ9.G-NU8f4IrxF2vr7X43lE0w",
      "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
    ];

    var tileIndex = 0;
    var tempTileLayer = L.tileLayer(tiles[tileIndex], {
      maxZoom: 21,
      attribution: 'OpenStreetMap contributors'
    });

    tempTileLayer.on("tileerror", function (error, tile) {
      console.log("Tile error:", error, tile);
      tileIndex++;
      if (tileIndex < tiles.length) {
        tempTileLayer.setUrl(tiles[tileIndex]);
      } else {
        console.log("No more tiles to try.");
      }
    });

    tempTileLayer.addTo(map);

    // Create numbered marker icons for image markers
    function createNumberedIcon(number, hasImage = false) {
      const iconColor = hasImage ? '#ea580c' : '#fb923c'; // Orange colors
      const textColor = '#ffffff';
      const borderColor = hasImage ? '#c2410c' : '#f97316'; // Darker orange borders
      
      return L.divIcon({
        className: 'custom-numbered-marker',
        html: `
          <div style="
            background-color: ${iconColor};
            border: 3px solid ${borderColor};
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: ${textColor};
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(234,88,12,0.4);
            font-family: 'Inter', sans-serif;
          ">${number}</div>
        `,
        iconSize: [35, 35],
        iconAnchor: [17, 17]
      });

      // Add the temporary tile layer to the map
      tempTileLayer.addTo(map);

      /* Creating a new icon object called warningIcon. */
      var warningIcon = L.icon({
        iconUrl: "assets/media/maps/warning-marker.svg",
        shadowUrl: "assets/media/maps/null.png", // Disable shadow
      });

      /* Creating a variable called primaryIcon and assigning it a value of L.icon. */
      var primaryIcon = L.icon({
        iconUrl: "assets/media/maps/primary-marker.svg",
        shadowUrl: "assets/media/maps/null.png", // Disable shadow
      });

      var successIcon = L.icon({
        iconUrl: "assets/media/maps/success-marker.svg",
        shadowUrl: "assets/media/maps/null.png", // Disable shadow
      });

      var infoIcon = L.icon({
        iconUrl: "assets/media/maps/info-marker.svg",
        shadowUrl: "assets/media/maps/null.png", // Disable shadow
      });

      var dangerIcon = L.icon({
        iconUrl: "assets/media/maps/danger-marker.svg",
        shadowUrl: "assets/media/maps/null.png", // Disable shadow
      });

      //LAYER REQUEST
      var chartingLine = L.Geoserver.wfs("https://map.asiadebut.tech/geoserver/wfs", {
        pmIgnore: true,
        layers: geoserverLayer,
        style: function (f) {
          switch (f.properties.method) {
            case "CW":
              return {
                color: "#057af6",
              };
            case "GI":
              return {
                color: "#ec7800",
              };
            case "HDD":
              return {
                color: "#f31869",
              };
            case "PJ":
              return {
                color: "#f2dd08",
              };
            case "GV":
              return {
                color: "#00db3e",
              };
            default:
              return {
                color: "#717071",
              };
          }
        },
        onEachFeature: function (f, l) {
          let color;
          switch (f.properties.method) {
            case "HDD":
              color = "danger";
              break;
            case "CW":
              color = "primary";
              break;
            case "GV":
              color = "success";
              break;
            case "GI":
              color = "warning";
              break;
            case "OH":
              color = "dark";
              break;
            default:
              color = "secondary";
          }
        },
        CQL_FILTER: "system_id='" + systemId + "' AND revision = 0",
      });
      chartingLine.addTo(map);

      //MAP CONFIRGURATION
      map.on("zoomend", function () {
        currentZoom = map.getZoom();
        if (currentZoom > 16) {
          chartingLine.setStyle({
            weight: 5,
            dashArray: "20",
          });
        } else if (currentZoom == 16) {
          chartingLine.setStyle({
            weight: 4,
            dashArray: "15",
          });
        } else if (currentZoom < 16) {
          chartingLine.setStyle({
            weight: 3,
            dashArray: "10",
          });
        } else {
          chartingLine.setStyle({
            weight: 2,
            dashArray: "5",
          });
        }
      });

      // TODO: Begin::Do event fetch api to update saved site visit data
      // Marker
      api
        .get(
          `reports/geometry/publicGeom?action=marker&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
        )
        .then((response) => {
          console.log({ "marker-init": response });
          uiBlockTotal.marker = response.markers.length;
          if (
            uiBlockTotal.marker == 0 &&
            uiBlockTotal.polygon == 0 &&
            uiBlockTotal.polyline == 0
          ) {
            // initLoadBlock.release();
          }
          // var marker;
          response.markers.forEach((element) => {
            // create marker
            var marker = L.marker([element.latitude, element.longitude]).addTo(
              map
            );

            // Do event fetch api to update unique _leaflet_id to the db
            let payload = {
              action: "update-geom-init",
              init: "marker",
              systemId: systemId,
              reportId: reportNo,
              authId: mapReportData.authority_id,
              id: element.id,
              geomId: element.geom_id,
              newGeomId: marker._leaflet_id,
            };
            api
              .put(`reports/geometry/publicGeom`, payload)
              .then((response) => {
                // console.log(response.data);
                console.log({ "marker-each-update-geom": response });

                // initializing check
                uiBlockCount.marker++;
                if (
                  uiBlockTotal.marker -
                    uiBlockCount.marker +
                    (uiBlockTotal.polygon - uiBlockCount.polygon) +
                    (uiBlockTotal.polyline - uiBlockCount.polyline) ==
                  0
                ) {
                  // initLoadBlock.release();
                }
              })
              .catch((error) => {
                console.log(error);
              });

            console.log({ "marker-each-instance": marker });

            // Create a unique ID for the modal using the marker's L.stamp property
            var modalId = "marker-" + marker._leaflet_id;

            // Create the modal HTML using the unique ID
            var modalHtml =
              `
          <div class="modal fade" data-bs-backdrop="static" id="` +
              modalId +
              `" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                      <form id="form-` +
              modalId +
              `">
                          <div class="modal-header">
                              <div class="dropzone">
                                  <!--begin::Message-->
                                  <div class="dz-message needsclick">
                                      <!--begin::Icon-->
                                      <span class="fa-stack">
                                          <i class="fad fa-image fa-stack-1x"
                                          style="font-size:9rem; position: absolute; left:70px; line-height: 250px;" ></i>
                                          <i class="fad fa-expand fa-stack-2x"
                                          style="font-size:20rem; position: absolute; left:15px; line-height:250px;"></i>
                                      </span>
                                      <!--end::Icon-->
                                  </div>
                              </div>
                          </div>
                          <input type="text" id="rowId-` +
              modalId +
              `" hidden />
                          <input type="text" id="action-` +
              modalId +
              `" value="add" hidden />
                          <div class="modal-body">
                              <div class="mb-0 form-floating fv-row">
                                  <textarea  data-kt-autosize="true" rows="4" class="form-control form-control-flush" placeholder="Nyatakan Keterangan"
                                  id="desc-` +
              modalId +
              `" name="description">` +
              element.description +
              `</textarea>
                                  <label for="desc-` +
              modalId +
              `" class="form-label">Keterangan</label>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>`;

            // Append the modal HTML to the body
            $("body").append(modalHtml);

            // Show the modal when the marker is created
            // $('#' + modalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $("#" + modalId).data("markerId", marker._leaflet_id);

            // Initialize Dropzone for the modal
            const latlng = marker.getLatLng();

            // TODO: request to geom/upload.php
            let dropzone = new Dropzone($("#" + modalId).find(".dropzone")[0], {
              url: apps + "/api/reports/geometry/upload",
              paramName: "file",
              maxFiles: 1,
              acceptedFiles: "image/*",
              thumbnailWidth: 600,
              thumbnailHeight: 600,
              maxFilesize: 1024,
              autoProcessQueue: false,
              addRemoveLinks: true,
              sending: function (file, xhr, formData) {
                formData.append("systemId", sid);
                formData.append("reportId", ref);
                formData.append("authId", auth);
                formData.append("markerId", marker._leaflet_id);
                formData.append("action", $("#action-" + modalId).val());
                formData.append("id", $("#rowId-" + modalId).val());
              },
              accept: function (file, done) {
                done();
              },
            });

            console.log({ "marker-init-dropzone-var": dropzone });

            let markerRowId;
            markerRowId = element.id;
            $("#rowId-" + modalId).val(markerRowId);

            // Get the marker ID from the modal's data attribute
            var markerId = $("#" + modalId).data("markerId");

            // Find the marker with the given ID
            var marker = map._layers[markerId];

            // Get the input values from the modal
            var description = element.description;

            // Get current timestamp to be patch with image url
            var currentImgDate = new Date();
            var currentImgTimestamp = currentImgDate.getTime();
            var image = atob(element.url) + "?t=" + currentImgTimestamp;

            $("#img-" + modalId).val(image);
            $("#action-" + modalId).val("edit");
            $("#cancel-" + modalId).removeClass("d-none");
            $("#delete-" + modalId).addClass("d-none");
            $("#save-" + modalId).addClass("d-none");
            $("#update-" + modalId).removeClass("d-none");

            // Set the marker's popup content
            marker.bindPopup(
              `
              <div class="d-flex flex-column">
                  <img src="` +
                image +
                `" class="report-image" />
                  <h4 class="fw-semibold">` +
                description +
                `</h4>
              </div>
              `,
              {
                minWidth: 400,
                closeButton: false,
              }
            );

            // Hide loading indication
            $("#save-" + modalId).removeAttr("data-kt-indicator");

            // Enable button
            $("#save-" + modalId).prop("disabled", false);

            // Hide the modal
            // $('#' + modalId).modal('hide');
            marker.setIcon(primaryIcon);
            map.pm.disableDraw();

            // Handle the "Cancel" button click for edit mode
            $("#cancel-" + modalId).click(function () {
              // Hide the modal
              $("#" + modalId).modal("hide");
            });

            // Handle the "Update" button click
            $("#update-" + modalId).click(function () {
              // TODO: api request to geom/marker.php
              let payload = {
                systemId: systemId,
                reportId: reportNo,
                authId: mapReportData.authority_id,
                description: $("#desc-" + modalId).val(),
                markerId: marker._leaflet_id,
                latitude: latlng.lat,
                longitude: latlng.lng,
                action: $("#action-" + modalId).val(),
                url: $("#img-" + modalId).attr("src"),
                id: markerRowId,
              };
              api
                .put(`reports/geometry/marker`, payload)
                .then((response) => {
                  var queuedFiles = dropzone.getQueuedFiles();
                  if (queuedFiles.length > 0) {
                    dropzone.processQueue();
                    dropzone.on("success", function (file, result) {
                      // Handle success after uploading all files
                      if (
                        dropzone.getQueuedFiles().length === 0 &&
                        dropzone.getUploadingFiles().length === 0
                      ) {
                        handleSuccess(result);
                      }
                    });
                  } else {
                    handleSuccess(response);
                  }
                })
                .catch((error) => {
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

              function handleSuccess(response) {
                toastr.options = optionToast;
                toastr.success("Pin Lokasi Berjaya Dikemaskini!");

                // Get the marker ID from the modal's data attribute
                var markerId = $("#" + modalId).data("markerId");

                // Find the marker with the given ID
                var marker = map._layers[markerId];

                // Get the input values from the modal
                var description = $("#desc-" + modalId).val();

                // Get current timestamp to be patch with image url
                var currentImgDate = new Date();
                var currentImgTimestamp = currentImgDate.getTime();
                var image = response.data.img + "?t=" + currentImgTimestamp;

                // Set the marker's popup content (on update success)
                marker
                  .bindPopup(
                    `
                  <div class="d-flex flex-end">
                  </div>
                  <div class="d-flex flex-column">
                  <img src="` +
                      image +
                      `" class="report-image"/>
                  <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` +
                      description +
                      `</h4>
                  </div>
              `,
                    {
                      minWidth: 400,
                      closeButton: false,
                    }
                  )
                  .openPopup();

                // Hide the modal
                $("#" + modalId).modal("hide");
                marker.setIcon(warningIcon);
              }
            });

            // Add a listener for the "pm:edit" event
            marker.on("pm:edit", function (event) {
              // alert('edit event for marker');
              // Get the new marker position
              var newLatLng = event.layer.getLatLng();
              let payload = {
                id: markerRowId,
                systemId: systemId,
                authId: mapReportData.authority_id,
                markerId: marker._leaflet_id,
                latitude: newLatLng.lat,
                longitude: newLatLng.lng,
                reportId: reportNo,
              };

              // TODO: api request to geom/marker.php
              api
                .put(`reports/geometry/marker`, payload)
                .then((response) => {
                  toastr.options = optionToast;
                  // Show a success message to the user
                  toastr.success("Lokasi Pin Telah Berubah!");
                  marker.setIcon(infoIcon);
                })
                .catch((error) => {
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
            });

            marker.on("pm:remove", function () {
              // TODO: api request to geom/marker.php
              api
                .delete(
                  `reports/geometry/marker?id=${markerRowId}&markerId=${marker._leaflet_id}&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
                )
                .then((response) => {
                  toastr.options = optionToast;
                  // Show a success message to the user
                  toastr.error("Pin bergambar Telah dibuang!");
                })
                .catch((error) => {
                  console.log(error);
                });
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });

      // TODO: Polygon
      api
        .get(
          `reports/geometry/publicGeom?action=polygon&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
        )
        .then((response) => {
          console.log({ "polygon-init": response });
          uiBlockTotal.polygon = response.polygon.length;
          if (
            uiBlockTotal.marker == 0 &&
            uiBlockTotal.polygon == 0 &&
            uiBlockTotal.polyline == 0
          ) {
            // initLoadBlock.release();
          }
          response.polygon.forEach((element) => {
            var dbPolygon = JSON.parse(element.geometry).coordinates[0].map(
              function (coord) {
                return [coord[1], coord[0]];
              }
            );
            console.log({ "polygon-each": dbPolygon });
            var polygon = L.polygon(dbPolygon, { color: "blue" }).addTo(map);
            console.log({ "polygon-each-instance": polygon });

            // Do event fetch api to update unique _leaflet_id to the db
            let payload = {
              action: "update-geom-init",
              sid: systemId,
              ref: reportNo,
              auth: mapReportData.authority_id,
              id: element.id,
              geomId: element.geom_id,
              newGeomId: polygon._leaflet_id,
            };
            api
              .put(`reports/geometry/polygon`, payload)
              .then((response) => {
                // console.log(response.data);
                console.log({ "polygon-each-update-geom": response });

                // initializing check
                uiBlockCount.polygon++;
                if (
                  uiBlockTotal.marker -
                    uiBlockCount.marker +
                    (uiBlockTotal.polygon - uiBlockCount.polygon) +
                    (uiBlockTotal.polyline - uiBlockCount.polyline) ==
                  0
                ) {
                  // initLoadBlock.release();
                }
              })
              .catch((error) => {
                console.log(error);
              });

            var polygonModalId = "polygon-" + polygon._leaflet_id;
            let polygonRowId;

            polygon.setStyle({
              color: "#3E97FF",
            });

            // Create the modal HTML using the unique ID
            var modalHtml =
              `
          <div class="modal fade" data-bs-backdrop="static" id="` +
              polygonModalId +
              `" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                      <form>
                          <div class="modal-body mb-0 form-floating">
                              <div class="form-floating mb-5">
                                  <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                  id="notes-` +
              polygonModalId +
              `" style="height: 100px"></textarea>
                                  <label for="notes-` +
              polygonModalId +
              `" class="form-label">Catatan</label>
                              </div>
                          </div>
                          <input type="text" id="rowId-` +
              element.id +
              `" hidden/>
                          <input type="text" id="action-` +
              polygonModalId +
              `" value="edit" hidden/>
                      </form>
                  </div>
              </div>
          </div>`;

            // Append the modal HTML to the body
            $("body").append(modalHtml);

            // insert rowId retrieved from db
            polygonRowId = element.id;
            $("#rowId-" + polygonModalId).val(polygonRowId);

            // insert recorded description from db into modal input
            $("#notes-" + polygonModalId).val(element.description);

            // Show the modal when the marker is created
            // $('#' + polygonModalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $("#" + polygonModalId).data("polygonId", polygon._leaflet_id);

            let geom = polygon.toGeoJSON();
            console.log({ "polygon-togeojson": geom });
            console.log({ "polygon-togeojson-sendToRequest": geom });
            // Handle the "Save" button click
            $("#save-" + polygonModalId).click(function () {
              // TODO: api request to geom/polygon.php
              let payload = {
                sid: sid,
                ref: ref,
                auth: auth,
                notes: $("#notes-" + polygonModalId).val(),
                polygonId: polygon._leaflet_id,
                geom: geom,
                action: $("#action-" + polygonModalId).val(),
                id: $("#rowId-" + polygonModalId).val(),
              };
              api
                .post("reports/geometry/polygon", payload)
                .then((response) => {
                  console.log({ "response.data.id": response.data.id });
                  polygonRowId = response.data.id;
                  $("#rowId-" + polygonModalId).val(polygonRowId);

                  toastr.options = optionToast;
                  toastr.success("Poligon Bernota Berjaya Disimpan!");

                  // Get the marker ID from the modal's data attribute
                  var polygonId = $("#" + polygonModalId).data("polygonId");

                  // Find the marker with the given ID
                  var polygon = map._layers[polygonId];

                  // Get the input values from the modal
                  var notes = $("#notes-" + polygonModalId).val();

                  $("#cancel-" + polygonModalId).removeClass("d-none");
                  $("#delete-" + polygonModalId).addClass("d-none");
                  $("#action-" + polygonModalId).val("edit");

                  // Set the marker's popup content
                  polygon
                    .bindPopup(
                      `
                          <div class="d-flex flex-column">
                              <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                        notes +
                        `</h5>
                          </div>
                          `,
                      {
                        minWidth: 350,
                        closeButton: false,
                      }
                    )
                    .openPopup();

                  // Hide the modal
                  $("#" + polygonModalId).modal("hide");
                  polygon.setStyle({
                    color: "#75CC68",
                  });
                })
                .catch((error) => {
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
            });

            // initialize bindpopup

            // Get the input values from the modal
            var notes = $("#notes-" + polygonModalId).val();

            $("#cancel-" + polygonModalId).removeClass("d-none");
            $("#delete-" + polygonModalId).addClass("d-none");
            $("#action-" + polygonModalId).val("edit");

            // Set the marker's popup content
            polygon.bindPopup(
              `
              <div class="d-flex flex-column">
                  <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                notes +
                `</h5>
              </div>
              `,
              {
                minWidth: 350,
                closeButton: false,
              }
            );

            // Handle the "Cancel" button click for new markers
            $("#delete-" + polygonModalId).click(function () {
              // Get the marker ID from the modal's data attribute
              var polygonId = $("#" + polygonModalId).data("polygonId");

              // Find the marker with the given ID and remove it from the map
              if (polygonId) {
                map.removeLayer(map._layers[polygonId]);
              }

              // Hide the modal
              $("#" + polygonModalId).modal("hide");
            });

            // Add a listener for the "pm:edit" event
            polygon.on("pm:edit", function (event) {
              // TODO: api request to geom/polygon.php
              payload = {
                id: element.id,
                sid: sid,
                auth: auth,
                polygonId: polygon._leaflet_id,
                geom: event.layer.toGeoJSON(),
                ref: ref,
              };
              // Send an AJAX request to update the marker in the database
              api
                .put("reports/geometry/polygon", payload)
                .then((response) => {
                  toastr.options = optionToast;
                  // Show a success message to the user
                  toastr.success("Poligon Telah Berubah!");
                  polygon.setStyle({
                    color: "#7239ea",
                  });
                })
                .catch((error) => {
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
            });

            polygon.on("pm:remove", function () {
              // TODO: api request to geom/polygon.php
              // Send an AJAX request to delete the marker from the database
              api
                .delete(
                  `reports/geometry/polygon?sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}&id=${element.id}&polygonId=${polygon._leaflet_id}`
                )
                .then((response) => {
                  toastr.options = optionToast;
                  // Show a success message to the user
                  toastr.error("Poligon Telah dibuang!");
                })
                .catch((error) => {
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
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });

      // TODO: polyline
      api
        .get(
          `reports/geometry/publicGeom?action=polyline&sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}`
        )
        .then((response) => {
          console.log({ "polyline-init": response });
          uiBlockTotal.polyline = response.polyline.length;
          if (
            uiBlockTotal.marker == 0 &&
            uiBlockTotal.polygon == 0 &&
            uiBlockTotal.polyline == 0
          ) {
            // initLoadBlock.release();
          }
          response.polyline.forEach((element) => {
            var dbLine = JSON.parse(element.geometry).coordinates.map(function (
              coord
            ) {
              return [coord[1], coord[0]];
            });
            console.log({ "polyline-each": dbLine });
            var polyline = L.polyline(dbLine, { color: "red" }).addTo(map);
            console.log({ "polyline-each-instance": polyline });

            // Do event fetch api to update unique _leaflet_id to the db
            let payload = {
              action: "update-geom-init",
              init: "polyline",
              sid: systemId,
              ref: reportNo,
              auth: mapReportData.authority_id,
              id: element.id,
              geomId: element.geom_id,
              newGeomId: polyline._leaflet_id,
            };
            api
              .put("reports/geometry/publicGeom", payload)
              .then((response) => {
                // console.log(response.data);
                console.log({ "polyline-each-update-geom": response });

                // initializing check
                uiBlockCount.polyline++;
                if (
                  uiBlockTotal.marker -
                    uiBlockCount.marker +
                    (uiBlockTotal.polygon - uiBlockCount.polygon) +
                    (uiBlockTotal.polyline - uiBlockCount.polyline) ==
                  0
                ) {
                  // initLoadBlock.release();
                }
              })
              .catch((error) => {
                console.log(error);
              });

            var polylineModalId = "line-" + polyline._leaflet_id;

            polyline.setStyle({
              color: "#3E97FF",
            });

            // Create the modal HTML using the unique ID
            var modalHtml =
              `
          <div class="modal fade" data-bs-backdrop="static" id="` +
              polylineModalId +
              `" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                      <form>
                          <div class="modal-body mb-0 form-floating">
                              <div class="form-floating mb-5">
                                  <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                  id="notes-` +
              polylineModalId +
              `" style="height: 100px"></textarea>
                                  <label for="notes-` +
              polylineModalId +
              `" class="form-label">Catatan</label>
                              </div>
                          </div>
                          <input type="text" id="rowId-` +
              element.id +
              `" hidden/>
                          <input type="text" id="action-` +
              polylineModalId +
              `" value="edit" hidden/>
                      </form>
                  </div>
              </div>
          </div>`;

            // Append the modal HTML to the body
            $("body").append(modalHtml);

            // insert recorded description from db into modal input
            $("#notes-" + polylineModalId).val(element.description);

            let polylineRowId;
            polylineRowId = element.id;
            $("#rowId-" + polylineModalId).val(polylineRowId);

            // Show the modal when the marker is created
            // $('#' + polylineModalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $("#" + polylineModalId).data("lineId", polyline._leaflet_id);

            const geoJSON = polyline.toGeoJSON();
            let geom = geoJSON.geometry.coordinates;
            // Handle the "Save" button click
            $("#save-" + polylineModalId).click(function () {
              // TODO: api request to geom/polyline.php
              let payload = {
                sid: sid,
                ref: ref,
                auth: auth,
                notes: $("#notes-" + polylineModalId).val(),
                polylineId: polyline._leaflet_id,
                geom: geom,
                action: $("#action-" + polylineModalId).val(),
                id: element.id,
              };
              api
                .post("reports/geometry/polyline", payload)
                .then((response) => {
                  toastr.options = optionToast;
                  toastr.success("Lukisan Bernota Berjaya Disimpan!");

                  // Get the marker ID from the modal's data attribute
                  var polylineId = $("#" + polylineModalId).data("lineId");

                  // Find the marker with the given ID
                  var polyline = map._layers[polylineId];

                  // Get the input values from the modal
                  var notes = $("#notes-" + polylineModalId).val();

                  $("#cancel-" + polylineModalId).removeClass("d-none");
                  $("#delete-" + polylineModalId).addClass("d-none");
                  $("#action-" + polylineModalId).val("edit");

                  // Set the marker's popup content
                  polyline
                    .bindPopup(
                      `
                      <div class="d-flex flex-column">
                          <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                        notes +
                        `</h5>
                      </div>
                      `,
                      {
                        minWidth: 350,
                        closeButton: false,
                      }
                    )
                    .openPopup();

                  // Hide the modal
                  $("#" + polylineModalId).modal("hide");
                  polyline.setStyle({
                    color: "#75CC68",
                  });
                })
                .catch((error) => {
                  console.log(error);
                });
            });

            // initiate bind popup

            // Get the marker ID from the modal's data attribute
            var polylineId = $("#" + polylineModalId).data("lineId");

            // Find the marker with the given ID
            var polyline = map._layers[polylineId];

            // Get the input values from the modal
            var notes = $("#notes-" + polylineModalId).val();

            $("#cancel-" + polylineModalId).removeClass("d-none");
            $("#delete-" + polylineModalId).addClass("d-none");
            $("#action-" + polylineModalId).val("edit");

            // Set the marker's popup content
            polyline.bindPopup(
              `
              <div class="d-flex flex-column">
                  <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` +
                notes +
                `</h5>
              </div>
              `,
              {
                minWidth: 350,
                closeButton: false,
              }
            );

            // Handle the "Cancel" button click for new markers
            $("#delete-" + polylineModalId).click(function () {
              // Get the marker ID from the modal's data attribute
              var polylineId = $("#" + polylineModalId).data("lineId");

              // Find the marker with the given ID and remove it from the map
              if (polylineId) {
                map.removeLayer(map._layers[polylineId]);
              }

              // Hide the modal
              $("#" + polylineModalId).modal("hide");
            });

            // Add a listener for the "pm:edit" event
            polyline.on("pm:edit", function (event) {
              // TODO: api request to geom/polyline.php
              let payload = {
                id: element.id,
                sid: sid,
                auth: auth,
                polylineId: polyline._leaflet_id,
                geom: event.layer.toGeoJSON().geometry.coordinates,
                ref: ref,
              };
              // Send an AJAX request to update the marker in the database
              api
                .put("reports/geometry/polyline", payload)
                .then((response) => {
                  toastr.options = optionToast;
                  // Show a success message to the user
                  toastr.success("Garisan Telah Berubah!");
                  polyline.setStyle({
                    color: "#7239ea",
                  });
                })
                .catch((error) => {
                  console.log(error);
                });
            });

            polyline.on("pm:remove", function () {
              // TODO: api request to geom/polyline.php
              // Send an AJAX request to delete the marker from the database
              api
                .delete(
                  `reports/geometry/polyline?sid=${systemId}&ref=${reportNo}&auth=${mapReportData.authority_id}&id=${element.id}&polylineId=${polyline._leaflet_id}`
                )
                .then((response) => {
                  toastr.options = optionToast;
                  // Show a success message to the user
                  toastr.error("Garisan Telah dibuang!");
                })
                .catch((error) => {
                  console.log(error);
                });
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });
    });
    }
    // TODO:
    // TODO: End::Do event fetch api to update saved site visit data
  }

  return {
    init: function () {
      mapSetup();
    },
  };
})();

KTUtil.onDOMContentLoaded(function () {
  prints.init();
});