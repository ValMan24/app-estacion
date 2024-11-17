

    /*< Realiza el intento de logueo */
    cantUser().then(info => {

let data = info;

let parsedData = JSON.parse(data);

// Paso 2: Aplanar el array y extraer los números
let result = parsedData.flat().map(item => {
    if (typeof item === 'object' && item['COUNT(*)']) {
        return parseInt(item['COUNT(*)']); // Extraemos el número del objeto
    }
    return item; // Si es un número, lo dejamos tal cual
});





const register = document.getElementById("resgistrados");
const cliente = document.getElementById("clientes");
if (register) {register.innerHTML = `${result[0]}`;}
if (cliente) {cliente.innerHTML = `${result[1]}`;}







console.log(result); // [3, 1]
    })




async function cantUser () {
    
        // Realiza la consulta a la API
        const consulta = await fetch('https://mattprofe.com.ar/alumno/6905/app-estacion/api/user/cantRegisterUser');
            const validar = await consulta.json();
return validar.list
        }

// Llama a la función y muestra los resultados
cantUser();
