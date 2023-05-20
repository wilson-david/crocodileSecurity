<?php

include '../models/Conexion.php';

class Login{
    var $con;

    function Login(){
        $this->con = new Conexion;
    }


    function validar_datos($user){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        $data = [];
        if ($this->con->Conectarse() == true) {

            $query = $this->con->conexion->query("SELECT u.usuario,u.rol,u.estado,e.nombres,e.id as id_empleado,u.contrasenia
                                                FROM usuario u
                                                INNER JOIN empleado e ON e.id = u.id_empleado
                                                WHERE u.usuario = '$user' ");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }

}