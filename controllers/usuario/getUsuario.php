<?php

include_once  '../../clases/Usuario.class.php';

$data = array();
$objGetEmpleado = new Usuario;

$res = $objGetEmpleado->get_usuario();
$data['CONTENIDO'] = "";
foreach($res as $row){
    
    $data['CONTENIDO'] .= '<tr>';
    $data['CONTENIDO'] .= '<th><button type="button" class="btn btn-success btn-circle" id="edit_btn"
                                onclick="cargar_datos('.$row['id'].',\''.$row['usuario'].'\',\''.$row['contrasenia'].'\',\''.$row['rol'].'\','.$row['estado'].','.$row['id_empleado'].');" ;"><i class="fa fa-pencil"></i></button></th>';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['usuario'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['rol'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['estado'].'</th> ';
    $data['CONTENIDO'] .= '</tr>';
}


print_r( json_encode( $data) );