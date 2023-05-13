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

            $query = $this->con->conexion->query("SELECT * FROM usuario WHERE usuario = '$user' ");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }

}