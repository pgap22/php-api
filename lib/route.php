<?php
//Importamos nuestra funcion startWith
require("./lib/helper.php");
require("./db/db.php");

//Permite que cualquier persona utiilize nuestra API
header('Access-Control-Allow-Origin: *');

//Traduce los datos en JSON y cambia a utf-8 por las ñññññ
header('Content-Type: application/json; charset=utf-8');

function route($method, $nameRoute, $functions)
{


    //Detectar el metodo
    if ($_SERVER['REQUEST_METHOD'] == $method) {

        //Obtener la URL
        $URL = $_SERVER['REQUEST_URI'] ?? ''; //Si no existe REQUEST_URI URL sera un string vacio
        //Detectar la url
        if ($URL == $nameRoute) {

            foreach ($functions as $fun) {
                //Tener acceso a la base de datos
                global $db;

                //Obtener datos POST | PUT
                $body = (array) json_decode(file_get_contents("php://input"),true) ?? '';

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
