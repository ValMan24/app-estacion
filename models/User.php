<?php 

	/**
	* @file User.php
	* @brief Declaraciones de la clase User para la conexión con la base de datos.
	* @author Matias Leonardo Baez
	* @date 2024
	* @contact elmattprofe@gmail.com
	*/

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	/*< incluye la clase Mailer.php para enviar correo electrónico*/
	include_once 'Mailer.php';

	// se crea la clase User que hereda de DBAbstract
	class User extends DBAbstract{

		private $nameOfFields = array();

		/**
		 * 
		 * @brief Es el constructor de la clase User
		 * 
		 * Al momento de instanciar User se llama al padre para que ejecute su constructor
		 * 
		 * */
		function __construct(){

			// quiero salir de la clase actual e invocar al constructor
			parent::__construct();

			/**< Obtiene la estructura de la tabla */
			$result = $this->query('DESCRIBE usuario');

			foreach ($result as $key => $row) {
				$buff =$row["Field"];
				
				/**< Almacena los nombres de los campos*/
				$this->nameOfFields[] = $buff;

				/**< Autocarga de atributos a la clase */
				$this->$buff=NULL;
			}
			

		}

		/**
		 * 
		 * Hace soft delete del registro
		 * @return bool siempre verdadero
		 * 
		 * */
		function leaveOut(){

			$id = $this->id;
			$fecha_hora = date("Y-m-d H:i:s");

			$ssql = "UPDATE usuario SET delete_at='$fecha_hora' WHERE id=$id";

			$this->query($ssql);

			return true;
		}

		function dataUser(){
			$aux = $_SERVER["HTTP_SEC_CH_UA"];
			$a = explode('"', $aux);
			$navegador = "Navegador: ".$a[5];
			$ip= "IP: ".$_SERVER["REMOTE_ADDR"];
			$data_SO = explode(" ", $_SERVER["HTTP_USER_AGENT"]);
			$SO = "Sistema Operativo: ".str_replace("(", "", $data_SO[1]);
			$version = "Versión: ".$data_SO[2].$data_SO[3];
			$bits = "Bits: ".$data_SO[5];
			$data_user = 'Datos: <br>'.$ip.'<br>'.$SO.', '.$version.' '.$bits.'<br>'.$navegador;
			return $data_user;


		}



		/**
		 * 
		 * Finaliza la sesión
		 * @return bool true
		 * 
		 * */
		function logout(){

			session_unset();

			session_destroy();

			header('Location: landing');

			return true;
		}

		/**
		 * 
		 * Intenta loguear al usuario mediante email y contraseña
		 * @param array $form indexado de forma asociativa
		 * @return array que posee códigos de error especiales
		 * 
		 * */
		function login($form){

	
			/*< recupera el method http*/
			$request_method = $_SERVER["REQUEST_METHOD"];

			/* si el method es invalido*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< recupera el email del formulario*/
			$email = $form["txt_email"];


			/*< consultamos si existe el email*/
			$result = $this->query("CALL `login`('$email')");


			// el email no existe
			if(count($result)==0){
				return ["error" => "credenciales no válidas", "errno" => 404];
			}
			else if($result[0]["activo"]== 0){
				return ["error" => "Su usuario aún no se ha validado, revise su casilla de correo", "errno" => 404];
			}
			else if ($result[0]["bloqueado"]==1 || $result[0]["recupero"]==1) {
				return ["error" => "Su usuario está bloqueado, revise su casilla de correo", "errno" => 404];
			}

			/*< seleccionamos solo la primer fila de la matriz*/
			$result = $result[0];
			
			// si el email existe y la contraseña es valida
			if($result["contrasenia"]==md5($form["txt_pass"]."app-estacion")){
				$correo = new Mailer();
				$tpl = new ACME("emails/login");

				$tpl->loadTPLFromAPI();
				$vars = ["TOKEN_EMAIL" => $result["token"], "APP_URL_BASE" => $_ENV["APP_URL_BASE"], "DATA_USER" => $this->dataUser()];

				$tpl->setVarsTPL($vars);

				$cuerpo_email = $tpl->returnTPL();

				$correo->send(["destinatario" => $email, "motivo" => "Inicio de Sesión a App-Estacion", "contenido" => $cuerpo_email] );


				/**< autocarga de valores en los atributos de la clase */
				foreach ($this->nameOfFields as $key => $value) {
					$this->$value = $result[$value];
				}

				

				/*< carga la clase en la sesión*/
				$_SESSION["app-estacion"]['user'] = $this;

				/*< usuario valido*/
				return ["error" => "Acceso valido", "errno" => 200];
			}

			// email existe pero la contraseña invalida

			$correo = new Mailer();
			$tpl = new ACME("emails/loginfalled");

			$tpl->loadTPLFromAPI();
			$vars = ["TOKEN_EMAIL" => $result["token"], "APP_URL_BASE" => $_ENV["APP_URL_BASE"], "DATA_USER" => $this->dataUser()];

			$tpl->setVarsTPL($vars);

			$cuerpo_email = $tpl->returnTPL();

			$correo->send(["destinatario" => $email, "motivo" => "Inicio de Sesión a App-Estacion", "contenido" => $cuerpo_email] );
			return ["error" => "Error de contraseña", "errno" => 405];

		}



		/**
		 * 
		 * Agrega un nuevo usuario si no existe el correo electronico en la tabla users
		 * @param array $form es un arreglo assoc con los datos del formulario
		 * @return array que posee códigos de error especiales 
		 * 
		 * */
		function register($request){


			/*< recupera el email*/
			$email = $request->email;
			$correo = $email;
			/*< consulta si el email ya esta en la tabla de usuarios*/
			$result = $this->query("SELECT * FROM usuario WHERE email = '$correo'");

			// el email no existe entonces se registra
			if(is_null($result) || count($result) ==0){

				/*< encripta la contraseña*/
				$pass = md5($request->pass1."app-estacion");

				/*< se crea el token único para validar el correo electrónico*/
				$token = md5($_ENV['PROJECT_WEB_TOKEN'].$email);
				$fechaActual = date("Y-m-d H:i:s");
				$token_action = md5($fechaActual.$email);
				/*< agrega el nuevo usuario y deja en pendiente de validar su email*/
				$ssql = "INSERT INTO usuario (email, contrasenia, token, token_action, activo, bloqueado, recupero, add_date) 
				VALUES ('$email', '$pass', '$token', '$token_action', 0, 0, 0, CURRENT_TIMESTAMP)";


				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				/*< se recupera el id del nuevo usuario*/
				$this->id = $this->db->insert_id;


				/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
				$correo = new Mailer();

				// crea el objeto con la vista
				$tpl = new ACME("emails/register");

				// carga la vista
				$tpl->loadTPLFromAPI();

				$vars = ["TOKEN_EMAIL" => $token_action, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

				/*< pasa el valor de la variable token a la vista*/
				$tpl->setVarsTPL($vars);

				/*< plantilla de email para validar cuenta*/
				$cuerpo_email = $tpl->returnTPL();

				/*< envia el correo electrónico de validación*/
				$correo->send(["destinatario" => $email, "motivo" => "Confirmación de registro", "contenido" => $cuerpo_email] );

				/*< aviso de registro exitoso*/
				return ["error" => "Usuario registrado", "errno" => 200];
			}

			// si el email existe 
			return ["error" => "Correo ya registrado, desea Iniciar Sesión?", "errno" => 405];

		}


		/**
		 * 
		 * Actualiza los datos del usuario con los datos de un formulario
		 * @param array $form es un arregle asociativo con los datos a actualizar
		 * @return array arreglo con el código de error y descripción
		 * 
		 * */
		function update($form){
			$nombre = $form["txt_first_name"];
			$apellido = $form["txt_last_name"];
			$id = $this->id;


			$this->first_name = $nombre;
			$this->last_name = $apellido;

			$ssql = "UPDATE users SET first_name='$nombre', last_name='$apellido' WHERE id=$id";

			$result = $this->query($ssql);

			return ["error" => "Se actualizo correctamente", "errno" => 200];
		}

		/**
		 * 
		 * Cantidad de usuarios registrados
		 * @return int cantidad de usuarios registrados
		 * 
		 * */
		function getCantUsers(){

			$result = $this->query("SELECT * FROM users");

			return $this->db->affected_rows;
		}


		/**
		 * 
		 * @brief Retorna un listado limitado
		 * @param string $request_method espera a GET
		 * @param array $request [inicio][cantidad]
		 * @return array lista con los datos de los usuarios 
		 * 
		 * */
		function getAllUsers($request){

			$request_method = $_SERVER["REQUEST_METHOD"];

			/*< Es el método correcto en HTTP?*/
			if($request_method!="GET"){
				return ["errno" => 410, "error" => "Metodo invalido"];
			}

			/*< Solo un usuario logueado puede ver el listado */
			if(!isset($_SESSION["morphyx"])){
				return ["errno" => 411, "error" => "Para usar este método debe estar logueado"];
			}

			/*

			if(!isset($_SESSION["morphyx"]['user_level'])){

				if($_SESSION["morphyx"]['user_level']!='admin'){
				return ["errno" => 412, "error" => "Solo el 	administrador puede utilizar el metodo"];
				}
			}

			*/


			$inicio = 0;

			if(isset($request["inicio"])){
				$inicio = $request["inicio"];
			}

			if(!isset($request["cantidad"])){
				return ["errno" => 404, "error" => "falta cantidad por GET"];
			}

			$cantidad = $request["cantidad"];

			$result = $this->query("SELECT * FROM users LIMIT $inicio, $cantidad");

			return $result;
		}


		function verificar($request){

			$token_act = $request["token_action"];


			$result = $this->query("SELECT * FROM usuario WHERE token_action = '$token_act'");

			if (count($result)==0) {
				return ["error" => "El token no corresponde a un usuario", "errno" => 404];
				return false;
			}else{
				$ssql = "UPDATE usuario SET   activo=1,token_action='', active_date=CURRENT_TIMESTAMP WHERE token_action='$token_act'";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				$correo = new Mailer();

				$tpl = new ACME("emails/verificado");

				$tpl->loadTPLFromAPI();


				$cuerpo_email = $tpl->returnTPL();

				$correo->send(["destinatario" => $email, "motivo" => "Validación completada", "contenido" => $cuerpo_email] );

				return ["error" => "Usuario validado", "errno" => 200];

				return true;

			}

		}





		function blocked($request){

			$token_actual = $request["token"];

			$fechaActual = date("Y-m-d H:i:s");

			$token_act = md5($fechaActual."app-estacion");

			$result = $this->query("SELECT * FROM usuario WHERE token = '$token_actual'");

			if (count($result)==0) {
				return ["error" => "El token no corresponde a un usuario", "errno" => 404];
				return false;
			}else{
				$ssql = "UPDATE usuario SET bloqueado=1, token_action='$token_act', blocked_date=CURRENT_TIMESTAMP WHERE token='$token_actual'";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

				$correo = new Mailer();

				$tpl = new ACME("emails/verificado");

				$tpl->loadTPLFromAPI();


				$cuerpo_email = $tpl->returnTPL();

				$correo->send(["destinatario" => $email, "motivo" => "Bloqueo de cuenta completado", "contenido" => $cuerpo_email] );

				return ["error" => "Usuario validado", "errno" => 200];

				return true;

			}

		}






function recovery($request){
$email = $request->email;
$correo = $email;
$result = $this->query("SELECT * FROM usuario WHERE email = '$correo'");

if (is_null($result) || count($result) == 0) {
	
	return ["error" => "El email no se encuentra registrado", "errno" => 405];

}else{
/*si está registrado cambiar el estado de recupero de 0 a
1 y se guarda la fecha hora de esto en recover_date, se genera el token_action y se envía al usuario un
correo electrónico*/

	$fechaActual = date("Y-m-d H:i:s");
	$token_act = md5($fechaActual."app-estacion");

$ssql = "UPDATE usuario SET recupero=1, token_action='$token_act', recover_date=CURRENT_TIMESTAMP WHERE email='$correo'";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);


				/*< instancia la clase Mailer para enviar el correo electrónico de validación de correo electrónico*/
				$correo = new Mailer();

				// crea el objeto con la vista
				$tpl = new ACME("emails/recovery");

				// carga la vista
				$tpl->loadTPLFromAPI();

				$vars = ["TOKEN_EMAIL" => $token_action, "APP_URL_BASE" => $_ENV["APP_URL_BASE"]];

				/*< pasa el valor de la variable token a la vista*/
				$tpl->setVarsTPL($vars);

				/*< plantilla de email para validar cuenta*/
				$cuerpo_email = $tpl->returnTPL();

				/*< envia el correo electrónico de validación*/
				$correo->send(["destinatario" => $email, "motivo" => "Restablecer Contraseña", "contenido" => $cuerpo_email] );




}


}


function validarTokenAction($token){
$result = $this->query("SELECT * FROM usuario WHERE email = '$correo'");
if(is_null($result) || count($result) == 0){
	return ["error" => "Token Action Invalido", "errno" => 405];
}else{
return ["error" => "Token Action valido", "errno" => 200];
}

}

function reset($request){
if($request->pass1 == $request->pass2){

$pass = $request->pass1;
$token_act = $request->tokenAction;
$token = $this->query("SELECT token FROM usuario WHERE token_action = '$token_act'");
$ssql = "UPDATE usuario SET contrasenia='$pass', bloqueado=0, recupero=0, token_action='' WHERE token_action='$token_act'";

				/*< ejecuta la consulta*/
				$result = $this->query($ssql);

$correo = new Mailer();
			$tpl = new ACME("emails/reset");

			$tpl->loadTPLFromAPI();

			$vars = ["TOKEN_EMAIL" => $token, "APP_URL_BASE" => $_ENV["APP_URL_BASE"], "DATA_USER" => $this->dataUser()];

			$tpl->setVarsTPL($vars);

			$cuerpo_email = $tpl->returnTPL();

			$correo->send(["destinatario" => $email, "motivo" => "Modificacion de contraseña", "contenido" => $cuerpo_email] );



return ["error" => "Cambio de contraseña exitoso", "errno" => 200];
}else{
	return ["error" => "Las contraseñas son distintas", "errno" => 405];
}



}







	}


?>