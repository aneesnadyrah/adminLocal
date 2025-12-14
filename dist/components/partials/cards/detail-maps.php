<?php 
/**
 * Unified Project Details Maps - Simple Design with Enhanced Map Functionality
 * Uses the new unified ProjectDetails class with auto-detection
 * Bootstrap 5 Tab Integration + Enhanced Map Features
 */

$systemId = $_GET['id'] ?? null;

// Initialize the unified ProjectDetails class
$projectDetails = new ProjectDetails();

// Initialize data
$projectData = null;
$roadData = [];

try {
    if (!empty($systemId)) {
        $projectData = $projectDetails->getProjectDetails($systemId);
        $roadData = $projectDetails->getRoadDetails($systemId) ?: [];
    }
} catch (Exception $e) {
    error_log("Error fetching maps data: " . $e->getMessage());
}

// Calculate map center
$centerLat = 3.139;
$centerLng = 101.687;

if (!empty($roadData)) {
    $latitudes = [];
    $longitudes = [];
    
    foreach ($roadData as $road) {
        if (!empty($road->coor_start ?? '')) {
            $startCoords = explode(', ', $road->coor_start);
            if (count($startCoords) >= 2) {
                $latitudes[] = floatval(trim($startCoords[0]));
                $longitudes[] = floatval(trim($startCoords[1]));
            }
        }
        
        if (!empty($road->coor_end ?? '')) {
            $endCoords = explode(', ', $road->coor_end);
            if (count($endCoords) >= 2) {
                $latitudes[] = floatval(trim($endCoords[0]));
                $longitudes[] = floatval(trim($endCoords[1]));
            }
        }
    }
    
    if (!empty($latitudes) && !empty($longitudes)) {
        $centerLat = array_sum($latitudes) / count($latitudes);
        $centerLng = array_sum($longitudes) / count($longitudes);
    }
}

?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!--begin::Map Section-->
<div class="accordion mt-5" id="kt_accordion_map">
    <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
        <div class="accordion-item">
            <h2 class="accordion-header" id="kt_accordion_map_header">
                <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#kt_accordion_map_body" aria-expanded="true" aria-controls="kt_accordion_map_body">
                    Peta Permohonan
                    <?php if ($projectData && !empty($projectData->reference_no)) { ?>
                        - <?php echo htmlspecialchars($projectData->reference_no) ?>
                    <?php } ?>
                </button>
            </h2>
            <div id="kt_accordion_map_body" class="accordion-collapse collapse show" 
                 aria-labelledby="kt_accordion_map_header" data-bs-parent="#kt_accordion_map">
                <div class="accordion-body">
                    
                    <!--begin::Map Info and Controls-->
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div class="text-muted fs-6">
                            <?php if (!empty($roadData)) { ?>
                                <?php echo count($roadData) ?> jalan direkodkan
                            <?php } else { ?>
                                Tiada data jalan tersedia
                            <?php } ?>
                        </div>
                        
                        <?php if (!empty($roadData)) { ?>
                        <div class="d-flex gap-2">
                            <button id="map-toggle-roads" class="btn btn-sm btn-secondary">
                                Hide Roads
                            </button>
                            <button id="map-layer-toggle" class="btn btn-sm btn-light">
                                Satellite
                            </button>
                        </div>
                        <?php } ?>
                    </div>
                    <!--end::Map Info and Controls-->

                    <!--begin::Map Container-->
                    <div id="gpkg-map" style="height: 450px; border: 1px solid #e1e5e9; border-radius: 0.475rem;">
                        <div id="map-loading" class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading map...</div>
                            </div>
                        </div>
                        
                        <div id="map-error" class="d-none d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <div class="text-muted mb-3">Error loading map</div>
                                <button class="btn btn-sm btn-primary" onclick="initializeMap()">Retry</button>
                            </div>
                        </div>
                    </div>
                    <!--end::Map Container-->
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Map Section-->

