<?php
//Carga masiva
use utils\CargaMasivaUtils;
use utils\FileUtils;
use utils\ArrayUtils;
use utils\FormatDataUtils;

//Nivel en el que se encuentra el archivo
define('LEVEL_FILE', '../../../../../');

//Includes
include_once LEVEL_FILE . "clases/data.php";

//Autoloaders
include_once "../Autoloader.php";
include_once "../../utils/Autoloader.php";


UtilsAutoloader::register();
DataAutoloader::register();


//Variables
$CEDULA = $_SESSION['CEDULA'];
$cliente = $_POST['cliente_id'];
$fecha_hora = date('Y-m-d H:i:s');
$hora = date('H:i');
$ruta_archivo_cargado = '../../archivos_cargados/carga_empleado';


$f_delimitador = false;
$separador = ",";

$array_data = array();
$array_data['mensaje_error'] = array();
$error_fila = array();

$array_encabezado = array(
    "ID REGISTRO",
    "ID CLIENTE*",
    "CLIENTE",
    "ID EVENTO*",
    "NOMBRE EVENTO",
    "ID ASESOR*",
    "NOMBRE ASESOR",
    "ID MATERIAL*",
    "NOMBRE MATERIAL*",
    "CANTIDAD*",
    "FECHA RECIBIDO*",
    "OBSERVACION"
);

$fileExts = array(".csv", ".xls", ".xlsx");

try {

    $resultFile = $cargaMasivaUtils->setFile(null, array(
        "temp" => $_FILES['archivo']['tmp_name'],
        "size" => $_FILES['archivo']['size'],
        "URL" => $ruta_archivo_cargado,
        "name" => $_FILES['archivo']['name'],
    ));

    switch ($cargaMasivaUtils->validateExt($fileExts)) {
        case FileUtils::EXT_NOT_FOUND:
            $array_data['mensaje_error'][] = utf8_encode("No existe información de la extensión del archivo cargado");
            $cargaMasivaUtils->terminateLoad($array_data);
            break;
        case FileUtils::EXT_UNKNOWN:
            $array_data['mensaje_error'][] = utf8_encode("El tipo de archivo cargado no es válido");
            $cargaMasivaUtils->terminateLoad($array_data);
            break;
    }

    $cargaMasivaUtils->getFileDataCargaMasiva();

} catch (Exception $ex) {
    $array_data['mensaje_error'][] = utf8_encode($ex->getMessage());
    $cargaMasivaUtils->terminateLoad($array_data);
}

$cargaMasivaUtils->setHeader($array_encabezado);
$data2Load = $cargaMasivaUtils->file->fileData['body'];

try {
    $array_encabezado_cargado = $cargaMasivaUtils->file->fileData['header'];
    $array_encabezado = $cargaMasivaUtils->header;
    $empleado_cargados = array_slice($array_encabezado_cargado, 15, sizeof($array_encabezado_cargado) - 14 );


    if (count($array_encabezado_cargado) != (count($array_encabezado) + count($empleado_cargados))) {
        $array_data['mensaje_error'][] = 'El tamaño del encabezado no corresponde.';
        $cargaMasivaUtils->terminateLoad($array_data);
    }
    if (!isset($array_data['mensaje_error'])) {
        foreach ($array_encabezado as $key => $row) {
            if ($key < 12 || ($key == count($data2Load) - 1)) {
                if (trim($array_encabezado_cargado[$key]) != trim($array_encabezado[$key])) {
                    $array_data['mensaje_error'][] = 'El encabezado ingresado no corresponde con el solicitado.';
                    $cargaMasivaUtils->terminateLoad($array_data);
                    break;
                }
            }
        }
    }
} catch (Exception $me) {
    $array_data['mensaje_error'][] = utf8_encode($me->getMessage());
    $cargaMasivaUtils->terminateLoad($array_data);
}
if($depuracion){
    $fp=fopen($ruta_log,'a');
    fwrite($fp,"\n ".date("H:i:s")." inventarios_cargados_".$CEDULA." pasa zona primera de encabezados \n");
    fclose($fp);
}
$errores_duplic = array();

