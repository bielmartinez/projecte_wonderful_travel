"use strict";

// Función para mostrar la hora, día y fecha actual en catalán
function rellotge() {
    let ara = new Date();
    let ampm = "AM";
    let hora = ara.getHours();
    let minut = ara.getMinutes();
    let segon = ara.getSeconds();

    // Formatear AM/PM
    if (hora >= 12) {
        ampm = "PM";
        if (hora > 12) hora -= 12;
    }
    if (hora === 0) hora = 12; // Mostrar 12 en vez de 0 para la medianoche

    // Añadir ceros si es necesario
    hora = hora < 10 ? "0" + hora : hora;
    minut = minut < 10 ? "0" + minut : minut;
    segon = segon < 10 ? "0" + segon : segon;

    // Hora en formato HH:MM:SS AM/PM
    let horaActual = `${hora}:${minut}:${segon} ${ampm}`;

    // Día de la semana en catalán
    let diaSetmana = ara.toLocaleDateString("ca", { weekday: 'long' });
    diaSetmana = diaSetmana.charAt(0).toUpperCase() + diaSetmana.slice(1);

    // Fecha actual en formato DD / MM / AAAA
    let dataActual = `${ara.getDate()} / ${ara.getMonth() + 1} / ${ara.getFullYear()}`;

    // Mostrar la información en el elemento con id="data"
    document.getElementById('data').innerHTML = `${horaActual}<br>${diaSetmana}<br>${dataActual}`;
}

// Función para actualizar el reloj cada segundo
function init() {
    rellotge(); // Llamar la función inmediatamente
    setInterval(rellotge, 1000); // Actualizar cada segundo
}
