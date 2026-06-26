'use strict';

let markers = window.INDEX_CONFIG.markers;
let propertyWithImages = window.INDEX_CONFIG.propertyWithImages;

// resto del código...

// Initialize Google Maps
window.initMap = async function () {
    let map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: {
            lat: 29.0669,
            lng: -13.5900
        },
        mapId: "af934e8f21fb7b29",
    });

    map.addListener('mapcapabilities_changed', () => {
        const mapCapabilities = map.getMapCapabilities();
        if (!mapCapabilities.isAdvancedMarkersAvailable) {
            console.log('Advanced markers are not available');
        }
    });

    const markerElements = [];
    let currentInfoWindow = null;

    markers.forEach((markerInfo) => {
        const position = {
            lat: parseFloat(markerInfo.lat),
            lng: parseFloat(markerInfo.lng)
        };

        const pin = new google.maps.marker.PinElement({
            glyphColor: "white",
        });

        const marker = new google.maps.marker.AdvancedMarkerElement({
            position: position,
            map: map,
            title: markerInfo.title,
            content: pin.element,
        });

        const infoWindow = new google.maps.InfoWindow({
            content: buildContent(markerInfo),
        });

        marker.addListener("click", () => {
            if (currentInfoWindow) {
                currentInfoWindow.close();
            }
            infoWindow.open(map, marker);
            currentInfoWindow = infoWindow;
        });

        markerElements.push(marker);
    });

    // Aplicar MarkerClusterer
    new markerClusterer.MarkerClusterer({
        map: map,
        markers: markerElements,
    });
}

// Function to build content for InfoWindow
function buildContent(property) {
    const content = document.createElement("div");
    content.classList.add("property");

    // imagen + link
    const imgWrapper = document.createElement("div");
    imgWrapper.classList.add("property-image");

    const linkImg = document.createElement("a");
    linkImg.href = `/property/${property.id}`;

    const img = document.createElement("img");
    img.src = `/storage/images/${property.images_div}/${propertyWithImages[property.id]}`;
    img.alt = property.title;
    img.style.cssText = "width: 100px; height: 100px; object-fit: cover;";

    linkImg.appendChild(img);
    imgWrapper.appendChild(linkImg);

    // detalles
    const details = document.createElement("div");
    details.classList.add("property-details");

    const title = document.createElement("h3");
    title.textContent = property.title;

    const desc = document.createElement("p");
    desc.textContent = property.description;

    const location = document.createElement("p");
    location.innerHTML = "<strong>Location:</strong> ";
    location.appendChild(document.createTextNode(property.location));

    const price = document.createElement("p");
    price.innerHTML = "<strong>Price per Night:</strong> ";
    price.appendChild(document.createTextNode(`Around ${property.price_per_night}€`));

    const capacity = document.createElement("p");
    capacity.innerHTML = "<strong>Capacity:</strong> ";
    capacity.appendChild(document.createTextNode(`${property.capacity} people`));

    const linkDetails = document.createElement("a");
    linkDetails.href = `/property/${property.id}`;
    linkDetails.textContent = "View Property Details";

    details.appendChild(title);
    details.appendChild(desc);
    details.appendChild(location);
    details.appendChild(price);
    details.appendChild(capacity);
    details.appendChild(linkDetails);

    content.appendChild(imgWrapper);
    content.appendChild(details);

    return content;
}

document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.getElementById("available-properties");
    const container = document.getElementById("carousel-container");
    const prevButton = document.querySelector(".prev");
    const nextButton = document.querySelector(".next");

    if (!carousel || !prevButton || !nextButton || !container) return;

    const firstCard = document.querySelector(".cardcontainer");

    // If there are no cards, hide the buttons and return
    if (!firstCard) {
        prevButton.style.display = "none";
        nextButton.style.display = "none";
        return;
    }

    let cardWidth = firstCard.offsetWidth + 15;

    function updateCarouselState() {
        const canScroll = carousel.scrollWidth > carousel.clientWidth + 5;

        if (!canScroll) {
            container.classList.add("no-scroll");
        } else {
            container.classList.remove("no-scroll");
        }
    }

    prevButton.addEventListener("gmp-click", function () {
        carousel.scrollBy({
            left: -cardWidth,
            behavior: "smooth"
        });
    });

    nextButton.addEventListener("gmp-click", function () {
        carousel.scrollBy({
            left: cardWidth,
            behavior: "smooth"
        });
    });

    window.addEventListener("resize", function () {
        const card = document.querySelector(".cardcontainer");
        if (card) cardWidth = card.offsetWidth + 15;
        updateCarouselState();
    });

    updateCarouselState();
});