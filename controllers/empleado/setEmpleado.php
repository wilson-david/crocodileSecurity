<?php

/* include_once  '../../models/Conexion.php'; */
include_once  '../../clases/Empleado.php';

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$tipo_doc = $_POST['tipo_doc'];
$num_doc = $_POST['num_doc'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$fecha = $_POST['fecha'];
$eps = $_POST['eps'];
$arl = $_POST['arl'];
$cesantia = $_POST['cesantia'];
$pension = $_POST['pension'];
$salario = $_POST['salario'];
$aux_transporte = $_POST['aux_transporte'];
$estado = isset($_POST['estado']) ? $_POST['estado'] : 0;
$fecha_sistema = date('Y-m-d');

$objInsert = new Empleado;

if($id == 0){
    if($objInsert->registrar_empleado($nombre,$apellido,$tipo_doc,$num_doc,$correo, $telefono, $fecha, $eps, $arl, $cesantia, $pension, $salario, $aux_transporte, $fecha_sistema)){
        $data_return['success'] = 'success';
    }else{
        $data_return['error'] = 'error';
    }
}else{
    if($objInsert->actualizar_empleado($id,$nombre,$apellido,$tipo_doc,$num_doc,$correo, $telefono, $fecha, $eps, $arl, $cesantia, $pension, $salario, $aux_transporte, $fecha_sistema,$estado)){
        $data_return['success'] = 'update';
    }else{
        $data_return['error'] = 'error';
    }
}



print_r( json_encode( $data_return) );
?>