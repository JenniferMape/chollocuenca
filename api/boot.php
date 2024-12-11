<?php
require('./vendor/idiorm.php');
require 'vendor/autoload.php';


/******************************
 * Conexion DB
*******************************/
// Acceso a las variables de entorno
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');


if (!$dbHost || !$dbName || !$dbUser || !$dbPassword) {
    die('Faltan algunas variables de entorno para la conexión a la base de datos.');
}
try {
   
    ORM::configure("mysql:host=$dbHost;dbname=$dbName");
    ORM::configure('username', $dbUser);
    ORM::configure('password', $dbPassword);

    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    
    ORM::configure('return_result_sets', true);

    $db = ORM::get_db();

    $db->query('SELECT 1');

} catch (Exception $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";

    ORM::configure("mysql:host=$dbHost");
    ORM::configure('username', $dbUser);
    ORM::configure('password', $dbPassword);

    try {
        $db = ORM::get_db();
        $db->query("CREATE DATABASE IF NOT EXISTS $dbName"); 

        ORM::configure("mysql:host=$dbHost;dbname=$dbName");

        $sqlFile = '../Database/chollo_cuenca.sql'; 
        if (!file_exists($sqlFile)) {
            die("El archivo SQL '$sqlFile' no existe.");
        }

        $sqlContent = file_get_contents($sqlFile);
        if ($sqlContent === false) {
            die("No se pudo leer el archivo SQL.");
        }

        $queries = explode(";", $sqlContent);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $db->query($query);
            }
        }

    } catch (Exception $e) {
        die("Error al crear la base de datos o ejecutar el script SQL: " . $e->getMessage());
    }
}
