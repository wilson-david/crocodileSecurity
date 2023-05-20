<?php
namespace utils;

use Exception;
use PHPExcel_IOFactory;
use PHPExcel;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;



// include_once __DIR__ . "/../clases/PHPExcel/IOFactory.php";

class CargaMasivaUtils {
    public $file;
    public $header;
    public $dirParent = "";

    const ERR_NO_FILE = 1;
    const ERR_NO_HEADER = 2;

    public function __construct() {
        $backtrace = debug_backtrace();
        $file = $backtrace[0]["file"];
        $this->dirParent = dirname($file);
    }

    public function setFile(FileUtils $file = null, $fileData = array()) {
        if ($file == null && count($fileData) == 0) {
            throw new Exception("No hay información de archivos");
        }

        if ($file != null) {
            $this->file = $file;
        } else {
            try {
                $fileTemp = new FileUtils(
                    $fileData['URL'],
                    $fileData['size'],
                    $fileData['name'],
                    null,
                    $fileData['temp'],
                    $fileData['dates']
                );

                $this->file = $fileTemp;
                try {
                    $this->getFileDataCargaMasiva();
                } catch (Exception $ex) {
                    throw $ex;
                }                
                
            } catch (Exception $e) {
                throw $e;
            }
        }

        return true;
    }

    public function setFileAllSheets(FileUtils $file = null, $fileData = array()) {
        if ($file == null && count($fileData) == 0) {
            throw new Exception("No hay información de archivos");
        }

        if ($file != null) {
            $this->file = $file;
        } else {
            try {
                $fileTemp = new FileUtils(
                    $fileData['URL'],
                    $fileData['size'],
                    $fileData['name'],
                    null,
                    $fileData['temp'],
                    $fileData['dates']
                );

                $this->file = $fileTemp;
                try {
                    $this->getFileDataCargaMasivaAllSheets();
                } catch (Exception $ex) {
                    throw $ex;
                }                
                
            } catch (Exception $e) {
                throw $e;
            }
        }

        return true;
    }

    public function validateExt($acceptedExts){
        try{
            return $this->file->validateExt($acceptedExts);
        }catch(Exception $ex){
            throw $ex;
        }
    }

