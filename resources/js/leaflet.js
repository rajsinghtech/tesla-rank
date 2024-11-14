import Leaflet from "leaflet";
import "leaflet/dist/leaflet.css";

const coords = window.locations.map(({ latitude, longitude }) => [
    latitude,
    longitude,
]);

const marks = window.destinations.map(({ latitude, longitude }) => [
    latitude,
    longitude,
]);

console.log(coords, marks);

const map = Leaflet.map("map").setView(coords[~~(coords.length / 2)], 12);

Leaflet.tileLayer("https://{s}.tile.osm.org/{z}/{x}/{y}.png", {
    attribution:
        'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
    maxZoom: 18,
}).addTo(map);

const markerLayer = Leaflet.layerGroup().addTo(map);

marks.forEach((mark) => {
    Leaflet.marker(mark).addTo(markerLayer);
});

Leaflet.polyline(coords).addTo(markerLayer);
