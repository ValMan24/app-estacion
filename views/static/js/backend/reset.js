const url = new URL(window.location.href);
const token = url.searchParams.get('token_action')
	reset.classList.add("dnone")
 dnone.style.display = "none"


	/*< Realiza el intento de logueo */
	reset(token).then(data => {

		// si el logueo fue valido
		if (data.list.errno == 200) {
			/*< Redirecciona al panel */
 reset.classList.remove("dnone")




reset.addEventListener('submit', (e) =>{
	e.preventDefault();
	const formData = new FormData(recoveryFormulario);

;

	const resetPassword = {

		pass1 : formData.get('txt_pass1'),
		pass2 : formData.get('txt_pass2'),
		tokenAction : token
	}

	fetch(`${APP_URL_BASE}/api/user/reset`, {
		method: 'POST', // HTTP method
		headers: {
		    'Content-Type': 'application/json', // Specifies JSON format
		},
			body: JSON.stringify(resetPassword),//valor a agregar
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



		}
		msg_validar.textContent = data.list.error;
		msg_validar.style.color = "red"
	})





 async function reset (token){

  const consulta = await fetch(`https://mattprofe.com.ar/alumno/6905/app-estacion/api/user/validarTokenAction?token_action=${token}`);
    const validar = await consulta.json();
return validar

 }