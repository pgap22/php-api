<?php 

$autenticado = function($db,$body){
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';

   

    if(empty($headers['Authorization']) || !explode("Bearer ", $token)[1]){
        http_response_code(403);
        echo json_encode([
            "message"=> "No estas autenticado"
        ],JSON_UNESCAPED_UNICODE);

        exit();
    }

    try {
        $token = explode("Bearer ", $token)[1];
        $query = "SELECT * FROM usuarios WHERE token = '$token'";
    
        $user = mysqli_fetch_assoc(mysqli_query($db, $query));

        if($user){
            $_REQUEST['user'] = $user;
        }


    } catch (\Throwable $th) {
        http_response_code(403);
        echo json_encode([
            "message"=> "No estas autenticado"
        ],JSON_UNESCAPED_UNICODE);

        exit(); 
    }

};
