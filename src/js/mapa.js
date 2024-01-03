if(document.querySelector('#mapa')){
    const lat = 20.988071;
    const lng = -89.621893;
    const zoom = 16;
    const map = L.map('mapa').setView([lat, lng], zoom);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.marker([lat, lng]).addTo(map)
    .bindPopup(`
        <h2 class="mapa__heading">DevWebCamp</h2>
        <p class="mapa__texto">Centro Internacional de Congresos de Mérida</p>
    `)
    .openPopup();
}