    /**
     * Created By: S. Delgado
     * Date: 16/01/2020
     * Método que permite obtener la información de un archivo para carga masiva
     * ya sea en CSV o Excel y guardarla en el parámetro fileData del file proporcionado.
     *
     * @param FileUtils $file
     * @return void
     */
    public function getFileDataCargaMasiva() {
        try {
            switch ($this->file->fileExt) {
            case '.csv':
                $data = FileUtils::getCSVData($this->file->fileURL);
                break;
            case '.xls':
            case '.xlsx':
                $data = FileUtils::getExcelData($this->file->fileURL, $this->file->fileDates);
                break;
            default:
                throw new Exception("No hay información de extensión configurada", FileUtils::ERR_NO_CONF_EXT);
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        $this->file->fileData = $data;
    }


        /**
     * Created by: Andrés Obregón
     * Date: 22/07/2022
     * Método que permite obtener la información de un archivo para carga masiva
     * Tipo Excel y guardarla en el parámetro fileData del file proporcionado.
     * Esta funcion permite traer la informacion de todas las hojas del archivo
     *
     * @param FileUtils $file
     * @return void
     */
    public function getFileDataCargaMasivaAllSheets() {
        try {
            switch ($this->file->fileExt) {
            case '.xls':
            case '.xlsx':
                $data = FileUtils::getExcelDataAllSheets($this->file->fileURL, $this->file->fileDates);
                break;
            default:
                throw new Exception("No hay información de extensión configurada", FileUtils::ERR_NO_CONF_EXT);
                // break; //--- Jump statements (return, break, continue, goto) and throw expressions move control flow out of the current code block. So any unlabelled statements that come after a jump are dead code.
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        $this->file->fileData = $data;
    }

    public function setHeader($header){
        if(!is_array($header)){
            throw new Exception("El header es requerido", self::ERR_NO_HEADER);
        }

        $count = 0;
        foreach ($header as $key => $field) {
            $header[$key] = preg_replace( "/\r|\n/", "", $field );
            define(preg_replace("/\s+/", "_", preg_replace("/[\*]/","",$header[$key])), $count);
            $count++;
        }

        $this->header = $header;
    }

    public function validateHeader(){
        if(!$this->file){
            throw new Exception("No existe un archivo cargado", self::ERR_NO_FILE);
        }
        
        if(!$this->header || !isset($this->file->fileData['header'])){
            throw new Exception("No existe un encabezado", self::ERR_NO_HEADER);
        }

        return $this->file->fileData['header'] == $this->header;
    }

    public static function terminateLoad($data){
        print_r(json_encode($data));
        exit;
    }

    public function InitDocErrors($cargaName,$routeError,$errorFileName,$errorData,$errorDataDp = array()){

        $error_fila = array();
        $array_errores = array();

        foreach ($errorData as $key => $row) {
            $error_fila[] = array(
                'FILA' => $key + 1,
                'ERROR' => join(" - ", $row),
            );
        }
    
        foreach ($errorDataDp as $key0 => $row) {
            $key_error = ArrayUtils::searchAssociative(array($key0 + 1), $error_fila, array("FILA"));
            if($key_error===null){
                $error_fila[] = array(
                    'FILA' => $key0 + 1,
                    'ERROR' => join(" - ", $row),
                );
            }else{
                $error_fila[$key_error]['ERROR'] = $error_fila[$key_error]['ERROR'].' - '.join(" - ", $row);
            }
        }

        $this->error_fila = $error_fila;
        $array_datos_cargados = $this->file->fileData['body'];
        
        foreach ($error_fila as $key => $row) {
            $array_datos_cargados[$row['FILA'] - 1][count($this->header)] = $row['ERROR'];
            $array_errores[$row['FILA']] = $array_datos_cargados[$row['FILA'] - 1];
        }

        $objPHPExcel = new PHPExcel;

        $objPHPExcel->getProperties()
                ->setCreator("Ipinnovatech")
                ->setLastModifiedBy("Ipinnovatech")
                ->setTitle("Reporte Errores Detallado")
                ->setSubject("Reporte Errores Detallado")
                ->setDescription("Reporte Errores Detallado")
                ->setKeywords("Reporte Errores Detallado")
                ->setCategory("Ipinnovatech");
    
        $hoja_de_trabajo = $objPHPExcel->setActiveSheetIndex(0);
        $hoja_de_trabajo->setTitle("Reporte Errores Detallado");
    
        $hoja_de_trabajo->setCellValue("A1", $cargaName);
        $hoja_de_trabajo->mergeCells('A1:D1');   
        $hoja_de_trabajo->setCellValue("A2", '');
        $hoja_de_trabajo->mergeCells('A2:D2');
        $hoja_de_trabajo->setCellValue("A3", 'Tipo Informe:');
        $hoja_de_trabajo->setCellValue("B3", 'Registros con error');
        $hoja_de_trabajo->setCellValue("A4", 'Cantidad de elementos obtenidos:');
        $hoja_de_trabajo->setCellValue("B4", count($array_errores));
        $hoja_de_trabajo->mergeCells('B3:D3');
        $hoja_de_trabajo->mergeCells('B4:D4');
        $hoja_de_trabajo->setCellValue("A5", '');
        $hoja_de_trabajo->mergeCells('A5:D5');
    
        $stilos_header1 = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size'  => 26,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '333399')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $stilo_just_border = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true
            )
        );
    
        $hoja_de_trabajo
            ->getStyle('A1:D1')
            ->applyFromArray($stilos_header1);
        $hoja_de_trabajo
            ->getStyle('A3:D4')
            ->applyFromArray($stilo_just_border);

        $hoja_de_trabajo->setCellValue("A6", 'FILA');
        $Excel_letter = 1;
        $columnLetter = 'B';
        foreach ($this->header as $row) {
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($Excel_letter);
            $Excel_letter++;
    
            $hoja_de_trabajo->setCellValue($columnLetter."6", utf8_decode($row));
            $hoja_de_trabajo->getColumnDimension($columnLetter)->setAutoSize(true);
        }
        $Error_letter =PHPExcel_Cell::stringFromColumnIndex($Excel_letter);
        $hoja_de_trabajo->setCellValue($Error_letter."6", 'ERROR');
        $hoja_de_trabajo->getColumnDimension($Error_letter)->setWidth(40);
    
        $stilos_header2 = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '333399')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );

        $hoja_de_trabajo
            ->getStyle('A6:'.$Error_letter.'6')
            ->applyFromArray($stilos_header2);
    
        ksort($array_errores);
        $Excel_letter = 0;
        $columnLetter = 'A';
        $row_num = 7;
    
        foreach ($array_errores as $key => $row) {
            $Excel_letter = 0;
            $hoja_de_trabajo->setCellValue('A'.$row_num, $key);
            $hoja_de_trabajo->getColumnDimension($columnLetter)->setAutoSize(true);  
            $Excel_letter++;
            foreach ($row as $key2 => $data) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($Excel_letter);
                    $Excel_letter++;
                    $hoja_de_trabajo->setCellValueExplicit($columnLetter.$row_num, utf8_decode(utf8_encode($data)),PHPExcel_Cell_DataType::TYPE_STRING);   
                    if(sizeof($row)-1 != $key2){
                        $hoja_de_trabajo->getColumnDimension($columnLetter)->setAutoSize(true);
                    }        
            } 
            $row_num++;
        }

        $hoja_de_trabajo
            ->getStyle('A7:'.$columnLetter.($row_num-1))
            ->applyFromArray($stilo_just_border);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $errorFileName . '"');
        header('Cache-Control: max-age=0');
    
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //for XLS
        $objWriter->save($routeError);
    }

    public function validateDuplicateRecords( $fields ){
        if(!$this->file){
            throw new Exception("No existe un archivo cargado", self::ERR_NO_FILE);
        }

        if( !isset( $this->file->fileData['body'] ) ){
            throw new Exception("No existen datos", self::ERR_NO_HEADER);
        }
        
        $data = array();
        $errors = array();
        foreach( $this->file->fileData['body'] as $idx => $record ){
            $unique = "";
            $dataRecord = array();
            foreach( $fields as $field ){
                $dataRecord[] = $record[$field];
            }
            
            $unique = implode("-", $dataRecord);

            if( in_array($unique, array_keys($data)) ){
                $errors[$idx][] = "El registro esta duplicado dentro del archivo.";
            }else{
                $data[$unique] = $record;
            }
        }

        return $errors;
    }

}

?>