try {
    $uniqueKeys = array(
        ID_REGISTRO,
        ID_EVENTO,
        ID_ASESOR,
        ID_MATERIAL
    );
    $errores_duplic = $cargaMasivaUtils->validateDuplicateRecords($uniqueKeys);
} catch (Exception $e) {
    $array_data['mensaje_error'][] = utf8_encode($e->getMessage());
    $cargaMasivaUtils->terminateLoad($array_data);
}

if($depuracion){
    $fp=fopen($ruta_log,'a');
    fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." duplilcados ".var_export($errores_duplic, true)." \n");
    fclose($fp);
}

//Se re-inicializa el header para generacion correcta de archivo de errores.
$cargaMasivaUtils->header = $cargaMasivaUtils->file->fileData['header'];
$errores = array();

$mapCliente = $mapEvento = $mapAsesor = array();

//Creación de mapas--------------------------
getValidationData($data2Load, $mapCliente, $mapEvento, $mapAsesor);
//\\Creación de mapas------------------------

if($depuracion){
    $fp=fopen($ruta_log,'a');
    fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." termina creacion de mapas \n");
    fwrite($fp,"\n ".date("H:i:s")."clientes totales ".count($mapCliente)." \n");
    fwrite($fp,"\n ".date("H:i:s")."eventos totales ".count($mapEvento)." \n");
    fwrite($fp,"\n ".date("H:i:s")."asesores totales ".count($mapAsesor)." \n");
    fclose($fp);
}

$ParametroMatVal = '';
$inventarios_bd = array();
$inventarios_edit = array();

