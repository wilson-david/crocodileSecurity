<?php

include ("../clases/Login.php" );

session_start();

$username = $_POST['username'];
$password = $_POST['password'];

	$objLogin= new Login;

    $res = $objLogin->validar_datos( $username );
    print_r($res);
    die;
?>