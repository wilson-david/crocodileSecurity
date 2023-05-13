<?php

/* include_once  '../../models/Conexion.php'; */
include_once '../../clases/Empleado.php';

$data = array();
$objGetEmpleado = new Empleado;

$res = $objGetEmpleado->get_empleado();
$data['CONTENIDO'] = "";
foreach($res as $row){
    
    $data['CONTENIDO'] .= '<tr>';
    $data['CONTENIDO'] .= '<th><button type="button" class="btn btn-success btn-circle" id="edit_btn"
                                onclick="cargar_datos('.$row['id'].',\''.$row['nombres'].'\',\''.$row['apellidos'].'\',
                                '.$row['tipo_documento'].','.$row['numero_documento'].',\''.$row['correo'].'\',
                                '.$row['telefono'].',\''.$row['fecha_nacimiento'].'\',\''.$row['eps'].'\',
                                \''.$row['arl'].'\',\''.$row['cesantias'].'\',\''.$row['pensiones'].'\','.$row['salario'].',
                                '.$row['aux_transporte'].','.$row['estado'].');" ;"><i class="fa fa-pencil"></i></button></th>';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['nombres'].' </th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['apellidos'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['tipo_documento'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['numero_documento'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['correo'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['telefono'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['fecha_nacimiento'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['eps'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['arl'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['cesantias'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['pensiones'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['salario'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['aux_transporte'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['estado'].'</th> ';
    $data['CONTENIDO'] .= '</tr>';
}


print_r( json_encode( $data) );