<!--begin::Road Table-->
<?php if (!empty($roadData) && is_array($roadData)) { ?>
    <div class="accordion mt-5" id="kt_accordion_roads">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_roads_header">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_roads_body" aria-expanded="true" aria-controls="kt_accordion_roads_body">
                        Maklumat Jalan (<?php echo count($roadData) ?> rekod)
                    </button>
                </h2>
                <div id="kt_accordion_roads_body" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_roads_header" data-bs-parent="#kt_accordion_roads">
                    <div class="accordion-body">
                        
                        <!--begin::Simple Stats-->
                        <div class="row mb-5">
                            <div class="col-md-4 text-center">
                                <div class="fs-2 fw-bold text-dark"><?php echo count($roadData) ?></div>
                                <div class="text-muted fs-6">Total Jalan</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="fs-2 fw-bold text-dark">
                                    <?php 
                                    $totalLength = array_sum(array_column($roadData, 'road_length'));
                                    echo number_format($totalLength, 0) . 'm';
                                    ?>
                                </div>
                                <div class="text-muted fs-6">Total Jarak</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="fs-2 fw-bold text-dark">
                                    <?php 
                                    $uniqueMethods = array_unique(array_column($roadData, 'methods'));
                                    echo count(array_filter($uniqueMethods));
                                    ?>
                                </div>
                                <div class="text-muted fs-6">Kaedah</div>
                            </div>
                        </div>
                        <!--end::Simple Stats-->
                        
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th>Nama Jalan</th>
                                        <th>Jarak (m)</th>
                                        <th>Kaedah</th>
                                        <th>Koordinat Awal</th>
                                        <th>Koordinat Akhir</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roadData as $index => $road) { 
                                        $roadName = $road->road_name ?? 'Unknown Road';
                                        $roadLength = $road->road_length ?? 0;
                                        $coorStart = $road->coor_start ?? '';
                                        $coorEnd = $road->coor_end ?? '';
                                        
                                        $methods = ($road->methods ?? '') . " (" . ($road->methods_shortform ?? '') . ")";

                                        ?>
                                        <tr id="road-row-<?php echo $index ?>" data-road-index="<?php echo $index ?>">
                                            <td class="fw-semibold"><?php echo htmlspecialchars(strtoupper($roadName)) ?></td>
                                            <td><?php echo number_format($roadLength, 0) ?>m</td>
                                            <td class="text-muted"><?php echo htmlspecialchars($methods) ?></td>
                                            <td class="text-success"><?php echo htmlspecialchars($coorStart) ?></td>
                                            <td class="text-danger"><?php echo htmlspecialchars($coorEnd) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="focusOnRoad(<?php echo $index ?>)">
                                                    Focus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <!--begin::No Data-->
    <div class="accordion mt-5" id="kt_accordion_no_roads">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_no_roads_header">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_no_roads_body" aria-expanded="true" aria-controls="kt_accordion_no_roads_body">
                        Maklumat Jalan
                    </button>
                </h2>
                <div id="kt_accordion_no_roads_body" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_no_roads_header" data-bs-parent="#kt_accordion_no_roads">
                    <div class="accordion-body text-center py-10">
                        <div class="text-muted fs-4 mb-3">Tiada Data Jalan</div>
                        <div class="text-muted fs-6 mb-5">Belum ada maklumat jalan yang direkodkan.</div>
                        
                        <div class="border rounded p-4">
                            <div class="text-muted mb-3">Sila rujuk dokumen BKIL</div>
                            <a class="btn btn-primary" 
                               data-fslightbox="lightbox-bkil" 
                               href="#pdf-bkil-<?php echo htmlspecialchars($systemId ?? '') ?>">
                                Lihat Dokumen
                            </a>
                            
                            <div style="display: none;">
                                <div id="pdf-bkil-<?php echo htmlspecialchars($systemId ?? '') ?>" 
                                     style="height: 100vh; width: 95vw">
                                    <iframe class="w-100 h-100" 
                                            src="/attachments/bkil/<?php echo htmlspecialchars($systemId ?? '') ?>.pdf"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!--end::Road Table-->

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
let map;
let markers = [];
let polylines = [];
let markerLayer;
let polylineLayer;
let roadData = <?php echo json_encode($roadData) ?>;
let centerLat = <?php echo $centerLat ?>;
let centerLng = <?php echo $centerLng ?>;

let currentBaseLayer = 'OpenStreetMap';
let baseLayers = {};
let mapInitialized = false;

// Bootstrap 5 Tab Support - Re-initialize map when tab becomes active
document.addEventListener('DOMContentLoaded', function() {
    // Listen for Bootstrap 5 tab events - Maps tab is #tab_2
    const mapTabTrigger = document.querySelector('a[href="#tab_2"]');
    
    if (mapTabTrigger) {
        console.log('Map tab trigger found, setting up event listener');
        
        // Use Bootstrap 5 tab event
        mapTabTrigger.addEventListener('shown.bs.tab', function (e) {
            console.log('Maps tab activated via Bootstrap 5 event');
            
            // Small delay to ensure DOM is ready
            setTimeout(function() {
                if (!mapInitialized) {
                    console.log('Initializing map for first time');
                    initializeMap();
                } else if (map) {
                    console.log('Map exists, invalidating size');
                    map.invalidateSize();
                    
                    // Re-fit map to roads if available
                    if (roadData.length > 0) {
                        setTimeout(() => {
                            fitMapToRoads();
                        }, 100);
                    }
                }
            }, 150); // Slightly longer delay for tab animation
        });
        
        // Also listen for the old Bootstrap event as fallback
        mapTabTrigger.addEventListener('show.bs.tab', function (e) {
            console.log('Maps tab showing (fallback event)');
        });
        
    } else {
        console.log('No map tab trigger found, initializing map immediately');
        // If not in tabs, initialize immediately
        setTimeout(initializeMap, 100);
    }
    
    // Also check if we're already on the maps tab
    const mapsTabPane = document.getElementById('tab_2');
    if (mapsTabPane && mapsTabPane.classList.contains('active')) {
        console.log('Maps tab already active on load');
        setTimeout(initializeMap, 200);
    }
});

function initializeMap() {
    const loadingDiv = document.getElementById('map-loading');
    const errorDiv = document.getElementById('map-error');
    
    try {
        console.log('Starting map initialization...');
        
        // Hide error state
        if (errorDiv) errorDiv.classList.add('d-none');
        
        // Show loading state
        if (loadingDiv) loadingDiv.style.display = 'flex';
        
        // Remove existing map if any
        if (map) {
            console.log('Removing existing map');
            map.remove();
            map = null;
        }
        
        // Clear arrays
        markers = [];
        polylines = [];
        
        // Initialize map with better error handling
        const mapContainer = document.getElementById('gpkg-map');
        if (!mapContainer) {
            throw new Error('Map container not found');
        }
        
        // Ensure container has proper dimensions
        if (mapContainer.offsetHeight === 0) {
            console.log('Map container has no height, setting minimum height');
            mapContainer.style.height = '450px';
        }
        
        console.log('Creating Leaflet map...');
        map = L.map('gpkg-map', {
            preferCanvas: true, // Better performance
            zoomControl: true,
            attributionControl: true
        }).setView([centerLat, centerLng], roadData.length > 0 ? 13 : 10);
        
        // Setup layers
        baseLayers['OpenStreetMap'] = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        });
        
        baseLayers['Satellite'] = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri',
            maxZoom: 17
        });
        
        // Add default layer
        baseLayers['OpenStreetMap'].addTo(map);
        
        // Create layer groups
        markerLayer = L.layerGroup().addTo(map);
        polylineLayer = L.layerGroup().addTo(map);
        
        // Add road data if available
        if (roadData.length > 0) {
            console.log('Adding road data to map...');
            addRoadMarkersAndLines();
            fitMapToRoads();
        } else {
            console.log('No road data to add');
        }
        
        // Add controls
        addMapControls();
        
        // Map ready event
        map.whenReady(() => {
            console.log('Map is ready');
            mapInitialized = true;
            
            // Hide loading spinner
            setTimeout(() => {
                if (loadingDiv) loadingDiv.style.display = 'none';
            }, 500);
        });
        
        console.log('Map initialization completed');
        
    } catch (error) {
        console.error('Map initialization error:', error);
        
        // Always hide loading on error
        if (loadingDiv) loadingDiv.style.display = 'none';
        
        // Show error state
        if (errorDiv) errorDiv.classList.remove('d-none');
        
        mapInitialized = false;
    }
}

