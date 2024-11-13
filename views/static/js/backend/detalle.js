// Obtén la URL actual
const url = new URL(window.location.href);
// Usa searchParams para obtener el valor del parámetro
// const token_prod = url.searchParams.get('prod');
const link = url.searchParams.get('Z')


async function infoEstacion(link){


       const consulta = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations");
     const estaciones = await consulta.json();
const template = document.getElementById('plantilla');
const cardConteiner = document.getElementById('cardClima');

estaciones.forEach(estacion =>{ 
if(estacion.chipid == link){
const card = template.content.cloneNode(true);

card.querySelector('.font-apodo').textContent = estacion.apodo;
card.querySelector('.font-ubicacion').textContent = estacion.ubicacion;

cardConteiner.appendChild(card);
}
});



}

document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.querySelector('.registro_login');
    if (contenedor) {
        contenedor.style.display = 'none';
    }
});
infoEstacion(link);
