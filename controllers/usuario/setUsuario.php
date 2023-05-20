<?php

include_once  '../../clases/Usuario.class.php';

$id = $_POST['id'];
/* $nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$tipo_doc = $_POST['tipo_doc'];
$num_doc = $_POST['num_doc'];
$correo = $_POST['correo']; */
$usuario = $_POST['usuario'];
$rol = $_POST['rol'];
$id_empleado =  isset($_POST['id_empleado']) ? $_POST['id_empleado'] : 0;
$pass = isset($_POST['pass']) ? $_POST['pass'] : 0;
$estado = isset($_POST['estado']) ? $_POST['estado'] : 0;

$fecha_sistema = date('Y-m-d');

$objInsert = new Usuario;

if($id == 0){
    if($objInsert->registrar_usuario($usuario,$rol,$fecha_sistema,$id_empleado)){
        $data_return['success'] = 'success';
    }else{
        $data_return['error'] = 'error';
    }
}else{
    if($objInsert->actualizar_usuario($id,$usuario,$rol,$fecha_sistema,$pass,$estado)){
        $data_return['success'] = 'update';
    }else{
        $data_return['error'] = 'error';
    }
}



print_r( json_encode( $data_return) );
?>