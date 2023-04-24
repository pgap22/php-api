<?php 


$registrarUsuario = function($db,$body){

   $nombre =  $body['nombre'];
   $email =  $body['email'];
   $password =  password_hash($body['password'], PASSWORD_DEFAULT);

    try {
        //Detectar si hay un email ya registrado
        $isEmail = mysqli_query($db,"SELECT * FROM usuarios WHERE email = '$email'");
        $isEmail = mysqli_fetch_assoc($isEmail);

        if($isEmail){
            http_response_code(400);
            echo json_encode([
                "message"=> "Ese correo ya ha sido utilizado"
            ],JSON_UNESCAPED_UNICODE);
            return;
        }


        $query = "INSERT INTO usuarios (nombre, email,password) VALUES('$nombre', '$email', '$password')";

        $result = mysqli_query($db,$query);

        if($result){
                $lastID = mysqli_insert_id($db);
                $user = mysqli_query($db,"SELECT * FROM usuarios where id = $lastID");
                $user = mysqli_fetch_assoc($user);

                http_response_code(201);
                echo json_encode($user,JSON_UNESCAPED_UNICODE);
        }
    } catch (\Throwable $th) {
        http_response_code(500);
        echo json_encode([
            "message" => "Ha ocurrido un error",
        ]);
    }


};

$loginUsuario = function($db,$body){
    $email = $body['email'];
    $password = $body['password'];

    if(!$email){
        http_response_code(400);
        echo json_encode([
            "message" => "Email es obligatorio"
        ],JSON_UNESCAPED_UNICODE);
    }
    if(!$password){
        http_response_code(400);
        echo json_encode([
            "message" => "Email es obligatorio"
        ],JSON_UNESCAPED_UNICODE);
    }

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $user =  mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($user);

    if(!$user){
        http_response_code(401);
        echo json_encode([
            "message" => "El usuario o contraseña no es el correcto"
        ],JSON_UNESCAPED_UNICODE);
        return;        
    }

    if(!password_verify($password, $user['password'])){
        http_response_code(401);
        echo json_encode([
            "message" => "El usuario o contraseña no es el correcto"
        ],JSON_UNESCAPED_UNICODE);
        return;        
    }
    
    $id = $user['id'];
    $token = bin2hex(random_bytes(8));

    mysqli_query($db,"UPDATE usuarios SET token = '$token' WHERE id = $id");

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $user =  mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($user);

    http_response_code(201);
    echo json_encode($user,JSON_UNESCAPED_UNICODE);
};

$obtenerPerfil = function($db, $body){
    if(!empty($_REQUEST['user'])){
        http_response_code(200);
        echo json_encode($_REQUEST['user'],JSON_UNESCAPED_UNICODE);
    }else{
        http_response_code(500);
        echo json_encode([
            "message" => "No estas autenticado",
        ]);
    }
};