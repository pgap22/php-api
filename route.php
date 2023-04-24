<?php
//Importamos nuestra funcion startWith
include("./helper.php");
include("./db/db.php");

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

function route($method, $nameRoute, $functions)
{



    //Detectar el metodo
    if ($_SERVER['REQUEST_METHOD'] == $method) {

        //Obtener la URL
        $URL = $_SERVER['PATH_INFO'] ?? ''; //Si no existe PATH_INFO URL sera un string vacio

        //Detectar la url
        if ($URL == $nameRoute) {

            foreach ($functions as $fun) {
                //Tener acceso a la base de datos
                global $db;

                //Obtener datos POST | PUT
                $body = (array) json_decode(file_get_contents("php://input")) ?? '';

                $fun($db, $body);
            }
            exit();
        }


        //Ver si la url tiene paramatros
        $URLParams =  obtener_valores_parametros($nameRoute, $URL);
        

        if (!empty($URLParams)) {

            foreach ($functions as $fun) {
                //Tener acceso a la base de datos
                global $db;

                //Obtener datos POST | PUT
                $body = (array) json_decode(file_get_contents("php://input")) ?? '';

                $fun($db, $body, $URLParams);
            }
        
            
            exit();
        }
    }
}
