
<?php

$tabla = 'chocolate';

$obtenerChocolates = function ($db) {

    global $tabla;

    $chocolates = mysqli_query($db, "SELECT * FROM $tabla");
    $chocolates = mysqli_fetch_all($chocolates, MYSQLI_ASSOC);
    $chocolates = json_encode($chocolates, JSON_UNESCAPED_UNICODE);

    if ($chocolates) {
        http_response_code(200);
        echo $chocolates;
    } else {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
};

$obtenerChocolate = function ($db, $body, $params) {
    global $tabla;

    $id = $params['id'];

    $chocolates = mysqli_query($db, "SELECT * FROM $tabla WHERE id = $id");
    $chocolates = mysqli_fetch_assoc($chocolates);


    if ($chocolates) {
        http_response_code(200);
        $chocolates = json_encode($chocolates, JSON_UNESCAPED_UNICODE);
        echo $chocolates;
    } else {
        echo json_encode([
            "message" => "Chocolate no encontrado"
        ], JSON_UNESCAPED_UNICODE);
    }
};

$crearChocolate = function ($db, $body) {
    global $tabla;


    $nombre = $body['nombre'] ?? '';
    $precio = $body['precio'] ?? '';
    $imagen = $body['imagen'] ?? '';
    $marca = $body['marca'] ?? '';

    //Validaciones
    if (empty($nombre)) {
        mensaje("El nombre es obligatorio", 400);
    }
    if (empty($precio)) {
        mensaje("El precio es obligatorio", 400);
    }
    if (empty($imagen)) {
        mensaje("La imagen es obligatoria", 400);
    }
    if (empty($marca)) {
        mensaje("La marca es obligatoria", 400);
    }

    //Detectar si la imagen es valida
    if (!esImagen($imagen)) {
        mensaje("La imagen no es valida", 400);
    }

    //Guardar la imagen y obtener la ruta
    $rutaImagen = saveBase64Image($imagen); //Retorna la ruta

    //Crear consulta sql 
    $query = "INSERT INTO $tabla(nombre,precio,imagen,marca) VALUES('$nombre', $precio, '$rutaImagen', '$marca')";

    //resultados del query
    $result = mysqli_query($db, $query);


    if ($result) {
        //Si hay respuesta se obtiene su id
        $chocolateId = mysqli_insert_id($db);

        //Se busca ese chocolate 
        $chocolate = mysqli_query($db, "SELECT * FROM $tabla WHERE id = $chocolateId");

        //Codigo 201, Indica que la peticion de crear fue exitosa
        http_response_code(201);

        //Se retorna en formato JSON
        echo json_encode(mysqli_fetch_assoc($chocolate), JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "No se pudo crear el chocolate"], JSON_UNESCAPED_UNICODE);
    }
};

$eliminarChocolate = function ($db, $body, $params) {
    global $tabla;

    $id = $params['id'];

    $chocolates = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM $tabla WHERE id = $id"));

    if (!empty($chocolates)) {
        http_response_code(200);
        mysqli_query($db, "DELETE FROM $tabla WHERE id = $id");
        echo json_encode([
            "message" => "El Chocolate ha sido eliminado correctamente"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "message" => "Chocolate no encontrado"
        ], JSON_UNESCAPED_UNICODE);
    }
};

$actualizandoChocolate = function ($db, $body, $params) {
    global $tabla;

    $nombre = $body['nombre'] ?? '';
    $precio = $body['precio'] ?? '';
    $imagen = $body['imagen'] ?? '';
    $marca = $body['marca'] ?? '';
    $id = $params['id'];

    //Validaciones
    if (empty($nombre)) {
        mensaje("El nombre es obligatorio", 400);
    }
    if (empty($precio)) {
        mensaje("El precio es obligatorio", 400);
    }
    if (empty($imagen)) {
        mensaje("La imagen es obligatoria", 400);
    }
    if (empty($marca)) {
        mensaje("La marca es obligatoria", 400);
    }

    //Detectar si la imagen es valida
    if (!esImagen($imagen)) {
        mensaje("La imagen no es valida", 400);
    }

    //Guardar la imagen y obtener la ruta
    $rutaImagen = saveBase64Image($imagen); //Retorna la ruta

    //Crear consulta sql 
    $query = "UPDATE $tabla 
    SET nombre='$nombre', precio=$precio, imagen='$rutaImagen', marca='$marca' 
    WHERE id = $id";

    $result = mysqli_query($db, $query);


    if ($result) {
        //Se busca ese chocolate 
        $chocolate = mysqli_query($db, "SELECT * FROM $tabla WHERE id = $id");

        $chocolate = mysqli_fetch_assoc($chocolate);

        if(!$chocolate){
            mensaje("El Chocolate no se ha encontrado", 404);
        }

        //Codigo 200, Indica que la peticion fue exitosa
        http_response_code(200);

        //Se retorna en formato JSON
        echo json_encode($chocolate, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "No se pudo actualizar el chocolate"], JSON_UNESCAPED_UNICODE);
    }
};



?>