
recoveryFormulario.addEventListener('submit', (e) =>{
	

	 
	e.preventDefault();

	const formData = new FormData(recoveryFormulario);

	console.log(formData.get('txt_email'));

	const recoveryEmail = {

		email : formData.get('txt_email'),

	}

	fetch(`${APP_URL_BASE}/api/user/recovery`, {
		method: 'POST', // HTTP method
		headers: {
		    'Content-Type': 'application/json', // Specifies JSON format
		},
			body: JSON.stringify(recoveryEmail),//valor a agregar
		})
		.then(response => {
						//Agrega una clase al elemento window_chage
			// window_charge.classList.add("d-none")

		    return response.json(); // Parse JSON response
		})
		.then(data => {


			console.log('Success:', data)
			//display:none
			if(data.list.errno == 200){
				console.log("se envio el email correctamente")
			}
		})
})


// async function recovery(email) {
// 	/*< consulta a la API */
// 	const response = await fetch("/alumno/6905/app-estacion/api/user/recovery/?txt_email=" + email);
// 	/*< convierte la respuesta a formato json */
// 	const data = await response.json();

// 	return data;

// }
