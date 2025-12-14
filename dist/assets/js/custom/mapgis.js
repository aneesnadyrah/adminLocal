"use strict";

window.onload = function () {
  // 100.3745846,6.1836501 // kedah
  // 103.3260, 3.8077 //pahang
  // 103.14608033896808,5.328777955715676 // terengganu
  // 101.0914411,4.6002637 // ipoh
  let mapCenterInit;
  if (typeof tenant !== 'undefined') {
    // appsTenant variable exists
    if (tenant.toUpperCase() == "UCIDOS") {
      mapCenterInit = [103.3260, 3.8077];
    } else if (tenant.toUpperCase() == "KITER") {
      mapCenterInit = [103.14608033896808,5.328777955715676];
    } else if (tenant.toUpperCase() == "KUDRAT") {
      mapCenterInit = [101.0914411,4.6002637];
    } else {
      mapCenterInit = [103.3260, 3.8077];
    }
  } else {
    // appsTenant variable does not exist
    mapCenterInit = [101.0914411,4.6002637];
  }

  mapboxgl.accessToken = 'pk.eyJ1IjoibXJoNHMiLCJhIjoiY2poeDh3bmtoMDk5ZTNrcDhkaDBnamFiMiJ9.G-NU8f4IrxF2vr7X43lE0w';
  var map = new mapboxgl.Map({
    container: 'mapgis',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: mapCenterInit,
    zoom: 10,
    attributionControl: false,
  });

  var logo = document.querySelector('.mapboxgl-ctrl-logo');
  if (logo) {
    logo.parentNode.removeChild(logo);
  }

  map.on('load', function () {

    // Disable y-flip and alpha-premultiplication for all images and icons
    map.showTileBoundaries = false;
    map.showCollisionBoxes = false;

  });

const markers = [
  [103.3260, 3.8077, 'assets/media/avatars/300-1.jpg', 'Aliff'],
  [103.2260, 3.7077, 'assets/media/avatars/300-2.jpg', 'Atikah'],
  [103.2260, 3.9077, 'assets/media/avatars/300-3.jpg', 'Najmie']
];

// add a marker for each data point
markers.forEach(data => {
  const marker = new mapboxgl.Marker({
    element: document.createElement('img'),
    anchor: 'bottom',
    draggable: false
  }).setLngLat(data.slice(0, 2))
    .addTo(map);
  marker.getElement().src = data[2];
  marker.getElement().width = 40;
  marker.getElement().height = 40;
  marker.getElement().style.borderRadius = '50%';
});

// create a list of marker images that can be clicked to zoom to the corresponding marker
const markerList = document.createElement('ul');
markers.forEach(data => {
  const image = document.createElement('img');
  image.src = data[2];
  image.style.width = '35px';
  image.style.height = '35px';
  image.style.cursor = 'pointer';
  image.style.borderRadius = '50%';
  image.setAttribute('data-bs-toggle', 'tooltip'); // add this line to activate the tooltip
  image.setAttribute('data-bs-placement', 'left'); // add this line to set the tooltip position
  image.setAttribute('title', data[3]); // add this line to set the tooltip text

  image.addEventListener('click', () => {
    map.flyTo({
      center: data.slice(0, 2),
      zoom: 15
    });
  });

  const listItem = document.createElement('li');
  listItem.style.display = 'inline-block';
  listItem.style.margin = '5px';
  listItem.appendChild(image);
  markerList.appendChild(listItem);
});

// activate all Bootstrap tooltips in the page
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

const mapContainer = map.getContainer();
const mapStyle = window.getComputedStyle(mapContainer);

// adjust the position of the marker list based on the map's container padding
const paddingTop = parseFloat(mapStyle.paddingTop);
const paddingRight = parseFloat(mapStyle.paddingRight);
const markerListTop = paddingTop + 10;
const markerListRight = paddingRight + 10;

markerList.style.position = 'absolute';
markerList.style.top = `${markerListTop}px`;
markerList.style.right = `${markerListRight}px`;
markerList.style.zIndex = '1';
// markerList.style.backgroundColor = 'white';
markerList.style.padding = '10px';
// markerList.style.borderRadius = '5px';
// markerList.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.10)';

// Make the list appear vertically
markerList.style.display = 'flex';
markerList.style.flexDirection = 'column';
markerList.style.alignItems = 'flex-end';

mapContainer.appendChild(markerList);
};