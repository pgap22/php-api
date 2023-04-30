<?php  

$obtenerFeedback = function($db,$body){
    //Obtener feedback
    $query = "SELECT feedback.*, usuarios.nombre FROM feedback JOIN usuarios ON feedback.id_usuario=usuarios.id";

    $feedback = mysqli_query($db,$query);
    $feedback = mysqli_fetch_all($feedback, MYSQLI_ASSOC);

    echo json_encode($feedback);
};

$crearFeedback = function($db, $body){
    //Obtener datos de feedback
    $comentario = $body['comentario'] ?? '';
    $rating     = intval($body['rating'] ?? '');
    $usuarioID  = $_REQUEST['user']['id'];

    //Detectar si estan vacios
    if(empty($comentario)){
        mensaje("El comentario es obligatorio", 400);
    }
    if(empty($rating)){
        mensaje("El rating es obligario", 400);
    }

    //Rating debe ser del 1-5
    if($rating < 0 | $rating > 5){
        mensaje("El rating no es valido, debe ser del 1-5", 400);
    }

    $query = "INSERT INTO feedback(comentario,rating,id_usuario) VALUES('$comentario', $rating, $usuarioID)";
    mysqli_query($db, $query);

    $idFeedback = mysqli_insert_id($db);

    $query = "SELECT feedback.*, usuarios.nombre FROM feedback JOIN usuarios ON feedback.id_usuario=usuarios.id WHERE feedback.id = $idFeedback";
    
    $feedback = mysqli_query($db, $query);
    $feedback = mysqli_fetch_assoc($feedback);

    echo json_encode($feedback, JSON_UNESCAPED_UNICODE);
};
