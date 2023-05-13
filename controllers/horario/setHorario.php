<?php

/* include_once  '../../models/Conexion.php'; */
include_once  '../../clases/Empleado.php';
$id = $_POST['id'];
$puesto = $_POST['puesto'];
$tareas = $_POST['tareas'];
$direccion = $_POST['direccion'];
$barrio = $_POST['barrio'];
$guarda = $_POST['guarda'];
$ciudad = $_POST['ciudad'];
$horario = $_POST['horario'];
$fecha = $_POST['fecha'];
$estado = isset($_POST['estado']) ? $_POST['estado'] : 1;

$fecha_sistema = date('Y-m-d');

$objInsert = new Empleado;
$objNomPuesto = new Empleado;

$res = $objNomPuesto->get_validar_nom_puesto($puesto);
/* echo "<pre  >";
print_r($res);
die; */


if ($id == 0) {
    if (empty($res)) {
        if ($objInsert->crear_horario($puesto, $tareas, $direccion, $barrio, $guarda, $ciudad, $horario, $fecha, $fecha_sistema)) {
            $data_return['success'] = 'success';
        } else {
            $data_return['error'] = 'error';
        }
    } else {
        $data_return['error'] = 'nombre_duplicado';
    }
} else {
    if ($objInsert->modificar_horario($id, $puesto, $tareas, $direccion, $barrio, $guarda, $ciudad, $horario, $fecha, $fecha_sistema,$estado)) {
        $data_return['success'] = 'update';
    } else {
        $data_return['error'] = 'error';
    }
}




print_r( json_encode( $data_return) );
?>