function addRoadMarkersAndLines() {
    roadData.forEach((road, index) => {
        if (road.coor_start && road.coor_end) {
            const startCoords = road.coor_start.split(', ');
            const endCoords = road.coor_end.split(', ');
            
            if (startCoords.length >= 2 && endCoords.length >= 2) {
                const startPos = [parseFloat(startCoords[0]), parseFloat(startCoords[1])];
                const endPos = [parseFloat(endCoords[0]), parseFloat(endCoords[1])];
                
                // Validate coordinates
                if (isNaN(startPos[0]) || isNaN(startPos[1]) || isNaN(endPos[0]) || isNaN(endPos[1])) {
                    console.warn(`Invalid coordinates for road ${index}:`, road);
                    return;
                }
                
                const startIcon = L.divIcon({
                    html: '<div class="simple-marker start-marker">S</div>',
                    className: 'custom-marker',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                
                const endIcon = L.divIcon({
                    html: '<div class="simple-marker end-marker">E</div>',
                    className: 'custom-marker',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                
                const startMarker = L.marker(startPos, { icon: startIcon })
                    .bindPopup(createSimplePopup(road, 'Start'))
                    .addTo(markerLayer);
                
                const endMarker = L.marker(endPos, { icon: endIcon })
                    .bindPopup(createSimplePopup(road, 'End'))
                    .addTo(markerLayer);
                
                const polyline = L.polyline([startPos, endPos], {
                    color: '#3B82F6',
                    weight: 3,
                    opacity: 0.7
                }).bindPopup(createSimplePopup(road, 'Road')).addTo(polylineLayer);
                
                markers.push(startMarker, endMarker);
                polylines.push(polyline);
            }
        }
    });
}

function createSimplePopup(road, type) {
    return `
        <div style="padding: 10px; min-width: 200px;">
            <h6 style="margin: 0 0 10px 0; font-weight: bold;">${road.road_name} - ${type}</h6>
            <div style="margin-bottom: 5px;"><strong>Length:</strong> ${road.road_length}m</div>
            <div style="margin-bottom: 5px;"><strong>Methods:</strong> ${road.methods || 'N/A'}</div>
            <div><strong>Coordinates:</strong><br>
                Start: ${road.coor_start}<br>
                End: ${road.coor_end}
            </div>
        </div>
    `;
}

function fitMapToRoads() {
    if (markers.length > 0 && map) {
        try {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
            
            if (map.getZoom() > 16) {
                map.setZoom(16);
            }
        } catch (error) {
            console.error('Error fitting map to roads:', error);
        }
    }
}

function focusOnRoad(roadIndex) {
    if (!roadData[roadIndex] || !map) return;
    
    const road = roadData[roadIndex];
    const startCoords = road.coor_start.split(', ');
    const endCoords = road.coor_end.split(', ');
    
    if (startCoords.length >= 2 && endCoords.length >= 2) {
        const startPos = [parseFloat(startCoords[0]), parseFloat(startCoords[1])];
        const endPos = [parseFloat(endCoords[0]), parseFloat(endCoords[1])];
        
        const bounds = L.latLngBounds([startPos, endPos]);
        map.fitBounds(bounds.pad(0.2));
        
        // Highlight row
        document.querySelectorAll('[data-road-index]').forEach(row => {
            row.style.backgroundColor = '';
        });
        const targetRow = document.querySelector(`[data-road-index="${roadIndex}"]`);
        if (targetRow) {
            targetRow.style.backgroundColor = '#f8f9fa';
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        if (polylines[roadIndex]) {
            setTimeout(() => {
                polylines[roadIndex].openPopup();
            }, 300);
        }
    }
}

function addMapControls() {
    const toggleBtn = document.getElementById('map-toggle-roads');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (!map) return;
            
            const isVisible = map.hasLayer(markerLayer);
            
            if (isVisible) {
                map.removeLayer(markerLayer);
                map.removeLayer(polylineLayer);
                this.textContent = 'Show Roads';
                this.className = 'btn btn-sm btn-primary';
            } else {
                map.addLayer(markerLayer);
                map.addLayer(polylineLayer);
                this.textContent = 'Hide Roads';
                this.className = 'btn btn-sm btn-secondary';
            }
        });
    }
    
    const layerBtn = document.getElementById('map-layer-toggle');
    if (layerBtn) {
        layerBtn.addEventListener('click', function() {
            if (!map) return;
            
            if (currentBaseLayer === 'OpenStreetMap') {
                map.removeLayer(baseLayers['OpenStreetMap']);
                baseLayers['Satellite'].addTo(map);
                currentBaseLayer = 'Satellite';
                this.textContent = 'Street Map';
            } else {
                map.removeLayer(baseLayers['Satellite']);
                baseLayers['OpenStreetMap'].addTo(map);
                currentBaseLayer = 'OpenStreetMap';
                this.textContent = 'Satellite';
            }
        });
    }
}

// Force map refresh function for debugging
function refreshMap() {
    console.log('Force refreshing map...');
    mapInitialized = false;
    setTimeout(() => {
        initializeMap();
    }, 100);
}

// Global function to manually trigger map initialization (for debugging)
window.debugInitMap = function() {
    console.log('Debug: Manual map initialization');
    mapInitialized = false;
    initializeMap();
};

console.log('Map script loaded. Map data length:', roadData.length);
</script>

<style>
.custom-marker {
    background: transparent !important;
    border: none !important;
}

.simple-marker {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.start-marker {
    background-color: #22c55e;
}

.end-marker {
    background-color: #ef4444;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
}

.leaflet-popup-content {
    margin: 0;
}

/* Ensure map container maintains proper size */
#gpkg-map {
    min-height: 450px;
    position: relative;
}

/* Table row highlight */
tr[data-road-index]:hover {
    background-color: #f8f9fa !important;
    transition: all 0.2s ease;
}

.highlighted-row {
    transition: all 0.3s ease !important;
}

/* Clean Popup Design */
.clean-popup {
    width: 280px;
    background: white;
    border-radius: 8px;
    font-family: inherit;
    overflow: hidden;
}

.popup-header {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.header-top {
    margin-bottom: 0;
}

.road-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.road-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.road-details {
    flex: 1;
}

.road-name {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.2;
}

.road-type {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.popup-content {
    padding: 16px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-item i {
    font-size: 16px;
}

.info-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.coordinates-section {
    background: #f8fafc;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 16px;
}

.coord-header {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
}

.coord-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.coord-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.coord-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
}

.coord-value {
    font-size: 10px;
    padding: 4px 6px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    color: #374151;
}

.coord-row.start .coord-value {
    border-left: 3px solid #22c55e;
}

.coord-row.end .coord-value {
    border-left: 3px solid #ef4444;
}

.popup-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 12px;
    font-weight: 500;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.action-btn.primary {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.action-btn.primary:hover {
    background: #2563eb;
}

.action-btn.secondary {
    background: #10b981;
    border-color: #10b981;
    color: white;
}

.action-btn.secondary:hover {
    background: #059669;
}

.action-btn i {
    font-size: 14px;
}

/* Leaflet popup customization */
.leaflet-popup-content-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    padding: 0;
}

.leaflet-popup-content {
    margin: 0;
    padding: 0;
}

.leaflet-popup-tip {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.leaflet-popup-close-button {
    color: #6b7280;
    font-size: 18px;
    padding: 4px;
    margin: 8px;
}

.leaflet-popup-close-button:hover {
    color: #374151;
}

/* Notification styles */
@keyframes pulse-highlight {
    0% { 
        transform: scale(1); 
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    50% { 
        transform: scale(1.02); 
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% { 
        transform: scale(1); 
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

/* Leaflet popup customization */
.leaflet-popup-content-wrapper {
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    padding: 0;
}

.leaflet-popup-content {
    margin: 0;
    padding: 0;
}

.leaflet-popup-tip {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Mobile responsiveness for popups */
@media (max-width: 768px) {
    .road-popup-container {
        max-width: 280px;
        min-width: 260px;
    }
    
    .popup-title {
        font-size: 13px;
    }
    
    .info-label,
    .info-value {
        font-size: 11px;
    }
    
    .coord-value {
        font-size: 9px;
    }
    
    .popup-action-btn {
        font-size: 10px;
        padding: 5px 10px;
    }
}
</style>