//Validación de datos------------------------
foreach ($data2Load as $idx => $row) {

    if($depuracion){
        $fp=fopen($ruta_log,'a');
        fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." validacion campos carga \n");
        fwrite($fp,"\n ".date("H:i:s")."linea validada ".$idx." \n");
        fwrite($fp,"\n ".date("H:i:s")."data validada ".var_export($row, true)." \n");
        fclose($fp);
    }

    if(!empty($row[ID_REGISTRO])){
        if(empty($row[OBSERVACION])){
            $errores[$idx][] = "No se puede editar el registro si no posee OBSERVACIÓN";
        }
    }

    $objMaterialGenIVal = new MacInvAse;

    $objMaterialGenIVal->materialGen($row[ID_MATERIAL]);
    $MatGenerico = $objMaterialGenIVal->array_campos;

    if (!empty($MatGenerico[0]['MAT_GEN_ID'])) {
        $ParametroMatVal = $MatGenerico[0]['MAT_GEN_ID'];
    } else {
        $ParametroMatVal = $row[ID_MATERIAL];
    }

    //validamos si existe un material para ese asesor y ese evento,
    //en caso que exista y con las operaciones de menor a 0 no se podra crear 
    $Sumatoria = 0;
    $objMacInvSql = new MacInvAse;

    $objMacInvSql->valorMaterial($row[ID_EVENTO], $row[ID_ASESOR], $ParametroMatVal);
    $inventarioId = '';
    if($objMacInvSql->has_value){
        foreach ($objMacInvSql->array_campos as $datosMaterial) {
            $inventarioId = $datosMaterial['INV_ASE_ID'];
            if ($datosMaterial['OPERACION'] == 'SUMA') {
                $Sumatoria = $Sumatoria + $datosMaterial['CANTIDAD'];
            } else if ($datosMaterial['OPERACION'] == 'RESTA' 
                || $datosMaterial['OPERACION'] == 'AJUSTE') {
                    //si es una resta guardamo el valor restado en el id
                    if ($datosMaterial['OPERACION'] == 'RESTA'){
                        $inventarios_edit[$datosMaterial['INV_ASE_HIST_ID']] = $datosMaterial['CANTIDAD'];
                    }
                $Sumatoria = $Sumatoria - $datosMaterial['CANTIDAD'];
            }
        }
    }

    if(isset($inventarios_bd[$inventarioId])){
        if(!empty($row[ID_REGISTRO])){
            $Sumatoria =   $inventarios_bd[$inventarioId] + $inventarios_edit[$row[ID_REGISTRO]] - $row[CANTIDAD];
        }else{
            $Sumatoria =   $row[CANTIDAD] + $inventarios_bd[$inventarioId];
        }
    }else{
        $Sumatoria =   $row[CANTIDAD] + $Sumatoria;
    }

    if($Sumatoria >= 0){
        if(isset($inventarioId)){
            $inventarios_bd[$inventarioId] = $Sumatoria;  
        }
    }else{
        $errores[$idx][] = "No se puede editar por que las unidades disponibles quedarían menores a cero";
    }


    $objClientesql = new Cliente;
    //ID CLIENTE
    if (!empty($row[ID_CLIENTE])) {
        if (!is_numeric($row[ID_CLIENTE])) {
            $errores[$idx][] = "El campo ID CLIENTE debe ser un número";
        } else { 
            $clienteKey = ArrayUtils::searchAssociative(array($row[ID_CLIENTE]), $mapCliente, array("CLIENTE_ID"));
            $objResCliente = $objClientesql->cliente_inv_asesor($row[ID_CLIENTE],1);
            if ($clienteKey === null) {
                $errores[$idx][] = "El ID CLIENTE no existe";
            }
            if (empty($objResCliente)){
                $errores[$idx][] = "El Cliente no puede realizar la carga porque no tiene activo la opción de inventario por Asesor.";
            }
        }
    } else {
        $errores[$idx][] = "El campo ID CLIENTE es obligatorio";
    }

    //ID MATERIAL
    $materialValidacion = $objMacMaterialVal->obtenerArticuloPorCodigo($row[ID_MATERIAL]);
    $statusLab = array();
    if(isset($materialValidacion['data'])){
        $statusLab = (array)$materialValidacion['data'];
    }
    
    $objMaterialGen->materialGen($row[ID_MATERIAL]);
    $objValMatGen = $objMaterialGen->has_value;
    $objValMatGenData = $objMaterialGen->array_campos;

    $objeClienteProv = new Cliente();
    $objeClienteProv->proveedorCliente($row[ID_CLIENTE]);
    $dataMaterial = $objeClienteProv->array_campos;  


    if(!empty($statusLab) || !empty($objValMatGen)){
        if (!empty($statusLab)) {
            if($materialValidacion['data']->arreglo_respuestas->proveedor != $dataMaterial[0]['EQUIVALENCIA_CEDIS']){
                $errores[$idx][] = "El Material no esta asosciado al cliente";
            }
            $tipoProcLab = $materialValidacion['data']->arreglo_respuestas->tipo_producto;
            if ($tipoProcLab !== 'M') {
                $errores[$idx][] = "El Material no es tipo premio";
            }
        }

        if (!empty($objValMatGen)) {
            if($objValMatGenData[0]['PROVEEDOR'] != $dataMaterial[0]['EQUIVALENCIA_CEDIS'] ){
                $errores[$idx][] = "El Material no esta asosciado al cliente";
            }
            $tipoProcGen = $objValMatGenData[0]['NOMBRE_TIPO_MATERIAL'];
            if ($tipoProcGen !== 'PREMIOS') {
                $errores[$idx][] = "El Material no es tipo premio";
            }
        }
    }else{
        $errores[$idx][] = "El Material no existe";
    }
    
    $objEventoSql = new EventoNutresa;
    //ID EVENTO
    if (!empty($row[ID_EVENTO])) {
        $eventoKey = ArrayUtils::searchAssociative(array($row[ID_EVENTO]), $mapEvento, array("EVENTO_NUTRESA_ID"));
        if ($eventoKey === null) {
            $errores[$idx][] = "El EVENTO no existe";
        }else{
             $objEventoRes = $objEventoSql->obtenerEventosCliente($row[ID_EVENTO],$row[ID_CLIENTE]);
            if ($objEventoRes != 1) {
               $errores[$idx][] = "El EVENTO no está asociado al cliente";
            }
        }
    }else{
        $errores[$idx][] = "El campo ID EVENTO nes obligatorio";

    }

    //CANTIDAD
    if ($row[CANTIDAD] == "") {
        $errores[$idx][] = "El campo CANTIDAD es obligatorio";
    }else if($row[CANTIDAD] == 0){
        $errores[$idx][] = "El campo CANTIDAD no puede ser Cero";
    }else{
         //valida el tipo de operacion de cada registro
        $objMacHistOp = new MacInvAseHis;
        $objMacHistOp->getOperacionHist($row[ID_REGISTRO]);
        $idTemp = $objMacHistOp->array_campos;

        if (!empty($row[ID_REGISTRO]) && $idTemp[0]['OPERACION'] == 'SUMA') {
            $errores[$idx][] = "El campo CANTIDAD no se puede modificar cuando es un CARGUE";
        }
        if (($row[CANTIDAD] < 0 || $row[CANTIDAD] > 0) && !empty($row[ID_REGISTRO]) && $idTemp[0]['OPERACION'] == 'AJUSTE') {
            $errores[$idx][] = "El campo CANTIDAD no se puede modificar cuando es un AJUSTE";
        }
        if ($row[CANTIDAD] < 0 && !empty($row[ID_REGISTRO]) && $idTemp[0]['OPERACION'] == 'RESTA') {
            $errores[$idx][] = "El campo CANTIDAD no puede ser negativo cuando modificas un CONSUMO";
        }
    }

    //NOMBRE MATERIAL
    if (empty($row[NOMBRE_MATERIAL])) {
        $errores[$idx][] = "El campo NOMBRE MATERIAL es obligatorio";
    }

    // FECHA_RECIBIDO
    if (!empty($row[FECHA_RECIBIDO])) {
        $dateValidation = FormatDataUtils::validateDate($row[FECHA_RECIBIDO]);
        if ($dateValidation != 'SUCCESS') {
            if ($dateValidation == 'WRONG_DATE') {
                $errores[$idx][] = "El campo FECHA RECIBIDO no tiene un formato válido (dd/mm/yyyy)";
            }
        }
    } else {
        $errores[$idx][] = "El campo FECHA RECIBIDO es obligatorio";
    }

    $objAsesorSql = new Asesor;
    //ID ASESOR
    if (!empty($row[ID_ASESOR])) {
        $asesorKey = ArrayUtils::searchAssociative(array($row[ID_ASESOR]), $mapAsesor, array("ASESOR_ID"));
        if ($asesorKey === null) {
            $errores[$idx][] = "El Asesor no existe";
        }else{
            $objAsesorCliente  = $objAsesorSql->obtenerAsesorescliente($row[ID_ASESOR],$row[ID_CLIENTE]);
            if ($objAsesorCliente != 1) {
               $errores[$idx][] = "El Asesor no está asociado al cliente";
            }
        }
    } else {
        $errores[$idx][] = "El campo ID ASESOR es obligatorio";
    }

    if($depuracion){
        $fp=fopen($ruta_log,'a');
        fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." validacion campos carga \n");
        fwrite($fp,"\n ".date("H:i:s")."linea validada ".$idx." \n");
        fwrite($fp,"\n ".date("H:i:s")."errores al momento ".var_export($errores, true)." \n");
        fclose($fp);
    }

}

