async function verEstaciones(){


       const consulta = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations");
     const estaciones = await consulta.json();
const template = document.getElementById('plantilla');
const cardConteiner = document.getElementById('cardConteiner');

estaciones.forEach(estacion =>{ 

const card = template.content.cloneNode(true);

card.querySelector('.tarjeta').href = `https://mattprofe.com.ar/alumno/6905/app-estacion/details?Z=${estacion.chipid}`;
card.querySelector('.font-apodo').textContent = estacion.apodo;
card.querySelector('.font-ubicacion').textContent = estacion.ubicacion;
card.querySelector('.font-12').textContent += `${estacion.visitas}`;

cardConteiner.appendChild(card);

});



}


document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.querySelector('.registro_login');
    if (contenedor) {
        contenedor.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.querySelector('.estaciones');
    if (contenedor) {
        contenedor.style.display = 'none';
    }
});


verEstaciones();
