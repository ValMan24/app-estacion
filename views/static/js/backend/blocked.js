const url = new URL(window.location.href);
const token = url.searchParams.get('token')



	/*< Realiza el intento de logueo */
	blocked().then(data => {

		// si el logueo fue valido
		if (data.list.errno == 200) {
			/*< Redirecciona al panel */
			window.location.href = `${APP_URL_BASE}/login`;
		}
		msg_blocked.textContent = data.list.error;
		msg_blocked.style.color = "red"
	})



















 async function blocked (){
  const consulta = await fetch(`https://mattprofe.com.ar/alumno/6905/app-estacion/api/user/blocked?token=${token}`);
    const validar = await consulta.json();
return validar

 }