$UNID_DISP_SUM = 0;
$CANT = 0;
$MaterialParametro ;
$nombreMaterial = "";

if($depuracion){
    $fp=fopen($ruta_log,'a');
    fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." validacion campos carga terminada \n");
    fwrite($fp,"\n ".date("H:i:s")."si los errores son 0 procede guardado cantidad de errores = ".count($errores)." \n");
    fclose($fp);

}
$contador = 0; 


//Creación de objetos------------------------
$arrayInsert = array();
if (empty($errores) && empty($errores_duplic)) {

    if($depuracion){
        $fp=fopen($ruta_log,'a');
        fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." inicia guardado de campos \n");
        fclose($fp);
    }
    
    foreach ($data2Load as $key => $row) {
        $objMaterialGenId = new MacInvAse;

        $contador += 1;
        if($depuracion){
            $fp=fopen($ruta_log,'a');
            fwrite($fp,"\n ".date("H:i:s")."linea validada via contador ".$contador." \n");
            fwrite($fp,"\n ".date("H:i:s")."linea validada via index".$key." \n");
            fclose($fp);
        }

            //Se verifica si el material se encuentre en la tabla de MATERIAL GENERICO
            $objMaterialGenId->materialGen($row[ID_MATERIAL]);
            $idMateriaGen = $objMaterialGenId->array_campos;


            if (!empty($idMateriaGen[0]['MAT_GEN_ID'])) {
                //Materiales Genericos
                $MaterialParametro = $idMateriaGen[0]['MAT_GEN_ID'];
                $nombreMaterial = $idMateriaGen[0]['NOMBRE'];
            } else {
                // Materiales del LAB
                $MaterialParametro = $row[ID_MATERIAL];
                $nombreMaterial = $row[NOMBRE_MATERIAL];
            }


        $fecha_Carga = str_replace('/', '-', $row[FECHA_RECIBIDO]);
        $fecha_recibo = date("Y-m-d ", strtotime($fecha_Carga));    

        if ($row[ID_REGISTRO] == "") {
            unset($objMacInvAse);
            $ID_INV_ASE = "";
            $objMacInvAse = new MacInvAse;
            $objMacHistConsulta = new MacInvAseHis;
            
            //Consulta el historial de las unidades del inventario del material
            $objMacHistConsulta->getInventarioHist($row[ID_ASESOR],$row[ID_EVENTO],$MaterialParametro);
            $result_Inv=$objMacHistConsulta->array_campos;
    
            if(isset($result_Inv[0]) && isset($result_Inv[0]['INV_ASE_ID'])){
                $objMacInvAse->INV_ASE_ID = $result_Inv[0]['INV_ASE_ID'];
                $ID_INV_ASE = $result_Inv[0]['INV_ASE_ID'];
                $UNID_DISP_SUM = $result_Inv[0]['UNIDADES_DISPONIBLES'];
            }

            $objMacInvAse->EVENTO_ID = $row[ID_EVENTO];
            $objMacInvAse->ASESOR_ID = $row[ID_ASESOR];
            $objMacInvAse->MATERIAL_ID = $MaterialParametro;
            $objMacInvAse->MATERIAL_NOMBRE = $nombreMaterial;
            $objMacInvAse->TIPO_MATERIAL_COD = "M";
            $objMacInvAse->TIPO_MATERIAL =  "PREMIOS";
            $objMacInvAse->UNIDADES_DISPONIBLES = $row[CANTIDAD] + $UNID_DISP_SUM ;
            $objMacInvAse->FECHA_RECIBIDO = $fecha_recibo;
            $objMacInvAse->FECHA_CREA = $fecha_hora;
            $objMacInvAse->FECHA_MODIFICA = $fecha_hora;
            $objMacInvAse->USUARIO_CREA = $CEDULA;
            $objMacInvAse->USUARIO_MODIFICA = $CEDULA;
            $objMacInvAse->guardar();

            if($depuracion){
                $fp=fopen($ruta_log,'a');
                fwrite($fp,"\n ".date("H:i:s")."guardado de inventario ".$key." \n");
                if(!empty($ID_INV_ASE)){
                    fwrite($fp,"\n ".date("H:i:s")."inventario id guardado ".$ID_INV_ASE." \n");
                }else{
                    fwrite($fp,"\n ".date("H:i:s")."inventario id guardado ".$objMacInvAse->INV_ASE_ID." \n");
                }
                fclose($fp);
            }

            unset($objMacInvHistInsert);
            $objMacInvHistInsert = new MacInvAseHis;

            if(!empty($ID_INV_ASE)){
                $objMacInvHistInsert->INV_ASE_ID = $ID_INV_ASE;
            }else{
                $objMacInvHistInsert->INV_ASE_ID =$objMacInvAse->INV_ASE_ID;
            }
            
            $objMacInvHistInsert->MATERIAL_ID = $MaterialParametro;
            $objMacInvHistInsert->MATERIAL_NOMBRE = $nombreMaterial;
            if($row[CANTIDAD] < 0){
                $objMacInvHistInsert->OPERACION = "AJUSTE";
                $objMacInvHistInsert->CANTIDAD =  abs($row[CANTIDAD]);
            }else{
                $objMacInvHistInsert->OPERACION = "SUMA";
                $objMacInvHistInsert->CANTIDAD =  $row[CANTIDAD];
            }
            $objMacInvHistInsert->FECHA_RECIBIDO = $fecha_recibo;
            $objMacInvHistInsert->OBSERVACION = $row[OBSERVACION];
            $objMacInvHistInsert->FECHA_CREA = $fecha_hora;
            $objMacInvHistInsert->FECHA_MODIFICA = $fecha_hora;
            $objMacInvHistInsert->USUARIO_CREA = $CEDULA;
            $objMacInvHistInsert->USUARIO_MODIFICA = $CEDULA;
            $objMacInvHistInsert->guardar();

            if($depuracion){
                $fp=fopen($ruta_log,'a');
                fwrite($fp,"\n ".date("H:i:s")."guardado de historial inventario ".$key." \n");
                fwrite($fp,"\n ".date("H:i:s")."historia inventario id ".$objMacInvHistInsert->INV_ASE_HIST_ID." \n");
                fclose($fp);
            }
        
        }else{
            $objMacHistSql = new MacInvAseHis;
            $objMacHistSql->obtener_by_id($row[ID_REGISTRO]);
            $datos = $objMacHistSql->array_campos;

            
            if ($datos[0]['OPERACION'] == 'RESTA') {
                
                unset($objMacInvAseUpdate);
                $objMacInvAseUpdate = new MacInvAse;
                
                $objMacInvAseUpdate->INV_ASE_ID = $datos[0]['INV_ASE_ID'];
                $objMacInvAseUpdate->UNIDADES_DISPONIBLES = $inventarios_bd[$datos[0]['INV_ASE_ID']];
                $objMacInvAseUpdate->FECHA_MODIFICA = $fecha_hora;
                $objMacInvAseUpdate->USUARIO_MODIFICA = $CEDULA;
                $objMacInvAseUpdate->guardar();

                if($depuracion){
                    $fp=fopen($ruta_log,'a');
                    fwrite($fp,"\n ".date("H:i:s")."actualizando inventario resta ".$key." \n");
                    fwrite($fp,"\n ".date("H:i:s")."inventario id actualizado ".$datos[0]['INV_ASE_ID']." \n");
                    fclose($fp);
                }

                unset($objMacInvHistUpdate);
                $objMacInvHistUpdate = new MacInvAseHis;

                $objMacInvHistUpdate->INV_ASE_HIST_ID =  $datos[0]['INV_ASE_HIST_ID'];
                $objMacInvHistUpdate->CANTIDAD = $row[CANTIDAD];
                $objMacInvHistUpdate->OBSERVACION = $row[OBSERVACION];
                $objMacInvHistUpdate->FECHA_MODIFICA = $fecha_hora;
                $objMacInvHistUpdate->USUARIO_MODIFICA = $CEDULA;
                $objMacInvHistUpdate->guardar();

                if($depuracion){
                    $fp=fopen($ruta_log,'a');
                    fwrite($fp,"\n ".date("H:i:s")."guardado de historial inventario resta ".$key." \n");
                    fwrite($fp,"\n ".date("H:i:s")."historia inventario id ".$objMacInvHistUpdate->INV_ASE_HIST_ID." \n");
                    fclose($fp);
                }
            }
        
        }
    }

}

