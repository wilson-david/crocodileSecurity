<?php

include '../../models/Conexion.php';

class Usuario{

    var $con;

    function Usuario(){
        $this->con = new Conexion;
    }

    function registrar_usuario($nombre,$apellido,$tipo_doc,$num_doc,$correo,$usuario,$rol,$fecha_crea
    ) {
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "INSERT INTO usuario 
                        (tipo_documento,documento,correo,nombres,apellidos,usuario,rol,fecha_crea)
                    VALUES ('$tipo_doc','$num_doc','$correo','$nombre','$apellido','$usuario','$rol','$fecha_crea')";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }

    function actualizar_usuario($id,$nombre,$apellido,$tipo_doc,$num_doc,$correo,$usuario,$rol,$fecha_sistema,$pass,$estado) {
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "UPDATE usuario 
                        SET tipo_documento = '$tipo_doc',
                            documento = '$num_doc',
                            correo = '$correo',
                            nombres = '$nombre',
                            apellidos = '$apellido',
                            usuario = '$usuario',
                            rol = '$rol',
                            estado = $estado,
                            contrasenia  = '$pass',
                            fecha_actualiza = '$fecha_sistema'
                            WHERE id = $id ";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }

    function get_usuario(){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        $data = [];
        if ($this->con->Conectarse() == true) {

            $query = $this->con->conexion->query("SELECT * FROM usuario ");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }
}