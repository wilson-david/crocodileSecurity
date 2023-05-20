<?php

/* include_once  '../../models/Conexion.php'; */
include_once '../../clases/Documentos.php';

session_start();
$id_empleado = $_SESSION['id_empleado'];

$data = array();
$objGetEmpleado = new Documentos;

$res = $objGetEmpleado->get_empleado_user($id_empleado);
$data['CONTENIDO'] = "";
foreach($res as $row){
    
    $data['CONTENIDO'] .= '<tr>';
    $data['CONTENIDO'] .= '<th>
                                <a href="desc_nomina.php?nombre='.$row['nombres'].'&apellido='.$row['apellidos'].'&salario='.$row['salario'].'&aux_transporte='.$row['aux_transporte'].'"><i class="fa fa-pencil"></i></a>
                                <a href="certificado_labo.php?nombre='.$row['nombres'].'&apellido='.$row['apellidos'].'&salario='.$row['salario'].'&aux_transporte='.$row['aux_transporte'].'&num_doc='.$row['numero_documento'].'&fecha='.$row['fecha_nacimiento'].'"><i class="fa fa-pencil"></i></a>';
                                
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['nombres'].' </th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['apellidos'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['tipo_documento'].'</th> ';
    $data['CONTENIDO'] .= '<th style="border: 1px solid black;">'.$row['numero_documento'].'</th> ';
    $data['CONTENIDO'] .= '</tr>';
}


print_r( json_encode( $data) );