<?php

include '../../models/Conexion.php';

class Usuario{

    var $con;

    function Usuario(){
        $this->con = new Conexion;
    }

    function registrar_usuario($usuario,$rol,$fecha_crea,$id_empleado) {
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "INSERT INTO usuario 
                        (usuario,rol,fecha_crea,id_empleado)
                    VALUES ('$usuario','$rol','$fecha_crea',$id_empleado)";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }

    function actualizar_usuario($id,$usuario,$rol,$fecha_sistema,$pass,$estado) {
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "UPDATE usuario 
                        SET usuario = '$usuario',
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

            $query = $this->con->conexion->query("SELECT u.id,u.usuario,u.rol,u.estado,e.id as id_empleado,u.contrasenia
                                                        FROM usuario u
                                                        INNER JOIN empleado e ON e.id = u.id_empleado ");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }
}