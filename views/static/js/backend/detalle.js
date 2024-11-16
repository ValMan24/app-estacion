let grafico = null

// Obtén la URL actual
const url = new URL(window.location.href);
const link = url.searchParams.get('Z')

if(link == null){window.location.href = `${APP_URL_BASE}/panel`;} 
let fecha = []
let hora ="";
let hora_minuto ="";
let aux ="";
let datos_clima = []

const graficoDolar = document.getElementById('grafico-dolar');

// Asignamos el estilo width y height
graficoDolar.style.width = "100%";
graficoDolar.style.height = "100%";


if(grafico == null){

    let btn = {};
    btn.value = "temperatura";
    createGraphic(btn)
  
}
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


async function getEstationData(){
    const consulta = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${link}&cant=7`);
    const dato_estacion = await consulta.json();
    let apodo = dato_estacion[0]["estacion"];
    let ubicacion = dato_estacion[0]["ubicacion"];
    let tempmax = dato_estacion[6]["tempmax"];
     let tempmin = dato_estacion[6]["tempmin"];
     let sensamax = dato_estacion[6]["sensamax"];
     let sensamin = dato_estacion[6]["sensamin"];
     let temperatura_actual = dato_estacion[6]["temperatura"];
     let sensacion_actual = dato_estacion[6]["sensacion"];
let presion = dato_estacion[6]["presion"];
let humedad = dato_estacion[6]["humedad"];
let ri = dato_estacion[6]["fwi"];
let viento = dato_estacion[6]["viento"];
     localStorage.setItem("sensamax",sensamax);
     localStorage.setItem("sensamin",sensamin);
     localStorage.setItem("temperatura",temperatura_actual);
     localStorage.setItem("sensacion",sensacion_actual)
     localStorage.setItem("presion",presion)
     localStorage.setItem("humedad",humedad)
    localStorage.setItem("tempmax",tempmax)
    localStorage.setItem("viento",viento)
localStorage.setItem("tempmin",tempmin)
localStorage.setItem("ri",ri)
    localStorage.setItem("apodo",apodo)
    localStorage.setItem("ubi",ubicacion)

    return dato_estacion
}


function createGraphic(btn){

    datos_clima = []
    fecha = []
    getEstationData().then( data => {

       for (let i = 0; i < data.length; i++) {
           datos_clima.push(data[i][btn.value]);  
           hora = data[i]["fecha"].split(/\s+/);
           hora_minuto =  hora[1].split(":");
           aux = `${hora_minuto[0]}:${hora_minuto[1]}`
           fecha.push(aux)

       }



       procesaDatos(datos_clima,fecha,btn.value)
       setInterval(() => {
        procesaDatos(datos_clima,fecha,btn.value)
        },6000);
  });
}

// Toma los datos de un registro y los agrega a los vectores correspondientes, luego los agrega a los datos para generar el gráfico
function procesaDatos(dato,fecha,titulo){
    let unidad ="";
    let maximo = ""
    let minimo = ""
    let actual = []
    let dataEstacion =""
    let title=""
    let texto = ""
    let encabezado = ""
    let pre = ""
    let hu = ""
    let ri = ""
    let vi = ""
    dataEstacion = `Estacion: ${localStorage.getItem("apodo")} <br>Ubicacion: ${localStorage.getItem("ubi")}<br>`



    switch (titulo) {

    case "temperatura":
      title = "Temperatura" 
      maximo = localStorage.getItem("tempmax") + "°C"
      minimo = localStorage.getItem("tempmin") + "°C"
      smax = localStorage.getItem("sensamax") + "°C"
      smin = localStorage.getItem("sensamin") + "°C"
      temp = localStorage.getItem("temperatura") + "°C"
      sensa = localStorage.getItem("sensacion") + "°C"
texto = `maxima:${maximo} <br> minima: ${minimo}<br> Temperatura actual: ${temp}`/*<br> sensacion térmica: ${sensa}*/

      break;
  case "presion":
     title = "Presión"
     unidad = "hPa"
      pre = localStorage.getItem("sensacion") + "hPa"
 texto = `Presión actual: ${pre}`
     break;
 case "humedad":
     title = "humedad"
    hu = localStorage.getItem("humedad")+ "%"
     texto = `Humedad actual: ${hu}`
     break;
 case "fwi":
     title = "Riesgo de incendio"
     ri = localStorage.getItem("ri") + "fwi"
    texto = `${ri}`
 
     break;
 case "viento":
     title = "viento"
     vi = localStorage.getItem("viento")+"Km/H"
texto = `Viento Actual: ${vi}`
     break;

 }
     let estacion = document.querySelector('.estacion');
let extra = document.querySelector('.extra');
// Modificar su contenido
    if (estacion) {
        estacion.innerHTML = dataEstacion;
        if (extra) {
        extra.innerHTML = texto
        }
    } else {
    }


 encabezado = `${dataEstacion}<h2>${title}</h2><br>${extra}`


    // Muestra los datos en el monitor
 pintaMonitor(dato)
    // Agregamos el nuevo dato como una posición dentro del vector
 datos_clima.push(dato)



 datos_clima.pop()
 localStorage.setItem("datos",datos_clima)
  
    // Valores que se grafican
 const valores = {
    labels: fecha,
    datasets: [{
           label: title, // detalle de la linea graficada
           backgroundColor: 'rgb(25, 174, 49)', // color circulo
           borderColor: 'rgb(25, 174, 49)', // color linea
           data: datos_clima // valores a graficar
       }]
}
  
    

    // muestra el gráfico con el título
pintaGrafico(valores, title)
}

// muestra el gráfico
function pintaGrafico(valores, titulo){
    let x = localStorage.getItem("datos").split(',');
console.log( x)
    // Opciones generales del gráfico
     let max_chart_y = Math.max(...x) > 10 ? Math.max(...x) * 1.2 : 10

        let min_chart_y = Math.min(...x) == 0 ? 0 : Math.min(...x) * 0.8

        // ConfiguraciÃ³n del grÃ¡fico
        var config = {
            type: 'line', /*doughnut - bar - line*/
            data: valores,
            options: {
                indexAxis: 'x', //Y: barras horizontales ; X: barras verticales
                plugins: {
                    legend: {
                        display: false // se desactiva la leyenda
                    }
                },
                scales: {
                    y: {
                        min: min_chart_y, // Establece el mÃ­nimo del eje Y (puedes ajustarlo segÃºn tus necesidades)
                        max: max_chart_y, // Establece el mÃ¡ximo del eje Y (ajÃºstalo tambiÃ©n)
                        ticks: {
                            stepSize: 2 // Controla el tamaÃ±o de los saltos entre los valores del eje Y
                        }
                    }
                }
            }
        };


    // si el objeto gráfico ya esta instanciado se destruye para que se vuelva a crear limpio
    
    if(grafico!=null){
        grafico.destroy();
    }

    // Crea el gráfico dentro del canvas
    grafico = new Chart(document.querySelector("#grafico-dolar"), config)
}


// muestra los valores en el monitor
function pintaMonitor(valores){

    const periodo = document.querySelector("#grafico-dolar")
    if (periodo) {
        periodo.innerHTML = valores.periodo
    } else {
        console.error("El elemento no se encontró en el DOM.");
    }

}
  