import Leaflet from "leaflet";
import "leaflet/dist/leaflet.css";

const locations = window.locations;
const destinations = window.destinations;
const speeds = window.speeds;
const directions = window.directions;

const coords = locations.map(({ latitude, longitude, created_at }) => {
    const locationDate = new Date(created_at);

    let minDiff = Infinity;

    return [
        latitude,
        longitude,
        directions.reduce((acc, { direction, created_at }) => {
            const directionDate = new Date(created_at);

            const diff = Math.abs(locationDate - directionDate);

            if (diff < minDiff) {
                minDiff = diff;
                return direction;
            }

            return acc;
        }),
    ];
});

console.log(coords);

const marks = destinations.map(({ latitude, longitude }) => [
    latitude,
    longitude,
]);

const map = Leaflet.map("map").setView(coords[0], 12);

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

coords.forEach((coord) => {
    const icon = L.icon({
        iconUrl: "triangle.svg",
        iconSize: [20, 20],
        className: `rotate-[${coord[2]}deg]`,
    });

    Leaflet.marker(coord, { icon }).addTo(markerLayer);
});

function onMapChange() {
    const images = document.querySelectorAll('img[src="triangle.svg"]');
    images.forEach((image, i) => {
        if (image.style.transform.includes("rotate")) return;

        image.style.transform = `${image.style.transform} rotate(${coords[i][2]}deg)`;
        image.style.transformOrigin = "center";
    });
}

map.on("moveend", onMapChange);
