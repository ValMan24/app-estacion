
 admin.style.position="fixed"



// const map = L.map('map').setView([-27.4692131, -58.8306349], 2);

// const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
//     maxZoom: 19
// }).addTo(map);


// /* Obtenemos el listado de datos */
// loadTracker().then(info => {
//     // Parsear la cadena JSON del campo 'list' en un array
//     const datos = JSON.parse(info.list);

//     datos.forEach(fila => {
//         let latitud = fila.latitud;
//         let longitud = fila.longitud;
//         let accesos = fila.repeticiones;

//         if (latitud && longitud) {
//             const marker = L.marker([latitud, longitud]).addTo(map)
//                 .bindPopup('Accesos: ' + accesos)
//                 .openPopup();
//         } else {
//             console.error("Coordenadas no válidas para la fila:", fila);
//         }
//     });
// }).catch(error => {
//     console.error("Error al cargar la información:", error);
// });

// /**
//  * Función asincrona para acceder al listado que tiene las latitudes
//  * y longitudes a pintar como marcadores en el mapa
//  */
// async function loadTracker() {
//     const response = await fetch("https://mattprofe.com.ar/alumno/6905/app-estacion/api/user/list_clients_location");
//     const data = await response.json();

//     return data;
// }
const map = L.map('map').setView([-27.4692131, -58.8306349], 2);

const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

loadTracker().then(info => {
    try {
        console.log("Datos completos recibidos:", info); 

        if (info.list && Array.isArray(info.list.tracker)) {
 
            info.list.tracker.forEach(fila => {
                const latitud = parseFloat(fila.latitud);
                const longitud = parseFloat(fila.longitud);
                const accesos = fila.accesos;

                // Validar coordenadas antes de usarlas
                if (!isNaN(latitud) && !isNaN(longitud)) {
                    const marker = L.marker([latitud, longitud]).addTo(map)
                        .bindPopup('Accesos: ' + accesos)
                        .openPopup();
                } else {
                    console.error("Coordenadas no válidas para la fila:", fila);
                }
            });
        } else {
            console.error("La estructura de 'list.tracker' no es válida o no contiene un array.");
        }
    } catch (error) {
        console.error("Error al procesar la lista de ubicaciones:", error);
    }
}).catch(error => {
    console.error("Error al cargar la información:", error);
});

/**
 * Función asincrona para acceder al listado que tiene las latitudes
 * y longitudes a pintar como marcadores en el mapa
 */
async function loadTracker() {
    const response = await fetch("https://mattprofe.com.ar/alumno/6905/app-estacion/api/user/list_clients_location");
    
    // Validar si la respuesta es exitosa
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    return data;
}
