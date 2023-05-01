<?php 


$registrarUsuario = function($db,$body){

   $nombre    =  $body['nombre'] ?? '';
   $email     =  strtolower($body['email'] ?? '') ?? '';
   $telefono  =  $body['telefono'] ?? '';
   $direccion =  $body['direccion'] ?? '';
   $token     =  bin2hex(random_bytes(8));
   $rol       =  'usuario';

   $password  =  password_hash($body['password'] ?? '', PASSWORD_DEFAULT) ?? '';

    //Validar campos vacios
    if(empty($nombre)){
        mensaje("El nombre es obligatorio", 400);
    }
    if(empty($email)){
        mensaje("El email es obligatorio", 400);
    }
    if(empty($telefono)){
        mensaje("El telefono es obligatorio", 400);
    }
    if(empty($direccion)){
        mensaje("La direccion es obligatorio", 400);
    }
    if(empty($password)){
        mensaje("El password es obligatorio", 400);
    }


    try {
        //Detectar si hay un email ya registrado
        $isEmail = mysqli_query($db,"SELECT * FROM usuarios WHERE email = '$email'");
        $isEmail = mysqli_fetch_assoc($isEmail);

        if($isEmail){
            mensaje("Ese correo ya ha sido utilizado", 400);
        }

        $query = "
        INSERT INTO usuarios (nombre,email,password,telefono,direccion,rol,token) 
        
        VALUES('$nombre', '$email', '$password', '$telefono', '$direccion', '$rol','$token')
        ";

        $result = mysqli_query($db,$query);

        if($result){
                $lastID = mysqli_insert_id($db);
                $user = mysqli_query($db,"SELECT * FROM usuarios where id = $lastID");
                $user = mysqli_fetch_assoc($user);

                http_response_code(201);
                echo json_encode($user,JSON_UNESCAPED_UNICODE);
        }
    } catch (\Throwable $th) {
        mensaje("Ha ocurrido un error",500);
    }


};

$loginUsuario = function($db,$body){
    $email = strtolower($body['email']) ?? '';
    $password = $body['password'] ?? '';

    if(!$email){
        mensaje("El email es obligatorio", 400);
    }
    if(!$password){
        mensaje("La contraseña es obligatoria", 400);
    }

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $user =  mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($user);

    if(!$user){
        mensaje("Las credenciales no son correctas", 401);
        return;        
    }

    if(!password_verify($password, $user['password'])){
        mensaje("Las credenciales no son correctas", 401);
        return;        
    }
    
    $id = $user['id'];
    $token = bin2hex(random_bytes(8));

    mysqli_query($db,"UPDATE usuarios SET token = '$token' WHERE id = $id");

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $user =  mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($user);

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