<?php  


$obtenerChocolates = function($db){
    $chocolates = mysqli_query($db,"SELECT * FROM chocolates");
    $chocolates = mysqli_fetch_all($chocolates, MYSQLI_ASSOC);
    $chocolates = json_encode($chocolates,JSON_UNESCAPED_UNICODE);

    if($chocolates){
        http_response_code(200);
        echo $chocolates;
    }
    else{
        echo json_encode([],JSON_UNESCAPED_UNICODE);
    }
};

$obtenerChocolate = function($db,$body,$params){
    $id = $params['id'];

    $chocolates = mysqli_query($db,"SELECT * FROM chocolates WHERE id = $id");
    $chocolates = mysqli_fetch_assoc($chocolates);
   

    if($chocolates ){
        http_response_code(200);
        $chocolates = json_encode($chocolates,JSON_UNESCAPED_UNICODE);
        echo $chocolates;
    }
    else{
        echo json_encode([
            "message" => "Chocolate no encontrado"
        ],JSON_UNESCAPED_UNICODE);
    }
};

$crearChocolate = function($db,$body){    
    $nombre = $body['nombre'];
    $precio = $body['precio'];

    //Crear consulta sql 
    $query = "INSERT INTO chocolates(nombre,precio) VALUES('$nombre', $precio)";

    $result = mysqli_query($db, $query);
    

    if($result){
        //Si hay respuesta se obtiene su id
        $chocolateId = mysqli_insert_id($db);

        //Se busca ese chocolate 
        $chocolate = mysqli_query($db, "SELECT * FROM chocolates WHERE id = $chocolateId");
        
        //Codigo 201, Indica que la peticion de crear fue exitosa
        http_response_code(201);
        
        //Se retorna en formato JSON
        echo json_encode(mysqli_fetch_assoc($chocolate),JSON_UNESCAPED_UNICODE);

    } else {
        http_response_code(500);
        echo json_encode(["message" => "No se pudo crear el chocolate"],JSON_UNESCAPED_UNICODE);
    }
    
};

$eliminarChocolate = function($db,$body,$params){
    $id = $params['id'];

    $chocolates = mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM chocolates WHERE id = $id"));

    if(!empty($chocolates)){
        http_response_code(200);
        mysqli_query($db,"DELETE FROM chocolates WHERE id = $id");   
        echo json_encode([
            "message" => "El Chocolate ha sido eliminado correctamente"
        ],JSON_UNESCAPED_UNICODE);
    }

    else{
        echo json_encode([
            "message" => "Chocolate no encontrado"
        ],JSON_UNESCAPED_UNICODE);
    }
};

$actualizandoChocolate = function($db,$body,$params){

    $nombre = $body['nombre'];
    $precio = $body['precio'];
    $id = $params['id'];

    //Crear consulta sql 
    $query = "UPDATE chocolates SET nombre='$nombre',precio=$precio WHERE id = $id ";

    $result = mysqli_query($db, $query);
    

    if($result){

        //Si hay respuesta se obtiene su id
        $chocolateId = mysqli_insert_id($db);

        //Se busca ese chocolate 
        $chocolate = mysqli_query($db, "SELECT * FROM chocolates WHERE id = $chocolateId");
        
        //Codigo 201, Indica que la peticion de crear fue exitosa
        http_response_code(201);
        
        //Se retorna en formato JSON
        echo json_encode(mysqli_fetch_assoc($chocolate),JSON_UNESCAPED_UNICODE);

    } else {
        http_response_code(500);
        echo json_encode(["message" => "No se pudo crear el chocolate"],JSON_UNESCAPED_UNICODE);
    }

};



?>