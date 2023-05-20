<?php

include '../../models/Conexion.php';

class Documentos{

    var $con;

    function Empleado(){
        $this->con = new Conexion;
    }

    function get_empleado_user($id){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        $data = [];
        if ($this->con->Conectarse() == true) {

            $query = $this->con->conexion->query("SELECT * FROM empleado where id = $id");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }
    
}