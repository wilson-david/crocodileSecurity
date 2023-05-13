<?php

/* include_once  '../../models/Conexion.php'; */
include_once  '../../clases/Empleado.php';
$data = array();
$objGetEmpleado = new Empleado;



    $res = $objGetEmpleado->get_empleado_horario();
    $data['CONTENIDO'] = "";
    foreach($res as $row){
        
        $data['CONTENIDO'] .= '<option value='.$row['id'].'>'.$row['nombres'].'</option>';
    }
    




print_r( json_encode( $data) );