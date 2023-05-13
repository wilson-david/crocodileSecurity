<?php

class Conexion{
    var $conexion;

    function __construct(){
            $this->conexion = mysqli_connect('localhost','root','','c_security');

        
    }

    function Conectarse(){
        if (!$this->conexion) {
            die("Conexión fallida: " . mysqli_connect_error());
        }
        return true;

    }

    
}