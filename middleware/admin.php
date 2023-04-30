<?php 

$admin = function(){
 $role = $_REQUEST['user']['rol'];
 
 if($role !== 'admin'){
    mensaje("No eres administrador !", 403);
 }
}

?>