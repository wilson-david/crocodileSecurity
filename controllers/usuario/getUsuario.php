<?php

include_once  '../../clases/Usuario.class.php';

$data = array();
$objGetEmpleado = new Usuario;

$res = $objGetEmpleado->get_usuario();
$data['CONTENIDO'] = "";
foreach($res as $row){
    
    $data['CONTENIDO'] .= '<tr>';
    $data['CONTENIDO'] .= '<th><button type="button" class="btn btn-success btn-circle" id="edit_btn"
                                onclick="cargar_datos('.$row['id'].',\''.$row['nombres'].'\',\''.$row['apellidos'].'\',
                                \''.$row['tipo_documento'].'\',\''.$row['documento'].'\',\''.$row['correo'].'\',
                                '.$row['estado'].',\''.$row['usuario'].'\',\''.$row['contrasenia'].'\',\''.$row['rol'].'\');" ;"><i class="fa fa-pencil"></i></button></th>';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['nombres'].' </th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['apellidos'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['tipo_documento'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['documento'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['correo'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['usuario'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['rol'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['estado'].'</th> ';
    $data['CONTENIDO'] .= '</tr>';
}


print_r( json_encode( $data) );