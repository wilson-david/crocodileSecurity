<?php

include_once("conexion.php");

class RegistroLogin{

    var $con;
    var $insert_id;
    var $has_value;
    var $consulta;
    var $array_campos;
    var $array_campos2;
    var $usuario;
    var $value;
    var $total;

    function RegistroLogin(){
        $this->con = new conexion;
        $this->insert_id;
        $this->has_value = false;
        $this->consulta;
        $this->array_campos = Array();
        $this->array_campos2 = Array();
        $this->usuario = Array();
        $this->total;
        $this->value;
    }

    function validar_user( $NUMERO_DE_CEDULA ){
        $this->total = array();
        $this->has_value = false;

        if($this->con->Conectarse()==true){
            $query = "";


            $this->consulta = oci_parse($this->con->conect, $query);
            oci_bind_by_name($this->consulta, ':CEDULA', $NUMERO_DE_CEDULA) ;

            if(oci_execute($this->consulta)){
                $row = oci_fetch_assoc($this->consulta);
                if( is_array($row) ){
                    $this->has_value = true;
                    $this->usuario = $row;
                }
            }
        } 
        return $this->has_value;
    }
    
}