if($depuracion){
    $fp=fopen($ruta_log,'a');
    fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." carga de campos terminada \n");
    fwrite($fp,"\n ".date("H:i:s")."linea validada via contador ".$contador." \n");
    fclose($fp);
}

//Si hay errores - Se descarga archivo de error
if (count($errores) > 0 || count($errores_duplic) > 0) {
    $nombre_archivo_error_excel = "reporte_errores_detallado_" . $CEDULA . ".xls";

    $ruta_archivo = realpath(dirname(__FILE__) . '/../..') . '/archivos_error/' . $nombre_archivo_error_excel;

    $cargaMasivaUtils->InitDocErrors('Carga Masiva ', $ruta_archivo, $nombre_archivo_error_excel, $errores, $errores_duplic);

    if($depuracion){
        $fp=fopen($ruta_log,'a');
        fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." carga de campos terminada de forma erronea \n");
        fclose($fp);
    }

    $array_data['contador'] = $contador;
    $array_data['archivo_error'] = "archivos_error/" . $nombre_archivo_error_excel;
    $array_data['estado_carga_PKG'] = array(count($data2Load), count($data2Load) - (count($cargaMasivaUtils->error_fila)), count($cargaMasivaUtils->error_fila));
} else {
    //Validar errores - imprimir success
    $array_data['contador'] = $contador;

    if (count($array_data['mensaje_error']) > 0) {
        $array_data['success'] = false;

        if (count($cargaMasivaUtils->error_fila) > 0) {
            $array_data['mensaje_archivo'][] = utf8_encode('No se logro cargar el archivo, debe revisar el archivo de errores.');
        }
    } else {
        $array_data['success'] = true;

        if($depuracion){
            $fp=fopen($ruta_log,'a');
            fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." carga de campos terminada exitosamennte \n");            
            fclose($fp);
        }
    }
}

