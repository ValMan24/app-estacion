const url = new URL(window.location.href);
const token = url.searchParams.get('token_action')



	/*< Realiza el intento de logueo */
	validacion().then(data => {

		// si el logueo fue valido
		if (data.list.errno == 200) {
			/*< Redirecciona al panel */
			window.location.href = `${APP_URL_BASE}/login`;
		}
		msg_validar.textContent = data.list.error;
		msg_validar.style.color = "red"
	})


 async function validacion (){
  const consulta = await fetch(`https://mattprofe.com.ar/alumno/6905/app-estacion/api/user/verificar?token_action=${token}`);
    const validar = await consulta.json();
return validar

 }