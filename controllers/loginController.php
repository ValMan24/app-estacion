<?php 

 $aux = $_SERVER["HTTP_SEC_CH_UA"];
$a = explode('"', $aux);
$navegador = "Navegador: ".$a[5];
$ip= "IP: ".$_SERVER["REMOTE_ADDR"];
$data_SO = explode(" ", $_SERVER["HTTP_USER_AGENT"]);
 $SO = "Sistema Operativo: ".str_replace("(", "", $data_SO[1]);
 $version = "Versión: ".$data_SO[2].$data_SO[3];
 $bits = "Bits: ".$data_SO[5];
 $data_user = 'Datos: <br>'.$ip.'<br>'.$SO.', '.$version.' '.$bits.'<br>'.$navegador;
$_ENV['DATA_USER'] = $data_user;


		// crea el objeto con la vista
	$tpl = new Acme("login");

	// carga la vista
	$tpl->loadTPL();

	// array para pasar variables a la vista
	$vars = ["PROJECT_SECTION" => "Login"];

	// reemplaza las variables en la vista
	$tpl->setVarsTPL($vars);

	// imprime en pantalla la página
	$tpl->printTPL();


 ?>