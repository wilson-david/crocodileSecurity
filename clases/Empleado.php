<?php

include '../../models/Conexion.php';

class Empleado{

    var $con;

    function Empleado(){
        $this->con = new Conexion;
    }


    function registrar_empleado(
        $nombre,
        $apellido,
        $tipo_doc,
        $num_doc,
        $correo,
        $telefono,
        $fecha,
        $eps,
        $arl,
        $cesantia,
        $pension,
        $salario,
        $aux_transporte,
        $fecha_sistema
    ) {
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "INSERT INTO empleado 
                        (nombres,
                        apellidos,                     
                        tipo_documento,
                        numero_documento,
                        correo,
                        telefono,
                        fecha_nacimiento,
                        eps,
                        arl,
                        cesantias,
                        pensiones,
                        salario,
                        aux_transporte,
                        estado,
                        fecha_crea)
                    VALUES ('$nombre','$apellido',$tipo_doc,'$num_doc','$correo',
                    '$telefono','$fecha','$eps','$arl','$cesantia',
                    '$pension',$salario,$aux_transporte,1,'$fecha_sistema')";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }

    function actualizar_empleado(
        $id,
        $nombre,
        $apellido,
        $tipo_doc,
        $num_doc,
        $correo,
        $telefono,
        $fecha,
        $eps,
        $arl,
        $cesantia,
        $pension,
        $salario,
        $aux_transporte,
        $fecha_sistema,
        $estado){
        if ($this->con->Conectarse() == true) {
            $query = "UPDATE empleado
                        SET nombres = '$nombre',
                            apellidos = '$apellido',                     
                            tipo_documento = $tipo_doc,
                            numero_documento = $num_doc,
                            correo = '$correo',
                            telefono = $telefono,
                            fecha_nacimiento = '$fecha',
                            eps = '$eps',
                            arl = '$arl',
                            cesantias = '$cesantia',
                            pensiones = '$pension',
                            salario = $salario,
                            aux_transporte = $aux_transporte ,
                            fecha_actualiza  = '$fecha_sistema',
                            estado = $estado
                            WHERE id = $id ";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }


    function get_empleado(){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        $data = [];
        if ($this->con->Conectarse() == true) {

            $query = $this->con->conexion->query("SELECT * FROM empleado ");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }


    function get_empleado_horario(){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        $data = [];
        if ($this->con->Conectarse() == true) {

            $query = $this->con->conexion->query("SELECT id,CONCAT(numero_documento,' - ',nombres,' ',apellidos) AS nombres FROM empleado ");
            
            $i=0;
            while($fila = $query->fetch_assoc()){
                $data[$i] = $fila;
                $i++;
            }
        }
        return $data;
    }



//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //Horario
    function crear_horario($puesto, $tareas, $direccion, $barrio, $guarda, $ciudad, $horario, $fecha, $fecha_sistema){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "INSERT INTO horario 
                        (nombre_puesto,tareas,direccion, barrio, id_empleado,
                        ciudad,horario,fecha_puesto,fecha_crea)
                    VALUES ('$puesto','$tareas','$direccion','$barrio',$guarda,'$ciudad','$horario','$fecha','$fecha_sistema')";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }

    // actualziar horario
    function modificar_horario($id,$puesto, $tareas, $direccion, $barrio, $guarda, $ciudad, $horario, $fecha, $fecha_sistema){
        if ($this->con == null) {
            $this->con = new Conexion();
        }
        if ($this->con->Conectarse() == true) {
            $query = "UPDATE horario
                        SET nombre_puesto = '$puesto',
                            tareas = '$tareas',                     
                            direccion = '$direccion',
                            barrio = '$barrio',
                            id_empleado = $guarda,
                            ciudad = '$ciudad',
                            horario = '$horario',
                            fecha_puesto = '$fecha'
                            WHERE id = $id ";


            if (mysqli_query($this->con->conexion, $query)) {
                return true;
            }
            return false;
        }
    }

         //datos horario
        function get_datos_horario(){
            if ($this->con == null) {
                $this->con = new Conexion();
            }
            $data = [];
            if ($this->con->Conectarse() == true) {
                $query = $this->con->conexion->query("SELECT * FROM horario ");
                
                $i=0;
                while($fila = $query->fetch_assoc()){
                    $data[$i] = $fila;
                    $i++;
                }
            }
            return $data;
        }

        function get_nombre_puesto(){
            if ($this->con == null) {
                $this->con = new Conexion();
            }
            $data = [];
            if ($this->con->Conectarse() == true) {
                $query = $this->con->conexion->query("SELECT * FROM horario ");
                
                $i=0;
                while($fila = $query->fetch_assoc()){
                    $data[$i] = $fila;
                    $i++;
                }
            }
            return $data;
        }


        function get_validar_nom_puesto($puesto){
            if ($this->con == null) {
                $this->con = new Conexion();
            }
            $data = [];
            if ($this->con->Conectarse() == true) {
                $query = $this->con->conexion->query("SELECT CASE WHEN id IS NULL THEN 0 ELSE id END AS puesto
                                                            FROM horario WHERE nombre_puesto LIKE '%$puesto%'; ");
                
                $i=0;
                while($fila = $query->fetch_assoc()){
                    $data[$i] = $fila;
                    $i++;
                }
            }
            return $data;
        }
    
}