"use strict";

let mode24h = false;

function rellotgeDigital() {
    let ara = new Date();
    let ampm = "AM";
    let hora = ara.getHours();
    let minut = ara.getMinutes();
    let segon = ara.getSeconds();

    if (!mode24h) {
        if (hora >= 12) {
            ampm = "PM";
            if (hora > 12) hora -= 12;
        }
        if (hora === 0) hora = 12;
    }

    hora = hora < 10 ? "0" + hora : hora;
    minut = minut < 10 ? "0" + minut : minut;
    segon = segon < 10 ? "0" + segon : segon;

    let horaActual = mode24h ? `${hora}:${minut}:${segon}` : `${hora}:${minut}:${segon} ${ampm}`;
    let diaSetmana = ara.toLocaleDateString("ca", { weekday: 'long' });
    diaSetmana = diaSetmana.charAt(0).toUpperCase() + diaSetmana.slice(1);
    let dataActual = `${ara.getDate()} / ${ara.getMonth() + 1} / ${ara.getFullYear()}`;

    document.getElementById('data').innerHTML = `${horaActual}<br>${diaSetmana}<br>${dataActual}`;
}

function rellotgeAnalogic() {
    let canvas = document.getElementById('canvas');
    let ctx = canvas.getContext('2d');
    let radius = canvas.height / 2;
    ctx.translate(radius, radius);
    radius = radius * 0.90;
    setInterval(drawClock, 1000);

    function drawClock() {
        drawFace(ctx, radius);
        drawNumbers(ctx, radius);
        drawTime(ctx, radius);
    }

    function drawFace(ctx, radius) {
        let grad;
        ctx.beginPath();
        ctx.arc(0, 0, radius, 0, 2 * Math.PI);
        ctx.fillStyle = 'white';
        ctx.fill();
        grad = ctx.createRadialGradient(0, 0, radius * 0.95, 0, 0, radius * 1.05);
        grad.addColorStop(0, '#333');
        grad.addColorStop(0.5, 'white');
        grad.addColorStop(1, '#333');
        ctx.strokeStyle = grad;
        ctx.lineWidth = radius * 0.1;
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(0, 0, radius * 0.1, 0, 2 * Math.PI);
        ctx.fillStyle = '#333';
        ctx.fill();
    }

    function drawNumbers(ctx, radius) {
        let ang;
        let num;
        ctx.font = radius * 0.15 + "px arial";
        ctx.textBaseline = "middle";
        ctx.textAlign = "center";
        for (num = 1; num < 13; num++) {
            ang = num * Math.PI / 6;
            ctx.rotate(ang);
            ctx.translate(0, -radius * 0.85);
            ctx.rotate(-ang);
            ctx.fillText(num.toString(), 0, 0);
            ctx.rotate(ang);
            ctx.translate(0, radius * 0.85);
            ctx.rotate(-ang);
        }
    }

    function drawTime(ctx, radius) {
        let now = new Date();
        let hour = now.getHours();
        let minute = now.getMinutes();
        let second = now.getSeconds();
        hour = hour % 12;
        hour = (hour * Math.PI / 6) + (minute * Math.PI / (6 * 60)) + (second * Math.PI / (360 * 60));
        drawHand(ctx, hour, radius * 0.5, radius * 0.07);
        minute = (minute * Math.PI / 30) + (second * Math.PI / (30 * 60));
        drawHand(ctx, minute, radius * 0.8, radius * 0.07);
        second = (second * Math.PI / 30);
        drawHand(ctx, second, radius * 0.9, radius * 0.02);
    }

    function drawHand(ctx, pos, length, width) {
        ctx.beginPath();
        ctx.lineWidth = width;
        ctx.lineCap = "round";
        ctx.moveTo(0, 0);
        ctx.rotate(pos);
        ctx.lineTo(0, -length);
        ctx.stroke();
        ctx.rotate(-pos);
    }
}

function init() {
    rellotgeAnalogic(); // Inicialitzar el rellotge analògic
}