if($depuracion){
    $fp=fopen($ruta_log,'a');
    fwrite($fp,"\n ".date("H:i:s")."inventarios_cargados_".$CEDULA." carga finalizada \n");
    fclose($fp);
}
$cargaMasivaUtils->terminateLoad($array_data);
exit;

//Creación de objetos----------------------
function getValidationData($rowData, &$mapCliente, &$mapEvento, &$mapAsesor) {
    
    global $objCliente,$objEventoNutresa,$objAsesor;

    $clientes = ArrayUtils::arrayColumn($rowData, ID_CLIENTE);
    $mapClienteTemp = $objCliente->getValidationData(array(array('key' => Cliente::SEARCH_BY_PK, 'data' => $clientes)));
    $mapCliente = $mapClienteTemp[0];

    $eventos = ArrayUtils::arrayColumn($rowData, ID_EVENTO);
    $mapEventoTem = $objEventoNutresa->getValidationData(array(array('key'=>EventoNutresa::SEARCH_BY_PK, 'data'=>$eventos))); 
    $mapEvento = $mapEventoTem[0];

    $asesores = ArrayUtils::arrayColumn($rowData, ID_ASESOR);
    $mapAsesorTem = $objAsesor->getValidationData(array(array('key'=>Asesor::SEARCH_BY_PK, 'data'=>$asesores))); 
    $mapAsesor = $mapAsesorTem[0];
}
