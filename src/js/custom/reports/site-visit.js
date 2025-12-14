
/* Getting the sid and ref from the URL. */
const urlParams = new URLSearchParams(window.location.search);
const ref = urlParams.get('ref');
const auth = urlParams.get('auth');

// Get the current URL
let currentURL = new URL(window.location.href);
let urlWithOutParams = currentURL.origin + currentURL.pathname;

// Get the values of the parameters
let htaccessUrlParams = urlWithOutParams.replace(apps + '/reports/site', '').split("/");
// urlParams[0] is ignore as it handle the first / in the trimmed url
const sid = htaccessUrlParams[1];
var pmDrawingState = false; // custom drawing state variable for geoman

var initLoadBlock = (function () {
    // get block ui target element
    const blockEl = document.querySelector('[report-custom-data="map-container"]');

    var blockUI = new KTBlockUI(blockEl, {
        message: '<div class="blockui-message"><span class="spinner-border text-primary fs-2"></span> Sedang memuat tetapan peta...</div>',
    });
    return {
        block: function () {
            blockUI.block();
        },
        release: function () {
            blockUI.release();
        }
    }
})();

function mapSetup() {
    // ANCHOR - Begin::Api request queue function::maybe need to move this somewhere else in the future
    // Create a queue to store API requests
    const apiRequestQueue = new Map();

    // Function to make API requests
    async function makeApiRequest({ method, gateway, data }) {
        try {
            const response = await api[method.toLowerCase()](gateway, data);
            return response; // Return the entire response
        } catch (error) {
            throw error;
        }
    }

    // Function to process the API request queue for a specific tag
    async function processApiQueue(tag) {
        if (!apiRequestQueue.has(tag)) return;

        const queue = apiRequestQueue.get(tag);
        const delayBetweenRequests = 0; // Set the delay in milliseconds (adjust as needed)

        while (queue.length > 0) {
            const requestData = queue.shift(); // Get the first request

            try {
                // Make the API request using makeApiRequest function after the delay
                await new Promise(resolve => setTimeout(resolve, delayBetweenRequests));
                const response = await makeApiRequest(requestData);
                // console.log({ makeApiRequest: requestData });

                // If resolve function is provided, call it with the response
                if (requestData.resolve) {
                    requestData.resolve(response);
                    // console.log({ apiResponseFor: requestData, response: response });
                }
            } catch (error) {
                // If reject function is provided, call it with the error
                if (requestData.reject) {
                    requestData.reject(error);
                }
                console.error(`Error processing request ${requestData}:`, error);
            }
        }

        // Clear the queue after processing
        apiRequestQueue.delete(tag);
    }

    // Function to add requests to the queue
    function addToApiQueue({ method, gateway, data, tag = 'default' }) {
        return new Promise((resolve, reject) => {
            if (!apiRequestQueue.has(tag)) {
                apiRequestQueue.set(tag, []);
            }

            const queue = apiRequestQueue.get(tag);
            queue.push({ method, gateway, data, resolve, reject });
            // console.log({ [`${tag}Queue.length`]: queue.length });

            // If the queue was empty and not processing, start processing
            if (queue.length === 1) {
                processApiQueue(tag);
            }
        });
    }

    // ANCHOR - End::Api request queue function::maybe need to move this somewhere else in the future

    // setup variable for uiblock checking
    let uiBlockTotal = { marker: null, polygon: null, polyline: null };
    let uiBlockCount = { marker: 0, polygon: 0, polyline: 0 };
    let initModalAlertStatus = true;

    /* Creating a JavaScript object. */
    var reloadNotify = {
        html: `Anda pasti untuk memuat kembali halaman ini?`,
        icon: "warning",
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Ok, Teruskan!",
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: "btn btn-warning",
            cancelButton: 'btn btn-secondary'
        }
    }

    /* The above code is preventing the user from refreshing the page. */
    $(document).keydown(function (e) {
        if (e.keyCode == 116) {
            e.preventDefault();

            Swal.fire(reloadNotify).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Refresh the page if the user clicks "Ok, Teruskan!"
                }
            });
        }
    });

    /* The above code is preventing the user from refreshing the page. */
    $(document).on("keydown", function (e) {
        if (e.which === 116) {
            e.preventDefault();

            Swal.fire(reloadNotify).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Refresh the page if the user clicks "Ok, Teruskan!"
                }
            });
        }
    });

    // Prevent page refresh on desktop and tablet devices
    // $(window).on('beforeunload', function (e) {
    //     return false;
    // });

    // Prevent page refresh on mobile devices
    $(document).on('touchmove', function (event) {
        if (event.originalEvent.touches.length > 1) {
            event.preventDefault();
        }
    }, false);

    /* The above code is preventing the user from scrolling down the page. */
    var lastY;
    $(document).on('touchstart', function (event) {
        lastY = event.originalEvent.touches[0].clientY;
    });

    $(document).on('touchmove', function (event) {
        var currentY = event.originalEvent.touches[0].clientY;
        if (currentY > lastY) {
            event.preventDefault();
        }
        lastY = currentY;
    });

    /* Defining the options for the toastr.js plugin. */

    var optionToast = {
        closeButton: false,
        debug: false,
        newestOnTop: false,
        progressBar: false,
        positionClass: "toastr-top-center",
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


    // Define Leaflet map
    var map = L.map('map').setView([3.8358838977761702, 103.29957854588842], 16);

    var tiles = [
        'https://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}',
        'https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibXJoNHMiLCJhIjoiY2poeDh3bmtoMDk5ZTNrcDhkaDBnamFiMiJ9.G-NU8f4IrxF2vr7X43lE0w',
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        // Add more tile URLs here as needed
    ];

    var tileIndex = 0; // Variable to keep track of the current tile index

    // Create a temporary tile layer with the first tile URL
    var tempTileLayer = L.tileLayer(tiles[tileIndex], {
        maxZoom: 21,
        zoomControl: false,
    });

    // Attach the 'tileerror' event to the temporary tile layer
    tempTileLayer.on('tileerror', function (error, tile) {
        console.log('Tile error:', error, tile);

        // Move to the next tile URL
        tileIndex++;
        if (tileIndex < tiles.length) {
            // If there are more tiles, replace the tile layer with the next tile URL
            tempTileLayer.setUrl(tiles[tileIndex]);
        } else {
            // If there are no more tiles, handle the error as desired
            console.log('No more tiles to try.');
        }
    });

    // Add the temporary tile layer to the map
    tempTileLayer.addTo(map);

    /* Creating a new icon object called warningIcon. */
    var warningIcon = L.icon({
        iconUrl: 'assets/media/maps/warning-marker.svg',
        shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    /* Creating a variable called primaryIcon and assigning it a value of L.icon. */
    var primaryIcon = L.icon({
        iconUrl: 'assets/media/maps/primary-marker.svg',
        shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    var successIcon = L.icon({
        iconUrl: 'assets/media/maps/success-marker.svg',
        shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    var infoIcon = L.icon({
        iconUrl: 'assets/media/maps/info-marker.svg',
        shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    var dangerIcon = L.icon({
        iconUrl: 'assets/media/maps/danger-marker.svg',
        shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    // Create additional Control placeholders
    function addControlPlaceholders(map) {
        var corners = map._controlCorners,
            l = 'leaflet-',
            container = map._controlContainer;

        function createCorner(vSide, hSide) {
            var className = l + vSide + ' ' + l + hSide;

            corners[vSide + hSide] = L.DomUtil.create('div', className, container);
        }

        createCorner('center', 'left');
        createCorner('center', 'right');
    }
    addControlPlaceholders(map);


    //LAYER REQUEST
    var chartingLine = L.Geoserver.wfs("https://map.asiadebut.tech/geoserver/wfs", {
        pmIgnore: true,
        layers: geoserverLayer,
        style: function (f) {
            switch (f.properties.method) {
                case 'CW':
                    return {
                        color: "#057af6"
                    };
                case 'GI':
                    return {
                        color: "#ec7800"
                    };
                case 'HDD':
                    return {
                        color: "#f31869"
                    };
                case 'PJ':
                    return {
                        color: "#f2dd08"
                    };
                case 'GV':
                    return {
                        color: "#00db3e"
                    };
                default:
                    return {
                        color: "#717071"
                    };
            }
        },
        onEachFeature: function (f, l) {

            let color;
            switch (f.properties.method) {
                case 'HDD':
                    color = "danger";
                    break;
                case 'CW':
                    color = "primary";
                    break;
                case 'GV':
                    color = "success";
                    break;
                case 'GI':
                    color = "warning";
                    break;
                case 'OH':
                    color = "dark";
                    break;
                default:
                    color = "secondary";
            }

            let dataPopup = `
        <div>
            <h6 class="fw-semibold">Laluan Cadangan Korekan</h6>
            <table class="table table-bordered">
            <tr>
                <th>Jarak</th>
                <td class="fw-semibold">` + f.properties.length + ` m</td>
            </tr>
            <tr>
                <th>Kaedah</th>
                <td><span class="badge badge-light-` + color + ` fs-6">` + f.properties.method + `</span></td>
            </tr>
            </table>
        </div>`;
            popup = l.bindPopup(dataPopup, {
                maxHeight: 300,
                maxWidth: 500,
                closeButton: false,
            });
        },
        CQL_FILTER: "system_id='" + sid + "' AND revision = 0",
    });
    chartingLine.addTo(map);



    //MAP CONFIRGURATION

    // Change the position of the Zoom Control to a newly created placeholder.
    map.zoomControl.setPosition('centerright');

    map.on('zoomend', function () {
        currentZoom = map.getZoom();
        if (currentZoom > 16) {
            chartingLine.setStyle({
                weight: 5,
                dashArray: '20'
            });
        } else if (currentZoom == 16) {
            chartingLine.setStyle({
                weight: 4,
                dashArray: '15'
            });
        } else if (currentZoom < 16) {
            chartingLine.setStyle({
                weight: 3,
                dashArray: '10'
            });
        } else {
            chartingLine.setStyle({
                weight: 2,
                dashArray: '5'
            });
        }
    });

    map.on('click', function (e) {
        if (!map.pm.globalEditEnabled() && !pmDrawingState) {
            // // console.log({ "e": e });
            var layers = [];
            layers.push(chartingLine);
            // // console.log({ "layers": layers });
            var closest = L.GeometryUtil.closestLayer(map, layers, e.latlng);
            // // console.log({ "closest": closest });
            if (closest) {
                var layer = closest.layer;
                var distance = closest.distance;
                if (distance < 50) {
                    var popup = layer.getPopup();
                    if (!popup) {
                        popup = L.popup({
                            maxHeight: 300,
                            maxWidth: 500,
                            closeButton: false,
                        });
                        layer.bindPopup(dataPopup, {
                            maxHeight: 300,
                            maxWidth: 500,
                            closeButton: false,
                        });
                    }
                    popup.setLatLng(closest.latlng);
                    popup.openOn(map);
                }
            }
        }
    });

    // console.log({ "edit-state": map.pm.globalEditEnabled() });

    // declare site visit init modal
    const initModalEl = document.getElementById('site-visit-init-modal');
    const initModal = new bootstrap.Modal(initModalEl);
    let svStartStatus = false;
    initModalEl.addEventListener('hide.bs.modal', function (event) {
        console.log('hide event fired');
        if (svStartStatus === false) {
            console.log('Modal hide prevented, showing again');
            event.preventDefault(); // Prevent the modal from being hidden

            // const targetModal = event.target;
            // console.log(targetModal);
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            if (initModalAlertStatus == true) {
                Swal.fire({
                    text: "Maaf, anda perlu menekan butang Mula untuk meneruskan Lawatan Tapak.",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, maklum!",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                });
            } else {
                initModalAlertStatus = true;
            }
        }
    });
    const initModalTitleEl = initModalEl.querySelector('[data-sv-init="title"]');
    const initModalHead1El = initModalEl.querySelector('[data-sv-init="head-1"]');
    const initModalHead2El = initModalEl.querySelector('[data-sv-init="head-2"]');
    const initModalSubtitleEl = initModalEl.querySelector('[data-sv-init="subtitle"]');
    const timeEl = initModalEl.querySelector('[data-sv-init="time"]');
    const timeDescEl = initModalEl.querySelector('[data-sv-init="time-desc"]');
    const startBtn = initModalEl.querySelector('[data-sv-init="start"]');
    const initModalHideBtn = initModalEl.querySelector('[data-sv-init="hide"]');
    const applInfoEl = initModalEl.querySelector('[data-sv-init="appl-info"]');
    const guestRepeaterEl = initModalEl.querySelector('[data-sv-init="guest-repeater"]');
    const bodyEl = document.getElementById('kt_body');
    let intervalId; // used to update clock

    // setup clock on modal

    // Initialize the timestamp with the server time when the page loads
    let serverTimestamp;

    // Function to fetch the timestamp from the PHP backend
    function fetchServerTimestamp() {
        // Replace 'timestamp.php' with the actual URL of your PHP backend script
        addToApiQueue({ method: 'GET', gateway: `reports/${sid}?site-visit=time` }).then(response => {
            // Store the server timestamp
            serverTimestamp = new Date(response.timestamp);
            // Start the clock
            updateClock();
        }).catch(error => {
            console.error('Error fetching server timestamp:', error);
            timeEl.textContent = 'Error fetching server timestamp';
        });
    }

    // Function to update the clock continuously
    function updateClock() {
        // Clear the interval if it's already running to avoid multiple intervals
        clearInterval(intervalId);

        // Start a new interval and store its ID in the intervalId variable
        intervalId = setInterval(() => {
            // Get the current time
            const currentTime = new Date();
            // Calculate the time difference in milliseconds
            const timeDifference = currentTime - serverTimestamp;
            // Update the time based on the difference
            const updatedTime = new Date(serverTimestamp.getTime() + timeDifference);

            // Format the date and time in the desired format
            const formattedDate = formatDate(updatedTime);
            const formattedTime = formatTime(updatedTime);

            // Update the timeEl span with the formatted date and time
            timeEl.textContent = `${formattedDate}   :   ${formattedTime}`;
        }, 1000); // Update the clock every second
    }

    // Function to format the date as "DD MMMM YYYY" (e.g., 02 OGOS 2023)
    function formatDate(date) {
        const months = [
            'JANUARI', 'FEBRUARI', 'MAC', 'APRIL', 'MEI', 'JUN',
            'JULAI', 'OGOS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DISEMBER'
        ];
        const day = String(date.getDate());
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        return `${day} ${month} ${year}`;
    }

    // Function to format the time as "hh:mm:ss"
    function formatTime(date) {
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }

    // Check if the site visit is being initialized
    function fetchSiteVisitDate() {
        // Replace 'timestamp.php' with the actual URL of your PHP backend script
        addToApiQueue({ method: 'GET', gateway: `reports/${sid}?site-visit=svDate&rid=${ref}&auth=${auth}` }).then(response => {
            if (response.svDate == null || response.svDate == undefined) {
                // Call the fetchServerTimestamp function to initiate fetching the timestamp from the server
                fetchServerTimestamp();
            } else {
                svStartStatus = true;
                timeEl.textContent = `${formatDate(new Date(response.svDate))}   :   ${formatTime(new Date(response.svDate))}`;
                initModalSubtitleEl.textContent = `Sila tekan pada butang Tambah Pegawai untuk menambah senarai kehadiran atau tekan pada butang Tutup untuk meneruskan laporan.`;
                timeDescEl.textContent = `Ini merupakan catatan masa berkaitan waktu Lawatan Tapak ini dilaksanakan.`;
                // Add the 'd-none' class to hide the button
                startBtn.classList.add('d-none');
                initModalHideBtn.classList.remove('d-none');

                // Disable the button
                startBtn.disabled = true;

                // Hide Application info & Enable the guest repeater
                initModalHead1El.classList.add('d-none');
                initModalHead2El.classList.remove('d-none');
                applInfoEl.classList.add('d-none');
                guestRepeaterEl.classList.remove('d-none');
                initModalTitleEl.textContent = `Kehadiran Lawatan Tapak`;
            }
        }).catch(error => {
            console.error('Error fetching svDate data:', error);
        });
    }
    fetchSiteVisitDate();

    // handle start button click event
    startBtn.onclick = () => {
        // stop the clock
        clearInterval(intervalId);
        let payload = {
            "site-visit": "time",
            sid: sid,
            rid: ref,
            auth: auth
        }
        addToApiQueue({ method: 'PUT', gateway: `reports/${sid}`, data: payload }).then((response) => {
            console.log(response);
            if (response.success) {
                fetchSiteVisitDate();
                Swal.fire({
                    text: "Tahniah. Lawatan Tapak ini berjaya dimulakan.",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, teruskan!",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // initModal.hide();
                    }
                });
            }
        }).catch((error) => {
            console.log(error);
        });
    };


    initModalHideBtn.onclick = () => {
        initModal.hide();
    }

    // Create the button HTML string with the appropriate icon
    const buttonHTML = `
<div class="card hover-elevate-up shadow parent-hover mb-2">
    <div class="card-body d-flex align-items p-0">
        <button class="btn btn-flex btn-active-light-success btn-icon-success btn-text-success btn-save" id="save-report" style="width: 100%;">
            <i class="fa fa-floppy-disk fs-1"></i>
            <span class="d-flex flex-column align-items-start ms-2">
                <span class="fs-4 fw-bold">Simpan Laporan</span>
                <span class="fs-7">Ringkasan Bergambar</span>
            </span>
        </button>
    </div>
</div>
<div class="card hover-elevate-up shadow parent-hover">
    <div class="card-body d-flex align-items p-0">
        <button class="btn btn-flex btn-active-light-primary btn-icon-primary btn-text-primary btn-new-button" id="attendance-list" style="width: 100%;">
            <i class="fa fa-signature fs-1"></i>
            <span class="d-flex flex-column align-items-start ms-2">
                <span class="fs-4 fw-bold">Tandatangan Kehadiran</span>
                <span class="fs-7">Senarai Kehadiran</span>
            </span>
        </button>
    </div>
</div>
`;
    // Create a new instance of the control with the button HTML
    const control = L.control.custom({
        position: 'bottomright',
        content: buttonHTML,
        events: {
            click: function (e) {
                // // console.log({"event-clicked":e.target.closest('button').id});
                if (e.target.closest('button').id == "save-report") {
                    Swal.fire({
                        html: `Anda pasti untuk tamatkan laporan bergambar ini?`,
                        icon: "info",
                        buttonsStyling: false,
                        showCancelButton: true,
                        confirmButtonText: "Ok, Teruskan!",
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: "btn btn-info",
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        // TODO: request to reports/index.php with locked: "true"
                        if (result.isConfirmed) {
                            let payload = {
                                systemId: sid,
                                reportId: ref,
                                authId: auth,
                                locked: "true"
                            };
                            // Send Axios POST request
                            addToApiQueue({ method: 'POST', gateway: `reports/${sid}`, data: payload })
                                .then(response => {
                                    console.log(response);
                                    toastr.options = optionToast;
                                    toastr.success(response.rowDeleted + " data kosong di buang & laporan contengan bergambar berjaya disimpan! ");

                                    setTimeout(function () {
                                        location.href = `/reports/site/summary/${response.systemId}?r=${response.reportId}&a=${response.authId}`;
                                    }, 2000)
                                })
                                .catch(error => {
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
                        }
                    });
                    console.log('wrapper div element clicked');
                    console.log(e);


                } else if (e.target.closest('button').id == "attendance-list") {
                    // alert('show attendance list here');
                    initModal.show();
                }
            },
        },
    }).addTo(map);

    // Add Compass
    map.addControl(new L.Control.Compass({
        autoActive: true,
        textErr: "Sudut orientasi tidak ditemui"
    }));

    L.control.locate({
        position: 'centerright',
        returnToPrevBounds: true,
        flyTo: true,
        drawCircle: false,
        showPopup: false
    }).addTo(map);

    // Remove Grey Border
    setTimeout(function () {
        window.dispatchEvent(new Event('resize'));
    }, 1000);

    // Set the default marker icon to the custom icon
    // L.Marker.prototype.options.icon = primaryIcon; //error marker created through script not showing after implement this line


    map.pm.addControls({
        position: 'centerleft',
        drawCircle: false,
        drawCircleMarker: false,
        drawRectangle: false,
        drawText: false,
        cutPolygon: false,
        rotateMode: false,
    });

    const ms = {
        tooltips: {
            placeMarker: "Klik untuk letakkan penanda",
            firstVertex: "Klik untuk letakkan titik permulaan",
            continueLine: "Klik untuk teruskan melukis",
            finishLine: "Klik mana-mana penanda yang sedia ada untuk menamatkan",
            finishPoly: "Klik penanda pertama untuk menamatkan",
            finishRect: "Klik untuk menamatkan",
            startCircle: "Klik untuk letakkan pusat bulatan",
            finishCircle: "Klik untuk menamatkan bulatan",
            placeCircleMarker: "Klik untuk letakkan penanda bulatan",
            placeText: "Klik untuk letakkan teks"
        },
        actions: {
            finish: "Selesai",
            cancel: "Batal",
            removeLastVertex: "Buang Titik Terakhir"
        },
        buttonTitles: {
            drawMarkerButton: "Lukis Penanda",
            drawPolyButton: "Lukis Poligon",
            drawLineButton: "Lukis Garisan",
            drawCircleButton: "Lukis Bulatan",
            drawRectButton: "Lukis Segi Empat",
            editButton: "Sunting Lapisan",
            dragButton: "Heret Lapisan",
            cutButton: "Potong Lapisan",
            deleteButton: "Buang Lapisan",
            drawCircleMarkerButton: "Lukis Penanda Bulatan",
            snappingButton: "Seret penanda ke lapisan dan titik lain",
            pinningButton: "Pin titik bersama-sama",
            rotateButton: "Putar Lapisan",
            drawTextButton: "Lukis Teks",
            scaleButton: "Skala Lapisan",
            autoTracingButton: "Jejak Automatik Garis"
        },
        measurements: {
            totalLength: "Panjang",
            segmentLength: "Panjang Segmen",
            area: "Kawasan",
            radius: "Jejari",
            perimeter: "Perimeter",
            height: "Tinggi",
            width: "Lebar",
            coordinates: "Koordinat",
            coordinatesMarker: "Penanda Koordinat"
        }
    };
    map.pm.setLang('customName', ms, 'en');

    // TODO: Begin::Do event fetch api to update saved site visit data
    // Marker
    addToApiQueue({ method: "GET", gateway: `reports/geometry/marker?action=init&sid=${sid}&ref=${ref}&auth=${auth}`, tag: 'marker' }).then(response => {
        uiBlockTotal.marker = response.markers.length;
        if ((uiBlockTotal.marker == 0) && (uiBlockTotal.polygon == 0) && (uiBlockTotal.polyline == 0)) {
            initLoadBlock.release();
        }
        // var marker;
        response.markers.forEach((element, index) => {
            // create marker
            var marker = L.marker([element.latitude, element.longitude]).addTo(map);

            // Do event fetch api to update unique _leaflet_id to the db
            let payload = {
                action: "update-geom-init",
                systemId: sid,
                reportId: ref,
                authId: auth,
                id: element.id,
                geomId: element.geom_id,
                newGeomId: marker._leaflet_id
            };

            addToApiQueue({ method: "PUT", gateway: `reports/geometry/marker`, data: payload , tag: 'marker'}).then(response => {
                // console.log(response.data);
                // console.log({ "marker-each-update-geom": response });

                // initializing check
                uiBlockCount.marker++;
                if (((uiBlockTotal.marker - uiBlockCount.marker) + (uiBlockTotal.polygon - uiBlockCount.polygon) + (uiBlockTotal.polyline - uiBlockCount.polyline)) == 0) {
                    initLoadBlock.release();
                }
            }).catch(error => {
                console.log(error);
            });


            // console.log({ "marker-each-instance": marker });

            // Create a unique ID for the modal using the marker's L.stamp property
            var modalId = 'marker-' + marker._leaflet_id;

            // Create the modal HTML using the unique ID
            var modalHtml = `
        <div class="modal fade" data-bs-backdrop="static" id="` + modalId + `" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <form id="form-` + modalId + `">
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
                        <input type="text" id="rowId-` + modalId + `" hidden />
                        <input type="text" id="action-` + modalId + `" value="add" hidden />
                        <div class="modal-body">
                            <div class="mb-0 form-floating fv-row">
                                <textarea  data-kt-autosize="true" rows="4" class="form-control form-control-flush" placeholder="Nyatakan Keterangan"
                                id="desc-` + modalId + `" name="description">` + element.description + `</textarea>
                                <label for="desc-` + modalId + `" class="form-label">Keterangan</label>
                            </div>
                        </div>
                        <div class="modal-footer border-transparent mt-0 pt-0">
                            <button type="button" class="btn  btn-lg btn-active-light-danger" id="delete-` + modalId + `">
                                <i class="fad fa-trash-alt fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-active-light-dark" data-bs-dismiss="modal" id="cancel-` + modalId + `">
                                <i class="fad fa-xmark fs-3"></i>
                            </button>
                            <!--begin::Submit-->
                            <button id="save-` + modalId + `" class="btn btn-lg btn-active-light-success flex-shrink-0">
                                <!--begin::Indicator label-->
                                <i class="indicator-label fad fa-floppy-disk fs-2"></i>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>
                            <!--end::Submit-->
                            <button type="button" class="d-none btn btn-lg btn-active-light-warning" id="update-` + modalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

            // Append the modal HTML to the body
            $('body').append(modalHtml);

            // Show the modal when the marker is created
            // $('#' + modalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $('#' + modalId).data('markerId', marker._leaflet_id);

            // Initialize Dropzone for the modal
            const latlng = marker.getLatLng();

            // TODO: request to geom/upload.php
            let dropzone = new Dropzone($('#' + modalId).find('.dropzone')[0], {
                url: apps + "/api/reports/geometry/upload",
                paramName: 'file',
                maxFiles: 1,
                acceptedFiles: "image/*",
                thumbnailWidth: 600,
                thumbnailHeight: 600,
                maxFilesize: 1024,
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {

                    formData.append('systemId', sid);
                    formData.append('reportId', ref);
                    formData.append('authId', auth);
                    formData.append('markerId', marker._leaflet_id);
                    formData.append('action', $('#action-' + modalId).val());
                    formData.append('id', $('#rowId-' + modalId).val());


                },
                accept: function (file, done) {
                    done();
                }
            });

            // console.log({ "marker-init-dropzone-var": dropzone });

            let markerRowId;
            markerRowId = element.id;
            $('#rowId-' + modalId).val(markerRowId);

            // Get the marker ID from the modal's data attribute
            var markerId = $('#' + modalId).data('markerId');

            // Find the marker with the given ID
            var marker = map._layers[markerId];

            // Get the input values from the modal
            var description = element.description;

            // Get current timestamp to be patch with image url
            var currentImgDate = new Date();
            var currentImgTimestamp = currentImgDate.getTime();
            var image = atob(element.url) + '?t=' + currentImgTimestamp;

            $('#img-' + modalId).val(image);
            $('#action-' + modalId).val('edit');
            $('#cancel-' + modalId).removeClass("d-none");
            $('#delete-' + modalId).addClass("d-none");
            $('#save-' + modalId).addClass("d-none");
            $('#update-' + modalId).removeClass("d-none");

            // Set the marker's popup content
            marker.bindPopup(`
            <div class="d-flex flex-end">
                <button type="button" class="btn btn-dark btn-icon btn-edit"
                data-bs-toggle="modal" data-bs-target="#` + modalId + `">
                    <i class="fad fa-pen-to-square fs-2"></i>
                </button>
            </div>
            <div class="d-flex flex-column">
                <img src="` + image + `" class="report-image" />
                <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` + description + `</h4>
            </div>
            `, {
                minWidth: 400,
                closeButton: false,
            });

            // Hide loading indication
            $('#save-' + modalId).removeAttr("data-kt-indicator");

            // Enable button
            $('#save-' + modalId).prop("disabled", false);

            // Hide the modal
            // $('#' + modalId).modal('hide');
            marker.setIcon(primaryIcon);
            map.pm.disableDraw();

            // Handle the "Cancel" button click for edit mode
            $('#cancel-' + modalId).click(function () {
                // Hide the modal
                $('#' + modalId).modal('hide');
            });

            // Handle the "Update" button click
            $('#update-' + modalId).click(function () {
                // TODO: api request to geom/marker.php
                let payload = {
                    systemId: sid,
                    reportId: ref,
                    authId: auth,
                    description: $('#desc-' + modalId).val(),
                    markerId: marker._leaflet_id,
                    latitude: latlng.lat,
                    longitude: latlng.lng,
                    action: $('#action-' + modalId).val(),
                    url: $('#img-' + modalId).attr('src'),
                    id: markerRowId
                };
                addToApiQueue({ method: 'PUT', gateway: `reports/geometry/marker`, data: payload, tag: 'marker' }).then(response => {
                    var queuedFiles = dropzone.getQueuedFiles();
                    if (queuedFiles.length > 0) {
                        dropzone.processQueue();
                        dropzone.on("success", function (file, result) {
                            // Handle success after uploading all files
                            if (dropzone.getQueuedFiles().length === 0 && dropzone.getUploadingFiles().length === 0) {
                                handleSuccess(result);
                            }
                        });
                    } else {
                        handleSuccess(response);
                    }
                }).catch(error => {
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
                    var markerId = $('#' + modalId).data('markerId');

                    // Find the marker with the given ID
                    var marker = map._layers[markerId];

                    // Get the input values from the modal
                    var description = $('#desc-' + modalId).val();

                    // Get current timestamp to be patch with image url
                    var currentImgDate = new Date();
                    var currentImgTimestamp = currentImgDate.getTime();
                    var image = response.data.img + '?t=' + currentImgTimestamp;

                    // Set the marker's popup content (on update success)
                    marker.bindPopup(`
                <div class="d-flex flex-end">
                <button type="button" class="btn btn-dark btn-icon btn-edit"
                    data-bs-toggle="modal" data-bs-target="#` + modalId + `">
                    <i class="fad fa-pen-to-square fs-2"></i>
                </button>
                </div>
                <div class="d-flex flex-column">
                <img src="` + image + `" class="report-image"/>
                <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` + description + `</h4>
                </div>
            `, {
                        minWidth: 400,
                        closeButton: false,
                    }).openPopup();

                    // Hide the modal
                    $('#' + modalId).modal('hide');
                    marker.setIcon(warningIcon);
                }

            });

            // Add a listener for the "pm:edit" event
            marker.on('pm:edit', function (event) {
                // alert('edit event for marker');
                // Get the new marker position
                var newLatLng = event.layer.getLatLng();
                let payload = {
                    id: markerRowId,
                    systemId: sid,
                    authId: auth,
                    markerId: marker._leaflet_id,
                    latitude: newLatLng.lat,
                    longitude: newLatLng.lng,
                    reportId: ref
                };

                // TODO: api request to geom/marker.php
                addToApiQueue({ method: 'PUT', gateway: `reports/geometry/marker`, data: payload, tag: 'marker' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.success('Lokasi Pin Telah Berubah!');
                    marker.setIcon(infoIcon);
                }).catch(error => {
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

            marker.on('pm:remove', function () {
                // TODO: api request to geom/marker.php
                addToApiQueue({ method: "DELETE", gateway: `reports/geometry/marker?id=${markerRowId}&markerId=${marker._leaflet_id}&sid=${sid}&ref=${ref}&auth=${auth}`, tag: 'marker' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.error('Pin bergambar Telah dibuang!');
                }).catch(error => {
                    console.log(error);
                });
            });
        });
    }).catch(error => {
        console.log(error);
    });

    // TODO: Polygon
    addToApiQueue({ method: "GET", gateway: `reports/geometry/polygon?action=init&sid=${sid}&ref=${ref}&auth=${auth}`, tag: 'polygon' }).then(response => {
        uiBlockTotal.polygon = response.polygon.length;
        if ((uiBlockTotal.marker == 0) && (uiBlockTotal.polygon == 0) && (uiBlockTotal.polyline == 0)) {
            initLoadBlock.release();
        }
        response.polygon.forEach((element, index) => {
            var dbPolygon = JSON.parse(element.geometry).coordinates[0].map(function (coord) {
                return [coord[1], coord[0]];
            });
            // console.log({ "polygon-each": dbPolygon });
            var polygon = L.polygon(dbPolygon, { color: 'blue' }).addTo(map);
            // console.log({ "polygon-each-instance": polygon });

            // Do event fetch api to update unique _leaflet_id to the db
            let payload = {
                action: "update-geom-init",
                sid: sid,
                ref: ref,
                auth: auth,
                id: element.id,
                geomId: element.geom_id,
                newGeomId: polygon._leaflet_id
            };

            addToApiQueue({ method: 'PUT', gateway: `reports/geometry/polygon`, data: payload , tag: 'polygon'}).then(response => {
                // console.log(response.data);
                // console.log({ "polygon-each-update-geom": response });

                // initializing check
                uiBlockCount.polygon++;
                if (((uiBlockTotal.marker - uiBlockCount.marker) + (uiBlockTotal.polygon - uiBlockCount.polygon) + (uiBlockTotal.polyline - uiBlockCount.polyline)) == 0) {
                    initLoadBlock.release();
                }
            }).catch(error => {
                console.log(error);
            });

            var polygonModalId = 'polygon-' + polygon._leaflet_id;
            let polygonRowId;

            polygon.setStyle({
                color: '#3E97FF'
            });

            // Create the modal HTML using the unique ID
            var modalHtml = `
        <div class="modal fade" data-bs-backdrop="static" id="` + polygonModalId + `" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <form>
                        <div class="modal-body mb-0 form-floating">
                            <div class="form-floating mb-5">
                                <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                id="notes-` + polygonModalId + `" style="height: 100px"></textarea>
                                <label for="notes-` + polygonModalId + `" class="form-label">Catatan</label>
                            </div>
                        </div>
                        <input type="text" id="rowId-` + element.id + `" hidden/>
                        <input type="text" id="action-` + polygonModalId + `" value="edit" hidden/>
                        <div class="modal-footer border-transparent mt-0 pt-0">
                            <button type="button" class="btn  btn-lg btn-active-light-danger text-start" id="delete-` + polygonModalId + `">
                                <i class="fad fa-trash-alt fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-active-light-dark" data-bs-dismiss="modal" id="cancel-` + polygonModalId + `">
                                <i class="fad fa-xmark fs-3"></i>
                            </button>
                            <button type="button" class="btn btn-lg btn-active-light-success" id="save-` + polygonModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-lg btn-active-light-warning" id="update-` + polygonModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

            // Append the modal HTML to the body
            $('body').append(modalHtml);

            // insert rowId retrieved from db
            polygonRowId = element.id;
            $('#rowId-' + polygonModalId).val(polygonRowId);

            // insert recorded description from db into modal input
            $('#notes-' + polygonModalId).val(element.description);

            // Show the modal when the marker is created
            // $('#' + polygonModalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $('#' + polygonModalId).data('polygonId', polygon._leaflet_id);

            let geom = polygon.toGeoJSON();
            // console.log({ "polygon-togeojson": geom });
            // console.log({ "polygon-togeojson-sendToRequest": geom });
            // Handle the "Save" button click
            $('#save-' + polygonModalId).click(function () {
                // TODO: api request to geom/polygon.php
                let payload = {
                    sid: sid,
                    ref: ref,
                    auth: auth,
                    notes: $('#notes-' + polygonModalId).val(),
                    polygonId: polygon._leaflet_id,
                    geom: geom,
                    action: $('#action-' + polygonModalId).val(),
                    id: $('#rowId-' + polygonModalId).val()
                };
                addToApiQueue({ method: "POST", gateway: "reports/geometry/polygon", data: payload, tag: 'polygon' }).then(response => {
                    // console.log({ 'response.data.id': response.data.id });
                    polygonRowId = response.data.id;
                    $('#rowId-' + polygonModalId).val(polygonRowId);

                    toastr.options = optionToast;
                    toastr.success("Poligon Bernota Berjaya Disimpan!");

                    // Get the marker ID from the modal's data attribute
                    var polygonId = $('#' + polygonModalId).data('polygonId');

                    // Find the marker with the given ID
                    var polygon = map._layers[polygonId];

                    // Get the input values from the modal
                    var notes = $('#notes-' + polygonModalId).val();

                    $('#cancel-' + polygonModalId).removeClass("d-none");
                    $('#delete-' + polygonModalId).addClass("d-none");
                    $('#action-' + polygonModalId).val('edit');

                    // Set the marker's popup content
                    polygon.bindPopup(`
                        <div class="d-flex flex-end">
                            <button type="button" class="btn btn-active-light-dark btn-icon btn-edit"
                            data-bs-toggle="modal" data-bs-target="#` + polygonModalId + `">
                                <i class="fad fa-pen-to-square fs-2"></i>
                            </button>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
                        </div>
                        `, {
                        minWidth: 350,
                        closeButton: false,
                    }).openPopup();

                    // Hide the modal
                    $('#' + polygonModalId).modal('hide');
                    polygon.setStyle({
                        color: '#75CC68'
                    });

                })
                    .catch(error => {
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
            var notes = $('#notes-' + polygonModalId).val();

            $('#cancel-' + polygonModalId).removeClass("d-none");
            $('#delete-' + polygonModalId).addClass("d-none");
            $('#action-' + polygonModalId).val('edit');

            // Set the marker's popup content
            polygon.bindPopup(`
            <div class="d-flex flex-end">
                <button type="button" class="btn btn-active-light-dark btn-icon btn-edit"
                data-bs-toggle="modal" data-bs-target="#` + polygonModalId + `">
                    <i class="fad fa-pen-to-square fs-2"></i>
                </button>
            </div>
            <div class="d-flex flex-column">
                <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
            </div>
            `, {
                minWidth: 350,
                closeButton: false,
            });

            // Handle the "Cancel" button click for new markers
            $('#delete-' + polygonModalId).click(function () {
                // Get the marker ID from the modal's data attribute
                var polygonId = $('#' + polygonModalId).data('polygonId');

                // Find the marker with the given ID and remove it from the map
                if (polygonId) {
                    map.removeLayer(map._layers[polygonId]);
                }

                // Hide the modal
                $('#' + polygonModalId).modal('hide');
            });

            // Add a listener for the "pm:edit" event
            polygon.on('pm:edit', function (event) {
                // TODO: api request to geom/polygon.php
                payload = {
                    id: element.id,
                    sid: sid,
                    auth: auth,
                    polygonId: polygon._leaflet_id,
                    geom: event.layer.toGeoJSON(),
                    ref: ref
                };
                // Send an AJAX request to update the marker in the database
                addToApiQueue({ method: 'PUT', gateway: `reports/geometry/polygon`, data: payload, tag: 'polygon' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.success('Poligon Telah Berubah!');
                    polygon.setStyle({
                        color: '#7239ea'
                    });
                })
                    .catch(error => {
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

            polygon.on('pm:remove', function () {
                // TODO: api request to geom/polygon.php
                // Send an AJAX request to delete the marker from the database
                addToApiQueue({ method: "DELETE", gateway: `reports/geometry/polygon?sid=${sid}&ref=${ref}&auth=${auth}&id=${element.id}&polygonId=${polygon._leaflet_id}`, tag: 'polygon' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.error('Poligon Telah dibuang!');
                }).catch(error => {
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
        })
    }).catch(error => {
        console.log(error);
    });

    // TODO: polyline
    addToApiQueue({ method: "GET", gateway: `reports/geometry/polyline?action=init&sid=${sid}&ref=${ref}&auth=${auth}`, tag: 'polyline' }).then(response => {
        uiBlockTotal.polyline = response.polyline.length;
        if ((uiBlockTotal.marker == 0) && (uiBlockTotal.polygon == 0) && (uiBlockTotal.polyline == 0)) {
            initLoadBlock.release();
        }
        response.polyline.forEach((element, index) => {
            var dbLine = JSON.parse(element.geometry).coordinates.map(function (coord) {
                return [coord[1], coord[0]];
            });
            // console.log({ "polyline-each": dbLine });
            var polyline = L.polyline(dbLine, { color: 'red' }).addTo(map);
            // console.log({ "polyline-each-instance": polyline });

            // Do event fetch api to update unique _leaflet_id to the db
            let payload = {
                action: "update-geom-init",
                sid: sid,
                ref: ref,
                auth: auth,
                id: element.id,
                geomId: element.geom_id,
                newGeomId: polyline._leaflet_id
            };
            addToApiQueue({ method: 'PUT', gateway: `reports/geometry/polyline`, data: payload , tag: 'polyline'}).then(response => {
                // console.log(response.data);
                // console.log({ "polyline-each-update-geom": response });

                // initializing check
                uiBlockCount.polyline++;
                if (((uiBlockTotal.marker - uiBlockCount.marker) + (uiBlockTotal.polygon - uiBlockCount.polygon) + (uiBlockTotal.polyline - uiBlockCount.polyline)) == 0) {
                    initLoadBlock.release();
                }
            }).catch(error => {
                console.log(error);
            });

            var polylineModalId = 'line-' + polyline._leaflet_id;

            polyline.setStyle({
                color: '#3E97FF'
            });

            // Create the modal HTML using the unique ID
            var modalHtml = `
        <div class="modal fade" data-bs-backdrop="static" id="` + polylineModalId + `" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <form>
                        <div class="modal-body mb-0 form-floating">
                            <div class="form-floating mb-5">
                                <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                id="notes-` + polylineModalId + `" style="height: 100px"></textarea>
                                <label for="notes-` + polylineModalId + `" class="form-label">Catatan</label>
                            </div>
                        </div>
                        <input type="text" id="rowId-` + element.id + `" hidden/>
                        <input type="text" id="action-` + polylineModalId + `" value="edit" hidden/>
                        <div class="modal-footer border-transparent mt-0 pt-0">
                            <button type="button" class="btn btn-lg btn-active-light-danger text-start" id="delete-` + polylineModalId + `">
                                <i class="fad fa-trash-alt fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-active-light-dark" data-bs-dismiss="modal" id="cancel-` + polylineModalId + `">
                                <i class="fad fa-xmark fs-3"></i>
                            </button>
                            <button type="button" class="btn btn-lg btn-active-light-success" id="save-` + polylineModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-lg btn-active-light-warning" id="update-` + polylineModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

            // Append the modal HTML to the body
            $('body').append(modalHtml);

            // insert recorded description from db into modal input
            $('#notes-' + polylineModalId).val(element.description);

            let polylineRowId;
            polylineRowId = element.id;
            $('#rowId-' + polylineModalId).val(polylineRowId);

            // Show the modal when the marker is created
            // $('#' + polylineModalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $('#' + polylineModalId).data('lineId', polyline._leaflet_id);

            const geoJSON = polyline.toGeoJSON();
            let geom = geoJSON.geometry.coordinates;
            // Handle the "Save" button click
            $('#save-' + polylineModalId).click(function () {
                // TODO: api request to geom/polyline.php
                let payload = {
                    sid: sid,
                    ref: ref,
                    auth: auth,
                    notes: $('#notes-' + polylineModalId).val(),
                    polylineId: polyline._leaflet_id,
                    geom: geom,
                    action: $('#action-' + polylineModalId).val(),
                    id: element.id
                };
                addToApiQueue({ method: 'POST', gateway: `reports/geometry/polyline`, data: payload, tag: 'polyline' }).then((response) => {
                    toastr.options = optionToast;
                    toastr.success("Lukisan Bernota Berjaya Disimpan!");

                    // Get the marker ID from the modal's data attribute
                    var polylineId = $('#' + polylineModalId).data('lineId');

                    // Find the marker with the given ID
                    var polyline = map._layers[polylineId];

                    // Get the input values from the modal
                    var notes = $('#notes-' + polylineModalId).val();

                    $('#cancel-' + polylineModalId).removeClass("d-none");
                    $('#delete-' + polylineModalId).addClass("d-none");
                    $('#action-' + polylineModalId).val('edit');

                    // Set the marker's popup content
                    polyline.bindPopup(`
                    <div class="d-flex flex-end">
                        <button type="button" class="btn btn-active-light-dark btn-icon btn-edit"
                        data-bs-toggle="modal" data-bs-target="#` + polylineModalId + `">
                            <i class="fad fa-pen-to-square fs-2"></i>
                        </button>
                    </div>
                    <div class="d-flex flex-column">
                        <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
                    </div>
                    `, {
                        minWidth: 350,
                        closeButton: false,
                    }).openPopup();

                    // Hide the modal
                    $('#' + polylineModalId).modal('hide');
                    polyline.setStyle({
                        color: '#75CC68'
                    });

                }).catch(error => {
                    console.log(error);
                });
            });

            // initiate bind popup

            // Get the marker ID from the modal's data attribute
            var polylineId = $('#' + polylineModalId).data('lineId');

            // Find the marker with the given ID
            var polyline = map._layers[polylineId];

            // Get the input values from the modal
            var notes = $('#notes-' + polylineModalId).val();

            $('#cancel-' + polylineModalId).removeClass("d-none");
            $('#delete-' + polylineModalId).addClass("d-none");
            $('#action-' + polylineModalId).val('edit');

            // Set the marker's popup content
            polyline.bindPopup(`
            <div class="d-flex flex-end">
                <button type="button" class="btn btn-active-light-dark btn-icon btn-edit"
                data-bs-toggle="modal" data-bs-target="#` + polylineModalId + `">
                    <i class="fad fa-pen-to-square fs-2"></i>
                </button>
            </div>
            <div class="d-flex flex-column">
                <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
            </div>
            `, {
                minWidth: 350,
                closeButton: false,
            })

            // Handle the "Cancel" button click for new markers
            $('#delete-' + polylineModalId).click(function () {
                // Get the marker ID from the modal's data attribute
                var polylineId = $('#' + polylineModalId).data('lineId');

                // Find the marker with the given ID and remove it from the map
                if (polylineId) {
                    map.removeLayer(map._layers[polylineId]);
                }

                // Hide the modal
                $('#' + polylineModalId).modal('hide');
            });

            // Add a listener for the "pm:edit" event
            polyline.on('pm:edit', function (event) {
                // TODO: api request to geom/polyline.php
                let payload = {
                    id: element.id,
                    sid: sid,
                    auth: auth,
                    polylineId: polyline._leaflet_id,
                    geom: event.layer.toGeoJSON().geometry.coordinates,
                    ref: ref
                };
                // Send an AJAX request to update the marker in the database
                addToApiQueue({ method: 'PUT', gateway: `reports/geometry/polyline`, data: payload, tag: 'polyline' }).then((response) => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.success('Garisan Telah Berubah!');
                    polyline.setStyle({
                        color: '#7239ea'
                    });
                }).catch(error => {
                    console.log(error);
                });
            });

            polyline.on('pm:remove', function () {
                // TODO: api request to geom/polyline.php
                // Send an AJAX request to delete the marker from the database
                addToApiQueue({ method: 'DELETE', gateway: `reports/geometry/polyline?sid=${sid}&ref=${ref}&auth=${auth}&id=${element.id}&polylineId=${polyline._leaflet_id}`, tag: 'polyline' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.error('Garisan Telah dibuang!');
                }).catch(error => {
                    console.log(error);
                });
            });
        })

    }).catch(error => {
        console.log(error);
    });
    // TODO: End::Do event fetch api to update saved site visit data

    map.on('pm:drawstart', function (event) {
        // console.log({ "pm:drawstart": event });
        pmDrawingState = true;
    });

    map.on('pm:drawend', function (event) {
        // console.log({ "pm:drawend": event });
        pmDrawingState = false;
    });

    map.on('pm:create', function (event) {
        // console.log({ "pm:create": event });
        if (event.layer instanceof L.Marker) {
            var marker = event.layer;

            marker.setIcon(dangerIcon);

            // Create a unique ID for the modal using the marker's L.stamp property
            var modalId = 'marker-' + marker._leaflet_id;

            // Create the modal HTML using the unique ID
            var modalHtml = `
        <div class="modal fade" data-bs-backdrop="static" id="` + modalId + `" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <form id="form-` + modalId + `">
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
                        <input type="text" id="rowId-` + modalId + `" hidden />
                        <input type="text" id="action-` + modalId + `" value="add" hidden />
                        <div class="modal-body">
                            <div class="mb-0 form-floating fv-row">
                                <textarea  data-kt-autosize="true" rows="4" class="form-control form-control-flush" placeholder="Nyatakan Keterangan"
                                id="desc-` + modalId + `" name="description"></textarea>
                                <label for="desc-` + modalId + `" class="form-label">Keterangan</label>
                            </div>
                        </div>
                        <div class="modal-footer border-transparent mt-0 pt-0">
                            <button type="button" class="btn  btn-lg btn-active-light-danger" id="delete-` + modalId + `">
                                <i class="fad fa-trash-alt fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-active-light-dark" data-bs-dismiss="modal" id="cancel-` + modalId + `">
                                <i class="fad fa-xmark fs-3"></i>
                            </button>
                            <!--begin::Submit-->
                            <button id="save-` + modalId + `" class="btn btn-lg btn-active-light-success flex-shrink-0">
                                <!--begin::Indicator label-->
                                <i class="indicator-label fad fa-floppy-disk fs-2"></i>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>
                            <!--end::Submit-->
                            <button type="button" class="d-none btn btn-lg btn-active-light-warning" id="update-` + modalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

            // Append the modal HTML to the body
            $('body').append(modalHtml);

            // Show the modal when the marker is created
            $('#' + modalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $('#' + modalId).data('markerId', marker._leaflet_id);

            // Initialize Dropzone for the modal
            const latlng = marker.getLatLng();

            // TODO: request to geom/upload.php
            let dropzone = new Dropzone($('#' + modalId).find('.dropzone')[0], {
                url: apps + "/api/reports/geometry/upload",
                paramName: 'file',
                maxFiles: 1,
                acceptedFiles: "image/*",
                thumbnailWidth: 600,
                thumbnailHeight: 600,
                maxFilesize: 1024,
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {

                    formData.append('systemId', sid);
                    formData.append('reportId', ref);
                    formData.append('authId', auth);
                    formData.append('markerId', marker._leaflet_id);
                    formData.append('action', $('#action-' + modalId).val());
                    formData.append('id', $('#rowId-' + modalId).val());
                },
                accept: function (file, done) {
                    done();
                }
            });

            let markerRowId;

            // Handle the "Save" button click
            $('#save-' + modalId).click(function (e) {

                e.preventDefault();
                form = document.querySelector('#form-' + modalId);

                var validator = FormValidation.formValidation(form, {
                    fields: {
                        description: {
                            validators: {
                                notEmpty: {
                                    message: "Sila Isi Keterangan Gambar ini.",
                                },
                            }
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

                // Validate form
                validator.validate().then(function (status) {
                    if (status == "Valid") {
                        // Show loading indication
                        $('#save-' + modalId).attr("data-kt-indicator", "on");

                        // Disable button to avoid multiple click
                        $('#save-' + modalId).prop("disabled", true);
                        if (dropzone.getQueuedFiles().length === 0) {
                            // Hide loading indication
                            $('#save-' + modalId).removeAttr("data-kt-indicator");

                            // Enable button
                            $('#save-' + modalId).prop("disabled", false);

                            toastr.error("Sila muatnaik gambar di tapak!");

                        } else {
                            // TODO: api request geom/marker.phpdata:
                            let payload = {
                                systemId: sid,
                                reportId: ref,
                                authId: auth,
                                description: $('#desc-' + modalId).val(),
                                markerId: marker._leaflet_id,
                                latitude: latlng.lat,
                                longitude: latlng.lng,
                                action: $('#action-' + modalId).val(),
                            };
                            addToApiQueue({ method: 'POST', gateway: `reports/geometry/marker`, data: payload, tag: 'marker' }).then(response => {

                                markerRowId = response.data.id;
                                $('#rowId-' + modalId).val(markerRowId);


                                setTimeout(dropzone.processQueue(), 2000);

                                dropzone.on("success", function (f, r) {
                                    console.log(r.data);

                                    toastr.options = optionToast;

                                    toastr.success("Pin Lokasi Berjaya Disimpan!");

                                    // Get the marker ID from the modal's data attribute
                                    var markerId = $('#' + modalId).data('markerId');

                                    // Find the marker with the given ID
                                    var marker = map._layers[markerId];

                                    // Get the input values from the modal
                                    var description = $('#desc-' + modalId).val();

                                    // Get current timestamp to be patch with image url
                                    var currentImgDate = new Date();
                                    var currentImgTimestamp = currentImgDate.getTime();
                                    var image = r.data.img + '?t=' + currentImgTimestamp;

                                    $('#img-' + modalId).val(image);
                                    $('#action-' + modalId).val('edit');
                                    $('#cancel-' + modalId).removeClass("d-none");
                                    $('#delete-' + modalId).addClass("d-none");
                                    $('#save-' + modalId).addClass("d-none");
                                    $('#update-' + modalId).removeClass("d-none");

                                    // Set the marker's popup content
                                    marker.bindPopup(`
                                    <div class="d-flex flex-end">
                                        <button type="button" class="btn btn-dark btn-icon btn-edit"
                                        data-bs-toggle="modal" data-bs-target="#` + modalId + `">
                                            <i class="fad fa-pen-to-square fs-2"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <img src="` + image + `" class="report-image" />
                                        <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` + description + `</h4>
                                    </div>
                                    `, {
                                        minWidth: 400,
                                        closeButton: false,
                                    });

                                    // Hide loading indication
                                    $('#save-' + modalId).removeAttr("data-kt-indicator");

                                    // Enable button
                                    $('#save-' + modalId).prop("disabled", false);

                                    // Hide the modal
                                    $('#' + modalId).modal('hide');
                                    marker.setIcon(successIcon);
                                    map.pm.disableDraw();
                                });
                            })
                                .catch(error => {
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

                        }
                    }
                });
            });

            // Handle the "Cancel" button click for new markers
            $('#delete-' + modalId).click(function () {
                // Get the marker ID from the modal's data attribute
                var markerId = $('#' + modalId).data('markerId');

                // Find the marker with the given ID and remove it from the map
                if (markerId) {
                    map.removeLayer(map._layers[markerId]);
                }

                // Hide the modal
                $('#' + modalId).modal('hide');
            });

            // Handle the "Cancel" button click for edit mode
            $('#cancel-' + modalId).click(function () {
                // Hide the modal
                $('#' + modalId).modal('hide');
            });

            // Handle the "Update" button click
            $('#update-' + modalId).click(function () {
                // TODO: api request to geom/marker.php
                let payload = {
                    systemId: sid,
                    reportId: ref,
                    authId: auth,
                    description: $('#desc-' + modalId).val(),
                    markerId: marker._leaflet_id,
                    latitude: latlng.lat,
                    longitude: latlng.lng,
                    action: $('#action-' + modalId).val(),
                    url: $('#img-' + modalId).attr('src'),
                    id: markerRowId
                };
                console.log(payload.action);
                addToApiQueue({ method: 'PUT', gateway: `reports/geometry/marker`, data: payload, tag: 'marker' }).then(response => {
                    var queuedFiles = dropzone.getQueuedFiles();
                    if (queuedFiles.length > 0) {
                        dropzone.processQueue();
                        dropzone.on("success", function (file, result) {
                            // Handle success after uploading all files
                            if (dropzone.getQueuedFiles().length === 0 && dropzone.getUploadingFiles().length === 0) {
                                handleSuccess(result);
                            }
                        });
                    } else {
                        handleSuccess(response);
                    }
                }).catch(error => {
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
                    var markerId = $('#' + modalId).data('markerId');

                    // Find the marker with the given ID
                    var marker = map._layers[markerId];

                    // Get the input values from the modal
                    var description = $('#desc-' + modalId).val();

                    // Get current timestamp to be patch with image url
                    var currentImgDate = new Date();
                    var currentImgTimestamp = currentImgDate.getTime();
                    var image = response.data.img + '?t=' + currentImgTimestamp;

                    // Set the marker's popup content (on update success)
                    marker.bindPopup(`
                <div class="d-flex flex-end">
                <button type="button" class="btn btn-dark btn-icon btn-edit"
                    data-bs-toggle="modal" data-bs-target="#` + modalId + `">
                    <i class="fad fa-pen-to-square fs-2"></i>
                </button>
                </div>
                <div class="d-flex flex-column">
                <img src="` + image + `" class="report-image"/>
                <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` + description + `</h4>
                </div>
            `, {
                        minWidth: 400,
                        closeButton: false,
                    }).openPopup();

                    // Hide the modal
                    $('#' + modalId).modal('hide');
                    marker.setIcon(warningIcon);
                }

            });

            // Add a listener for the "pm:edit" event
            marker.on('pm:edit', function (event) {
                // alert('edit event for marker');
                // Get the new marker position
                var newLatLng = event.layer.getLatLng();
                let payload = {
                    id: markerRowId,
                    systemId: sid,
                    authId: auth,
                    markerId: marker._leaflet_id,
                    latitude: newLatLng.lat,
                    longitude: newLatLng.lng,
                    reportId: ref
                };

                // TODO: api request to geom/marker.php
                addToApiQueue({ method: 'PUT', gateway: `reports/geometry/marker`, data: payload, tag: 'marker' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.success('Lokasi Pin Telah Berubah!');
                    marker.setIcon(infoIcon);
                }).catch(error => {
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

            marker.on('pm:remove', function () {
                // TODO: api request to geom/marker.php
                addToApiQueue({ method: 'DELETE', gateway: `reports/geometry/marker?id=${markerRowId}&markerId=${marker._leaflet_id}&sid=${sid}&ref=${ref}&auth=${auth}`, tag: 'marker' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.error('Pin bergambar Telah dibuang!');
                }).catch(error => {
                    console.log(error);
                });
            });


        } else if (event.shape == 'Polygon') {
            var polygon = event.layer;
            // console.log({ "polygon-create": polygon });
            var polygonModalId = 'polygon-' + polygon._leaflet_id;

            polygon.setStyle({
                color: '#3E97FF'
            });

            // Create the modal HTML using the unique ID
            var modalHtml = `
        <div class="modal fade" data-bs-backdrop="static" id="` + polygonModalId + `" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <form>
                        <div class="modal-body mb-0 form-floating">
                            <div class="form-floating mb-5">
                                <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                id="notes-` + polygonModalId + `" style="height: 100px"></textarea>
                                <label for="notes-` + polygonModalId + `" class="form-label">Catatan</label>
                            </div>
                        </div>
                        <input type="text" id="rowId-` + polygonModalId + `" hidden/>
                        <input type="text" id="action-` + polygonModalId + `" value="add" hidden/>
                        <div class="modal-footer border-transparent mt-0 pt-0">
                            <button type="button" class="btn  btn-lg btn-active-light-danger text-start" id="delete-` + polygonModalId + `">
                                <i class="fad fa-trash-alt fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-active-light-dark" data-bs-dismiss="modal" id="cancel-` + polygonModalId + `">
                                <i class="fad fa-xmark fs-3"></i>
                            </button>
                            <button type="button" class="btn btn-lg btn-active-light-success" id="save-` + polygonModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-lg btn-active-light-warning" id="update-` + polygonModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

            // Append the modal HTML to the body
            $('body').append(modalHtml);

            // Show the modal when the marker is created
            $('#' + polygonModalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $('#' + polygonModalId).data('polygonId', polygon._leaflet_id);

            let geom = polygon.toGeoJSON();
            // console.log({ "polygon-togeojson": geom });
            // console.log({ "polygon-togeojson-sendToRequest": geom });
            let polygonRowId;
            // Handle the "Save" button click
            $('#save-' + polygonModalId).click(function () {
                // TODO: api request to geom/polygon.php
                let payload = {
                    sid: sid,
                    ref: ref,
                    auth: auth,
                    notes: $('#notes-' + polygonModalId).val(),
                    polygonId: polygon._leaflet_id,
                    geom: geom,
                    action: $('#action-' + polygonModalId).val(),
                    id: $('#rowId-' + polygonModalId).val()
                };
                addToApiQueue({ method: 'POST', gateway: 'reports/geometry/polygon', data: payload, tag: 'polygon' }).then(response => {
                    // console.log({ "response.data.id": response.data.id });
                    polygonRowId = response.data.id;
                    $('#rowId-' + polygonModalId).val(polygonRowId);

                    toastr.options = optionToast;
                    toastr.success("Poligon Bernota Berjaya Disimpan!");

                    // Get the marker ID from the modal's data attribute
                    var polygonId = $('#' + polygonModalId).data('polygonId');

                    // Find the marker with the given ID
                    var polygon = map._layers[polygonId];

                    // Get the input values from the modal
                    var notes = $('#notes-' + polygonModalId).val();

                    $('#cancel-' + polygonModalId).removeClass("d-none");
                    $('#delete-' + polygonModalId).addClass("d-none");
                    $('#action-' + polygonModalId).val('edit');

                    // Set the marker's popup content
                    polygon.bindPopup(`
                        <div class="d-flex flex-end">
                            <button type="button" class="btn btn-active-light-dark btn-icon btn-edit"
                            data-bs-toggle="modal" data-bs-target="#` + polygonModalId + `">
                                <i class="fad fa-pen-to-square fs-2"></i>
                            </button>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
                        </div>
                        `, {
                        minWidth: 350,
                        closeButton: false,
                    }).openPopup();

                    // Hide the modal
                    $('#' + polygonModalId).modal('hide');
                    polygon.setStyle({
                        color: '#75CC68'
                    });

                })
                    .catch(error => {
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

            // Handle the "Cancel" button click for new markers
            $('#delete-' + polygonModalId).click(function () {
                // Get the marker ID from the modal's data attribute
                var polygonId = $('#' + polygonModalId).data('polygonId');

                // Find the marker with the given ID and remove it from the map
                if (polygonId) {
                    map.removeLayer(map._layers[polygonId]);
                }

                // Hide the modal
                $('#' + polygonModalId).modal('hide');
            });

            // Add a listener for the "pm:edit" event
            polygon.on('pm:edit', function (event) {
                var polygonRowId = $('#rowId-' + polygonModalId).val();

                console.log(polygonRowId);
                // TODO: api request to geom/polygon.php
                payload = {
                    id: polygonRowId,
                    sid: sid,
                    auth: auth,
                    polygonId: polygon._leaflet_id,
                    geom: event.layer.toGeoJSON(),
                    ref: ref
                };
                // Send an AJAX request to update the marker in the database
                addToApiQueue({ method: 'PUT', gateway: 'reports/geometry/polygon', data: payload , tag: 'polygon'}).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.success('Poligon Telah Berubah!');
                    polygon.setStyle({
                        color: '#7239ea'
                    });
                })
                    .catch(error => {
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

            polygon.on('pm:remove', function () {
                var polygonRowId = $('#rowId-' + polygonModalId).val();
                console.log(polygonRowId);
                // TODO: api request to geom/polygon.php
                // Send an AJAX request to delete the marker from the database
                addToApiQueue({ method: 'DELETE', gateway: `reports/geometry/polygon?sid=${sid}&ref=${ref}&auth=${auth}&id=${polygonRowId}&polygonId=${polygon._leaflet_id}`, tag: 'polygon' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.error('Poligon Telah dibuang!');
                }).catch(error => {
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
            map.pm.disableDraw('Polygon');
        } else if (event.layer instanceof L.Polyline) {
            var polyline = event.layer;
            // console.log({ "polyline-create": polyline });
            var polylineModalId = 'line-' + polyline._leaflet_id;

            polyline.setStyle({
                color: '#3E97FF'
            });

            // Create the modal HTML using the unique ID
            var modalHtml = `
        <div class="modal fade" data-bs-backdrop="static" id="` + polylineModalId + `" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <form>
                        <div class="modal-body mb-0 form-floating">
                            <div class="form-floating mb-5">
                                <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                                id="notes-` + polylineModalId + `" style="height: 100px"></textarea>
                                <label for="notes-` + polylineModalId + `" class="form-label">Catatan</label>
                            </div>
                        </div>
                        <input type="text" id="rowId-` + polylineModalId + `" hidden/>
                        <input type="text" id="action-` + polylineModalId + `" value="add" hidden/>
                        <div class="modal-footer border-transparent mt-0 pt-0">
                            <button type="button" class="btn btn-lg btn-active-light-danger text-start" id="delete-` + polylineModalId + `">
                                <i class="fad fa-trash-alt fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-active-light-dark" data-bs-dismiss="modal" id="cancel-` + polylineModalId + `">
                                <i class="fad fa-xmark fs-3"></i>
                            </button>
                            <button type="button" class="btn btn-lg btn-active-light-success" id="save-` + polylineModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                            <button type="button" class="d-none btn btn-lg btn-active-light-warning" id="update-` + polylineModalId + `">
                                <i class="fad fa-floppy-disk fs-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>`;

            // Append the modal HTML to the body
            $('body').append(modalHtml);

            // Show the modal when the marker is created
            $('#' + polylineModalId).modal('show');

            // Set the marker ID as the modal's data attribute
            $('#' + polylineModalId).data('lineId', polyline._leaflet_id);

            const geoJSON = polyline.toGeoJSON();
            // console.log({ "polyline-togeojson": geoJSON });
            let geom = geoJSON.geometry.coordinates;
            // console.log({ "polyline-togeojson-sendToRequest": geom });
            let polylineRowId;
            // Handle the "Save" button click
            $('#save-' + polylineModalId).click(function () {
                // TODO: api request to geom/polyline.php
                let payload = {
                    sid: sid,
                    ref: ref,
                    auth: auth,
                    notes: $('#notes-' + polylineModalId).val(),
                    polylineId: polyline._leaflet_id,
                    geom: geom,
                    action: $('#action-' + polylineModalId).val(),
                    id: $('#rowId-' + polylineModalId).val()
                };
                addToApiQueue({ method: 'POST', gateway: 'reports/geometry/polyline', data: payload, tag: 'polyline' }).then(response => {
                    polylineRowId = response.data.id;
                    $('#rowId-' + polylineModalId).val(polylineRowId);

                    toastr.options = optionToast;
                    toastr.success("Lukisan Bernota Berjaya Disimpan!");

                    // Get the marker ID from the modal's data attribute
                    var polylineId = $('#' + polylineModalId).data('lineId');

                    // Find the marker with the given ID
                    var polyline = map._layers[polylineId];

                    // Get the input values from the modal
                    var notes = $('#notes-' + polylineModalId).val();

                    $('#cancel-' + polylineModalId).removeClass("d-none");
                    $('#delete-' + polylineModalId).addClass("d-none");
                    $('#action-' + polylineModalId).val('edit');

                    // Set the marker's popup content
                    polyline.bindPopup(`
                    <div class="d-flex flex-end">
                        <button type="button" class="btn btn-active-light-dark btn-icon btn-edit"
                        data-bs-toggle="modal" data-bs-target="#` + polylineModalId + `">
                            <i class="fad fa-pen-to-square fs-2"></i>
                        </button>
                    </div>
                    <div class="d-flex flex-column">
                        <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
                    </div>
                    `, {
                        minWidth: 350,
                        closeButton: false,
                    }).openPopup();

                    // Hide the modal
                    $('#' + polylineModalId).modal('hide');
                    polyline.setStyle({
                        color: '#75CC68'
                    });
                }).catch(error => {
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

            // Handle the "Cancel" button click for new markers
            $('#delete-' + polylineModalId).click(function () {
                // Get the marker ID from the modal's data attribute
                var polylineId = $('#' + polylineModalId).data('lineId');

                // Find the marker with the given ID and remove it from the map
                if (polylineId) {
                    map.removeLayer(map._layers[polylineId]);
                }

                // Hide the modal
                $('#' + polylineModalId).modal('hide');
            });

            // Add a listener for the "pm:edit" event
            polyline.on('pm:edit', function (event) {
                var polylineRowId = $('#rowId-' + polylineModalId).val();

                console.log(polylineRowId);
                // TODO: api request to geom/polyline.php
                let payload = {
                    id: polylineRowId,
                    sid: sid,
                    auth: auth,
                    polylineId: polyline._leaflet_id,
                    geom: event.layer.toGeoJSON().geometry.coordinates,
                    ref: ref
                };
                // Send an AJAX request to update the marker in the database
                addToApiQueue({ method: 'POST', gateway: 'reports/geometry/polyline', data: payload, tag: 'polyline' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.success('Garisan Telah Berubah!');
                    polyline.setStyle({
                        color: '#7239ea'
                    });
                }).catch(error => {
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

            polyline.on('pm:remove', function () {
                var polylineRowId = $('#rowId-' + polylineModalId).val();
                console.log(polylineRowId);
                // TODO: api request to geom/polyline.php
                // Send an AJAX request to delete the marker from the database
                addToApiQueue({ method: 'DELETE', gateway: `reports/geometry/polyline?id=${polylineRowId}&sid=${sid}&auth=${auth}&polylineId=${polyline._leaflet_id}&ref=${ref}`, tag: 'polyline' }).then(response => {
                    toastr.options = optionToast;
                    // Show a success message to the user
                    toastr.error('Garisan Telah dibuang!');
                })
                    .catch(error => {
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
        }
    });

    // initrepeater here
    function guestRepeaterInit() {
        // signaturepad counter repeater
        let signCountInitRepeater = 4;
        let signaturePadIdInitRepeater;
        let signCountInit = 4;
        let signaturePadIdInit;
        if (signCountInit < 10) {
            signaturePadIdInit = `signature-pad-0${signCountInit}`;
        } else {
            signaturePadIdInit = `signature-pad-${signCountInit}`;
        }

        // initialize repeater
        const guestRepeaterAddBtn = $('#guest-btn-create');
        var guestRepeater = $('#guest-involved').repeater({
            initEmpty: true,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function () {
                $(this).slideDown();

                // signature generate and initialization
                const signatureInputEl = $(this).find('[data-report-repeater="guest-signature"]');
                const signatureModalButton = signatureInputEl.prev();
                if (signatureInputEl.val() !== '') {
                    signatureModalButton.removeClass('btn-light-primary');
                    signatureModalButton.addClass('btn-secondary');
                    signatureModalButton.attr('disabled', true);
                }

                // listen on signatureModalButton click event
                signatureModalButton.click(function () {
                    console.log('signatureModal button click event fired');
                    initModalAlertStatus = false;
                });

                // Integrate signature_pad initialization here
                var tagName = 'div'; // The HTML tag name of the element to create

                if (signCountInitRepeater < 10) {
                    signaturePadIdInitRepeater = `signature-pad-0${signCountInitRepeater}`;
                } else {
                    signaturePadIdInitRepeater = `signature-pad-${signCountInitRepeater}`;
                }

                var attributes = {
                    'class': 'modal fade report-signature-modal',
                    'tabindex': "-1",
                    'id': signaturePadIdInitRepeater
                };
                var newElement = document.createElement(tagName);
                for (var key in attributes) {
                    if (attributes.hasOwnProperty(key)) {
                        newElement.setAttribute(key, attributes[key]);
                    }
                }

                var parentForNewEl = initModalEl.parentElement;

                parentForNewEl.appendChild(newElement);
                newElement.innerHTML = `<div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tandatangan Pegawai</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div name="signature-pad" class="signature-pad m-auto">
                        <div class="signature-pad--body">
                            <canvas></canvas>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" data-report-button="saveSignature">Simpan</button>
                </div>
            </div>
        </div>`;

                // Set modal button attributes for signature-pad
                $(this).find('[data-report-repeater="signature-modal-button"]').attr({
                    'data-bs-toggle': 'modal',
                    'data-bs-target': '#' + signaturePadIdInitRepeater
                });
                signCountInitRepeater++;
                while (document.getElementById(signaturePadIdInit)) {
                    const signModal = document.getElementById(signaturePadIdInit);
                    // const signModal = document.getElementById("signature-pad-01");
                    const canvas = signModal.querySelector("canvas");
                    const saveButton = signModal.querySelector('[data-report-button="saveSignature"]');
                    const signaturePad = new SignaturePad(canvas, {
                        // It's Necessary to use an opaque color when saving image as JPEG;
                        // this option can be omitted if only saving as PNG or SVG
                        // backgroundColor: 'rgb(255, 255, 255)'
                    });

                    // Adjust canvas coordinate space taking into account pixel ratio,
                    // to make it look crisp on mobile devices.
                    // This also causes canvas to be cleared.
                    function resizeCanvas() {
                        // When zoomed out to less than 100%, for some very strange reason,
                        // some browsers report devicePixelRatio as less than 1
                        // and only part of the canvas is cleared then.
                        const ratio = Math.max(window.devicePixelRatio || 1, 1);

                        // This part causes the canvas to be cleared
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);

                        // This library does not listen for canvas changes, so after the canvas is automatically
                        // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
                        // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
                        // that the state of this library is consistent with visual state of the canvas, you
                        // have to clear it manually.
                        signaturePad.clear();

                        // If you want to keep the drawing on resize instead of clearing it you can reset the data.
                        // signaturePad.fromData(signaturePad.toData());
                    }

                    // On mobile devices it might make more sense to listen to orientation change,
                    // rather than window resize events.
                    window.addEventListener('resize', resizeCanvas);
                    signModal.addEventListener('shown.bs.modal', function () {
                        resizeCanvas();
                    });

                    resizeCanvas();

                    saveButton.addEventListener("click", () => {
                        if (signaturePad.isEmpty()) {
                            alert("Sila turunkan tandatangan dahulu.");
                        } else {
                            const dataURL = signaturePad.toDataURL();// Process the signature data
                            processSignatureData(dataURL).then((croppedDataURL) => {
                                // Use the croppedDataURL as needed (e.g., save or further process it)
                                signatureInputEl.val(croppedDataURL);
                                // // console.log({ "original": dataURL, "converted": signatureInputEl.val() })

                                // TODO: make api request to save data here
                                let payload = {
                                    sid: sid,
                                    rn: ref,
                                    aid: auth,
                                    item: "report-signature-img",
                                    data: {
                                        signImg: croppedDataURL,
                                        signId: guestSignatureIdEl.val()
                                    }
                                };
                                addToApiQueue({ method: "POST", gateway: `reports/summary/utilities`, data: payload }).then(function (response) {
                                    console.log(response);
                                    toastr.success(response.message);
                                    guestSignatureEl.val(response.data.signature);
                                }).catch(function (error) {
                                    console.log(error);
                                });

                                // Change signature button
                                signatureModalButton.removeClass('btn-light-primary');
                                signatureModalButton.addClass('btn-secondary');
                                signatureModalButton.attr('disabled', true);

                                // Hide modal
                                signModal.style.display = 'none';
                                document.querySelector('.modal-backdrop').classList.remove('show');
                                bodyEl.style.overflow = '';
                                bodyEl.style.paddingRight = '';
                                bodyEl.classList.remove('modal-open');
                                document.querySelector('.modal-backdrop').remove();
                                // open initSetupModal
                                initModal.show();
                            }).catch((error) => {
                                console.error(error);
                            })
                        }
                    });

                    function processSignatureData(dataURL) {
                        return new Promise((resolve, reject) => {
                            // Create an Image object to load the signature data
                            const image = new Image();
                            image.src = dataURL;

                            image.onload = function () {
                                // Create a canvas to perform image processing operations
                                const tempCanvas = document.createElement('canvas');
                                const tempContext = tempCanvas.getContext('2d');

                                // Find the signature bounding box
                                const boundingBox = findSignatureBoundingBox(image);

                                // Calculate the width and height of the bounding box
                                const width = boundingBox.maxX - boundingBox.minX;
                                const height = boundingBox.maxY - boundingBox.minY;

                                // Draw the signature data onto the temporary canvas, preserving the signature position
                                tempCanvas.width = width;
                                tempCanvas.height = height;
                                tempContext.drawImage(
                                    image,
                                    boundingBox.minX,
                                    boundingBox.minY,
                                    width,
                                    height,
                                    0,
                                    0,
                                    width,
                                    height
                                );

                                // Get the cropped signature data as a base64 encoded PNG
                                const croppedDataURL = tempCanvas.toDataURL('image/png');

                                // Resolve the Promise with the croppedDataURL
                                resolve(croppedDataURL);
                            };

                            image.onerror = function () {
                                // In case of an error, reject the Promise
                                reject(new Error('Failed to load the image.'));
                            };
                        });
                    }

                    function findSignatureBoundingBox(image) {
                        // Get the pixel data of the image
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.width = image.width;
                        canvas.height = image.height;
                        context.drawImage(image, 0, 0);
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        const pixels = imageData.data;

                        // Find the minimum and maximum coordinates of the non-transparent pixels
                        let minX = canvas.width;
                        let minY = canvas.height;
                        let maxX = 0;
                        let maxY = 0;
                        for (let i = 0; i < pixels.length; i += 4) {
                            const alpha = pixels[i + 3];
                            if (alpha > 0) {
                                const x = (i / 4) % canvas.width;
                                const y = Math.floor((i / 4) / canvas.width);
                                minX = Math.min(minX, x);
                                minY = Math.min(minY, y);
                                maxX = Math.max(maxX, x);
                                maxY = Math.max(maxY, y);
                            }
                        }

                        // Return the bounding box coordinates
                        return {
                            minX,
                            minY,
                            maxX,
                            maxY
                        };
                    }

                    signCountInit++;
                    if (signCountInit < 10) {
                        signaturePadIdInit = `signature-pad-0${signCountInit}`;
                    } else {
                        signaturePadIdInit = `signature-pad-${signCountInit}`;
                    }
                }

                // autosave controls
                const guestNameEl = $(this).find('[data-report-repeater="guest-name"]');
                const guestCompanyEl = $(this).find('[data-report-repeater="guest-company"]');
                const guestPositionEl = $(this).find('[data-report-repeater="guest-position"]');
                const guestSignatureEl = $(this).find('[data-report-repeater="guest-signature"]');
                const guestTypeEl = $(this).find('[data-report-repeater="guest-type"]');
                const guestSignatureIdEl = $(this).find('[data-report-repeater="guest-signature-id"]');
                const guestContactIdEl = $(this).find('[data-report-repeater="guest-contact-id"]');
                var inputSaveTimer;

                // function to get values for the current repeater item
                function getRepeaterItemValues(repeaterItem) {
                    const values = {};

                    repeaterItem.find('[data-report-repeater]').each(function () {
                        const dataType = $(this).data('report-repeater');
                        let value;

                        if ($(this).is('input') || $(this).is('select')) {
                            if ($(this).is('[data-report-repeater="select2"]')) {
                                // For select2 elements, use the select2('data') method to get the selected data
                                value = $(this).select2('data').map(item => item.id);
                            } else {
                                value = $(this).val().trim();
                            }
                        } else if ($(this).is(':checkbox')) {
                            value = $(this).is(':checked');
                        } else {
                            value = $(this).text().trim();
                        }

                        values[dataType] = value;
                    });

                    return values;
                }

                // Function to check if all fields, except the signature, are filled
                function checkAllInputFilled() {
                    return guestNameEl.val().trim() !== '' && guestCompanyEl.val().trim() !== '' && guestPositionEl.val().trim() !== '';
                }

                // function to trigger data save request
                let isSaving = false;
                function saveDataToDatabase() {
                    if (isSaving) {
                        return; // Prevent additional save attempts
                    }

                    isSaving = true;

                    // Disable input fields
                    // guestNameEl.prop('disabled', true);
                    // guestCompanyEl.prop('disabled', true);
                    // guestPositionEl.prop('disabled', true);

                    let repeaterItem = guestNameEl.closest('[data-repeater-item]');
                    let repeaterVal = getRepeaterItemValues(repeaterItem);
                    let payload = {
                        sid: sid,
                        rn: ref,
                        aid: auth,
                        item: "guest-list-single",
                        data: repeaterVal
                    };

                    addToApiQueue({ method: "POST", gateway: `reports/summary/utilities`, data: payload }).then(function (response) {
                        console.log(response);
                        guestSignatureIdEl.val(response.data.sign_id);
                        guestContactIdEl.val(response.data.id);
                        toastr.success(response.message);
                    }).catch(function (error) {
                        console.log(error);
                    }).finally(function () {
                        isSaving = false;
                        // Re-enable input fields
                        // guestNameEl.prop('disabled', false);
                        // guestCompanyEl.prop('disabled', false);
                        // guestPositionEl.prop('disabled', false);
                    });
                }


                guestNameEl.on('input change', function () {
                    if (checkAllInputFilled()) {
                        clearTimeout(inputSaveTimer);
                        inputSaveTimer = setTimeout(saveDataToDatabase, 100);
                    }
                })

                guestCompanyEl.on('input change', function () {
                    if (checkAllInputFilled()) {
                        clearTimeout(inputSaveTimer);
                        inputSaveTimer = setTimeout(saveDataToDatabase, 100);
                    }
                })

                guestPositionEl.on('input change', function () {
                    if (checkAllInputFilled()) {
                        clearTimeout(inputSaveTimer);
                        inputSaveTimer = setTimeout(saveDataToDatabase, 100);
                    }
                })

                // TODO: check repeater value
                // console.log({'guestRepeater.repeaterVal()': guestRepeater.repeaterVal()});

                // Trigger the change event manually after adding the repeater item
                // document.getElementById('reportSummary').dispatchEvent(new Event('change'));
            },

            hide: function (deleteElement) {
                function getRepeaterItemValues(repeaterItem) {
                    const values = {};

                    repeaterItem.find('[data-report-repeater]').each(function () {
                        const dataType = $(this).data('report-repeater');
                        let value;

                        if ($(this).is('input') || $(this).is('select')) {
                            if ($(this).is('[data-report-repeater="select2"]')) {
                                // For select2 elements, use the select2('data') method to get the selected data
                                value = $(this).select2('data').map(item => item.id);
                            } else {
                                value = $(this).val().trim();
                            }
                        } else if ($(this).is(':checkbox')) {
                            value = $(this).is(':checked');
                        } else {
                            value = $(this).text().trim();
                        }

                        values[dataType] = value;
                    });

                    return values;
                }

                let repeaterItem = $(this).closest('[data-repeater-item]');
                // let repeaterVal = roadRepeater.repeaterVal(repeaterItem);
                let repeaterVal = getRepeaterItemValues(repeaterItem);

                console.log(repeaterVal);

                // Check if guest-contact-id is null
                if (repeaterVal['guest-contact-id'] === "") {
                    // If guest-contact-id is null, simply remove the element without making an API call
                    $(this).slideUp(function () {
                        deleteElement();
                    });
                    return; // Exit the function
                }

                if (confirm('Anda pasti untuk hapus individu ini dari Senarai Kehadiran?')) {
                    $(this).slideUp(function () {
                        // Call deleteElement() to remove the element from the DOM
                        deleteElement();

                        // The callback function will be executed after the slideUp animation is complete,
                        // ensuring that the element is removed from the DOM.

                        // Now that the element is removed from the DOM, you can directly use repeaterVal() to get the updated data

                        addToApiQueue({ method: "DELETE", gateway: `reports/summary/utilities?item=guestList&sid=${sid}&rn=${ref}&aid=${auth}&gid=${repeaterVal["guest-signature-id"]}` }).then(function (response) {
                            console.log(response);
                            toastr.success(response.message);
                        }).catch(function (error) {
                            console.log(error);
                        });
                    });
                }
            },

            ready: function () {
                // Init select2
                $('[data-report-repeater="select2"]').select2();
            },

            isFirstItemUndeletable: false
        });

        guestRepeaterAddBtn.click(() => {
            guestRepeater.find('[data-repeater-create]').trigger('click');
        });

        // manipulate repeater
        var guestData = [];
        var rawGuestData;

        // TODO: axios request to backend to obtain guest list
        addToApiQueue({ method: "GET", gateway: `reports/summary/utilities?item=guestList&sid=${sid}&rn=${ref}&aid=${auth}` }).then((response) => {
            // console.log({ guests: response });
            rawGuestData = { guests: response };

            // Loop through the JSON data and add each item to the repeater
            $.each(rawGuestData.guests, function (index, guest) {

                // Push roads info as a separate object into the array
                guestData.push({
                    "guest-name": guest.full_name,
                    "guest-company": guest.company_name,
                    "guest-position": guest.position,
                    "guest-signature": guest.signature,
                    "guest-type": guest.sign_type,
                    "guest-signature-id": guest.id,
                    "guest-contact-id": guest.contact_id
                });
            });

            // set the list to repeater items
            guestRepeater.setList(guestData);
        }).catch((error) => {
            console.log(error);
        });


    }

    guestRepeaterInit();
    initModal.show();
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
    // block the map
    initLoadBlock.block();
    // init the map
    mapSetup();
});