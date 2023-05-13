<?php

/* include_once  '../../models/Conexion.php'; */
include_once  '../../clases/Empleado.php';
$data = array();
$objGetEmpleado = new Empleado;


    $res = $objGetEmpleado->get_datos_horario();
    $data['CONTENIDO'] = "";
    foreach($res as $row){
        
        $data['CONTENIDO'] .= '<tr>';
        $data['CONTENIDO'] .= '<th><button type="button" class="btn btn-success btn-circle" id="edit_btn"
                                    onclick="cargar_datos('.$row['id'].',\''.$row['nombre_puesto'].'\',\''.$row['tareas'].'\',
                                    \''.$row['direccion'].'\',\''.$row['barrio'].'\','.$row['id_empleado'].',
                                    \''.$row['ciudad'].'\',\''.$row['horario'].'\',\''.$row['fecha_puesto'].'\');" ;"><i class="fa fa-pencil"></i></button></th>';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['nombre_puesto'].' </th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['tareas'].'</th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['direccion'].'</th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['barrio'].'</th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['id_empleado'].'</th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['ciudad'].'</th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['horario'].'</th> ';
        $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['fecha_puesto'].'</th> ';
        $data['CONTENIDO'] .= '</tr>';
    }





print_r( json_encode( $data) );