<?php

include ("../clases/Login.php" );

session_start();

$username = $_POST['username'];
$password = $_POST['password'];

	$objLogin= new Login;

    $res = $objLogin->validar_datos( $username );
    
    if(!empty($res)){
        if($username == $res[0]['usuario'] && $password == $res[0]['contrasenia']){
            $_SESSION["rol"] = $res[0]['rol'];
            $_SESSION["usuario"] = $res[0]['rol'];
            $_SESSION["id_empleado"] = $res[0]['id_empleado'];
            $_SESSION["nombre"] = $res[0]['nombres'];
            $data_return['success'] = 'success';
        }else{
            $data_return['success'] = 'datos_erroneos';
        }
        
    }else{
        $data_return['user_dose_not_exist'] = 'error';
    }
    print_r( json_encode( $data_return) );
?>