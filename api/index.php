<?php



/******************************
 * Mostrar errores
*******************************/

ini_set('display_errors',1);
ini_set("log_errors",1);
ini_set("error_log", "/home/u101606847/domains/chollocuenca.site/public_html/logs/php_error.log");
date_default_timezone_set('Europe/Madrid'); 


// Manejo de solicitudes OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); 
    exit;
}

define("URL", "https://api.chollocuenca.site");
/******************************
 * Requires
*******************************/
 require_once("boot.php");
 require_once('controllers/routes_controller.php');

 $controladorRutas = new RoutesController();

 $controladorRutas->index();


?>
