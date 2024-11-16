


//realiza una accion cuando se realiza un evento del tipo submit dentro del formulario
 //(se apreta el boton de tipo submit)
form_register.addEventListener('submit', (e) =>{
	//evita que se recargue la pagina cuando apreto el boton del formulario

	//Elimina una clase al elemento window_chage
	window_charge.classList.remove("d-none")

	//evento ya en el html de carga se le hace un style.display = block
	e.preventDefault();
	//hago un objeto con los valores de mi formulario lleno
	const formData = new FormData(form_register);

	console.log(formData.get('txt_email'));
	console.log(formData.get('txt_pass'));
	console.log(formData.get('txt_pass_2'));

	const register_data = {

		email : formData.get('txt_email'),
		pass1 : formData.get('txt_pass'),
		pass2 : formData.get('txt_pass_2')

	}

	fetch(`${APP_URL_BASE}/api/user/register`, {
		method: 'POST', // HTTP method
		headers: {
		    'Content-Type': 'application/json', // Specifies JSON format
		},
			body: JSON.stringify(register_data),//valor a agregar
		})
		.then(response => {
						//Agrega una clase al elemento window_chage
			window_charge.classList.add("d-none")

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