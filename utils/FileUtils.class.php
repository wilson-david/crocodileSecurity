<?php
namespace utils;

use DateTime;
use Exception;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Shared_Date;

include_once __DIR__ . "/../clases/PHPExcel/Reader/Excel2007.php";

/**
 * Created by: S. Delgado
 * Date: 14/01/2020
 *
 * Clase con funciones útiles para la validación de archivos
 */
class FileUtils {
    public $fileName;
    public $fileExt;
    public $fileData;
    public $fileURL;
    public $fileSize;
    public $fileDates;
    public $logger;

    const EXT_SUCCESS = 1;
    const EXT_UNKNOWN = 2;
    const EXT_NOT_FOUND = 3;

    const ERR_NO_URL = 1;
    const ERR_LOADING = 2;
    const ERR_NO_CONF_EXT = 3;
    const ERR_DELIMITERS = 4;
    const ERR_UNKNOWN_FILE_TYPE = 5;

    const FORMAT_DATE = "Y-m-d H:i:s";

    public function __construct($fileURL, $fileSize = "", $fileName = "", $fileExt = "", $fileTemp = "", $fileDates = array(), $logger = null) {
        if (!$fileURL || $fileURL == "") {
            throw new Exception("No hay información de URL", self::ERR_NO_URL);
        }

        if (isset($fileTemp) && $fileTemp != "") {
            $this->fileExt = ($fileExt === "" || !$fileExt) ? self::getFileExt($fileName, $logger) : $fileExt;
            $fileURL = self::__addExt($fileURL, $this->fileExt);

            if (!move_uploaded_file($fileTemp, $fileURL)) {
                throw new Exception("No es posible cargar el archivo", self::ERR_LOADING);
            }

            $this->fileName = ($fileName === "" || !$fileName) ? self::getFileName($fileURL, $logger) : $fileName;
            $this->fileName = self::__addExt($fileName, $this->fileExt);

            $this->fileURL = $fileURL;
        } else {
            $this->fileURL = $fileURL;

            $this->fileName = ($fileName === "" || !$fileName) ? self::getFileName($fileURL, $logger) : $fileName;
            $this->fileExt = ($fileExt === "" || !$fileExt) ? self::getFileExt($this->fileName, $logger) : $fileExt;
        }
        $this->fileSize = $fileSize;
        $this->fileDates = $fileDates;
    }

    /**
     * Created by: S. Delgado
     * Date: 16/01/2020
     * Método que permite obtener la información de un archivo CSV
     * teniendo en cuenta el delimitador especificado.
     * Retorna un arreglo donde el primer valor es el encabezado (header)
     * y el segundo contiene la información del resto de filas (body)
     * 
     * @param string $url
     * @param string $delimiter
     * @return array
     */
    public static function getCSVData($url, $delimiter = ",") {
        
        $curDelimiter = self::__detectDelimiter(fopen($url, 'r'));

        if ($curDelimiter != $delimiter) {
            throw new Exception("El archivo no tiene los delmitadores correctos ($delimiter)", self::ERR_DELIMITERS);
        }

        $header = array();
        $body = array();

        if (($handle = fopen($url, "r")) !== FALSE) {
            $count = 0;
            while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                if($data != null && !empty($data)){
                    if($count == 0){
                        $header = array_map("utf8_encode", $data);
                        $count++;
                    }else{
                        $body[] = array_map("utf8_encode", $data);
                    }
                }
            }
            fclose($handle);
        }

        // $file = array_map("utf8_encode", file($url));

        // foreach ($file as $key => $row) {
        //     if (empty($row)) {
        //         unset($file[$key]);
        //     }
        // }

        // $body = array_map("self::__reverseExplode", $file, array_fill(0, count($file), ','));
        // //print_r($file);
        // $header = array_shift($body);
        
        
        foreach ($header as $key => $field) {
            $header[$key] = preg_replace( "/\r|\n/", "", $field );
        }
        foreach ($body as $key => $field) {
            $body[$key] = preg_replace( "/\r|\n/", "", $field );
        }
        $body = array_map(function (array $arr) {
            $arr = array_map("trim", $arr);
            return $arr;
        }, $body);

        $response = array(
            "header" => $header,
            "body" => $body,
        );

        return $response;
    }

    private static function __reverseExplode($value, $delimiter) {
        return explode($delimiter, $value);
    }

    /**
     * Created by: S. Delgado
     * Date: 16/01/2020
     * Método para obtener la información de un archivo excel y convertirla en un array
     * donde header es el encabezado del archivo y body son las filas con la información
     *
     * @param string $url
     * @param array $dateData
     * @return array
     */
    public static function getExcelData($url, $dateData) {

        $header = array();
        $body = array();

        $reader = (strpos($url, ".xlsx")) ? PHPExcel_IOFactory::createReader('Excel2007') : PHPExcel_IOFactory::createReader('Excel5');
        if ($reader instanceof PHPExcel_Reader_Excel2007 || $reader instanceof PHPExcel_Reader_Excel5) {
            $reader->setReadDataOnly(true);
        } else {
            throw new Exception("Formato de archivo desconocido", self::ERR_UNKNOWN_FILE_TYPE);
        }

        $excelData = $reader->load($url);
        $activeWorksheet = $excelData->getActiveSheet();

        // print_r(var_dump($activeWorksheet->getRowIterator()));
        
        $cuenta = 0;
        // print_r(in_array(16,$dateData));
        // print_r($dateData);
        foreach ($activeWorksheet->getRowIterator() as $row) {
            $array_temp = array();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cellCount = 0;
            foreach ($cellIterator as $cell) {
                if (in_array($cellCount,$dateData['idx']) && $cuenta > 0) {
                    if ( $cell->getValue() != "" ){
                        $format = $dateData['fmt'][$cellCount];  
                        $value = $cell->getValue();
                        //Se valida si el dato llega en formato timestamp para convertirlo
                        if(is_numeric($value)){
                            $value = date($format, strtotime(date(self::FORMAT_DATE, PHPExcel_Shared_Date::ExcelToPHP($cell->getValue())) . ' +7 hours'));
                        }
                        $array_temp[] = $value;
                    }else{
                        $array_temp[] = "";
                    }
                } else {
                    $array_temp[] = $cell->getValue();
                }
                $cellCount++;
            }

            if (isset($array_temp) && $cuenta > 0) {
                $body[] = $array_temp;
            }

            if (isset($array_temp) && $cuenta == 0) {
                $header = $array_temp;
            }

            $cuenta++;
        }

        foreach ($header as $key => $field) {
            $header[$key] = preg_replace( "/\r|\n/", "", $field );
        }
        foreach ($body as $key => $field) {
            $body[$key] = preg_replace( "/\r|\n/", "", $field );
        }
        $body = array_map(function (array $arr) {
            $arr = array_map("trim", $arr);
            return $arr;
        }, $body);

        $response = array(
            "header" => $header,
            "body" => $body,
        );

        return $response;
    }

    /**
     * Created by: Andrés Obregón
     * Date: 22/07/2022
     * Método para obtener la información de todas las hojas de un archivo excel y convertirla en un array
     * donde los headers son los encabezado del archivo y bodis son las filas con la información de cada hoja
     *
     * @param string $url
     * @param array $dateData
     * @return array
     */
    public static function getExcelDataAllSheets($url, $dateData) {

        $headers = array();
        $bodis = array();

        $reader = (strpos($url, ".xlsx")) ? PHPExcel_IOFactory::createReader('Excel2007') : PHPExcel_IOFactory::createReader('Excel5');
        if ($reader instanceof PHPExcel_Reader_Excel2007 || $reader instanceof PHPExcel_Reader_Excel5) {
            $reader->setReadDataOnly(true);
        } else {
            throw new Exception("Formato de archivo desconocido", self::ERR_UNKNOWN_FILE_TYPE);
        }

        $excelData = $reader->load($url);
        $activeWorksheet = $excelData->getAllSheets();

        $sheets = [];
        foreach ($activeWorksheet as $sheet) { 
            $sheets[$sheet->getTitle()] = $sheet;
            $headers[$sheet->getTitle()] = array();
            $bodis[$sheet->getTitle()] = array();
        }

        foreach ($sheets as $key => $sheet) {
            //print_r($key);    

            
            $cuenta = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $array_temp = array();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $cellCount = 0;
                foreach ($cellIterator as $cell) {
                    if (in_array($cellCount,$dateData['idx']) && $cuenta > 0) {
                        if ( $cell->getValue() != "" ){
                            $format = $dateData['fmt'][$cellCount];  
                            $value = $cell->getValue();
                            //Se valida si el dato llega en formato timestamp para convertirlo
                            if(is_numeric($value)){
                                $value = date($format, strtotime(date(self::FORMAT_DATE, PHPExcel_Shared_Date::ExcelToPHP($cell->getValue())) . ' +7 hours'));
                            }
                            $array_temp[] = $value;
                        }else{
                            $array_temp[] = "";
                        }
                    } else {
                        $array_temp[] = $cell->getValue();
                    }
                    $cellCount++;
                }

                //print_r($array_temp);

                if (isset($array_temp) && $cuenta > 0) {
                    $bodis[$key][] = $array_temp;
                }

                if (isset($array_temp) && $cuenta == 0) {
                    $headers[$key] = $array_temp;
                }
                $cuenta++;
            }
            
        }

        foreach ($headers as $key => $header) {
            foreach ($header as $key2 => $field) {
                $headers[$key][$key2] = preg_replace( "/\r|\n/", "", $field );
            }
        }

        foreach ($bodis as $key => $body) {
            foreach ($body as $key2 => $field) {
                $body[$key][$key2] = preg_replace( "/\r|\n/", "", $field );
            }
        }

        foreach ($bodis as $key => $body) {
            $body = array_map(function (array $arr) {
                $arr = array_map("trim", $arr);
                return $arr;
            }, $body);

            $bodis[$key] = $body;
        }

        //print_r($bodis);

        $response = array(
            "headers" => $headers,
            "bodis" => $bodis,
        );

        return $response;
    }    

    /**
     * Created by: Stack Overflow
     * Date 14/01/2020
     * https://stackoverflow.com/a/37557537
     * FIXME: Este método debería tener en cuenta el tamaño del encabezado
     *
     * @param file_handler $fh
     * @return void
     */
    private static function __detectDelimiter($fh) {
        $delimiters = array("\t", ";", "|", ",");
        $data_1 = null;
        $data_2 = null;
        $delimiter = $delimiters[0];
        foreach ($delimiters as $d) {
            $data_1 = fgetcsv($fh, 4096, $d);
            if (sizeof($data_1) > sizeof($data_2)) {
                $delimiter = $d;
                $data_2 = $data_1;
            }
            rewind($fh);
        }

        return $delimiter;
    }

    /**
     * Created by: S. Delgado
     * Date: 14/01/2020
     * Método que agrega la extensión al nombre de un archivo validando que
     * este no cuente ya con esta extensión.
     *
     * @param string $name
     * @param string $ext
     * @return string $fileName
     */
    private static function __addExt($name, $ext) {
        if (self::getFileExt($name) == $ext) {
            return $name;
        } else {
            return $name . $ext;
        }
    }

    /**
     * Created by: S. Delgado
     * Date: 14/01/2020
     * Método que valida que la extensión del archivo cargado
     * esté dentro del rango de la(s) extension(es) especificada(s)
     *
     * @param string|array $exts
     * @return int
     */
    public function validateExt($exts) {
        if (!$this->fileExt || $this->fileExt == "") {
            return self::EXT_UNKNOWN;
        }

        if (is_array($exts)) {
            foreach ($exts as $ext) {
                if ($ext === $this->fileExt) {
                    return self::EXT_SUCCESS;
                }
            }
        } else {
            if ($exts === $this->fileExt) {
                return self::EXT_SUCCESS;
            }
        }

        return self::EXT_NOT_FOUND;
    }

    /**
     * Created by: S. Delgado
     * Date: 14/01/2020
     *
     * @param [type] $url
     * @param string $logger
     * @return void
     */
    public static function getFileName($url, $logger = 'FileUtils::__defaultLogger') {
        $fileName = "";
        try {
            $fileName = substr($url, strpos($url, '/'));
        } catch (Exception $e) {
            $logger($e, true);
            return false;
        }
        return $fileName;
    }

    /**
     * Created by: S. Delgado
     * Date: 14/01/2020
     * Método que obtiene la extensión del nombre de un archivo
     * TODO: Validar si es realmente el path de un archivo.
     *
     * @param string $name
     * @param string $logger
     * @return string $fileExt
     */
    public static function getFileExt($name, $logger = 'FileUtils::__defaultLogger') {
        $fileExt = "";
        try {
            $fileExt = substr($name, strpos($name, '.'));
        } catch (Exception $e) {
            $logger($e, true);
            return false;
        }
        return $fileExt;
    }

    /**
     * Created by: S. Delgado
     * Date: 14/01/2020
     * Método que permite mostrar información acerca de un error y su traza
     *
     * @param string $message
     * @return void
     */
    private static function __defaultLogger($message, $showBacktrace = false) {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        // print "Inicio 1: " . $d->format("Y-m-d H:i:s.u");
        // echo "\n";

        echo "<pre>";
        echo sprintf("%s: %s", $d->format('Y-m-d H:i:s.u'), $message);
        if ($showBacktrace) {
            echo "\n Backtrace: \n";
            print_r(debug_backtrace());
        }
        echo "</pre>";
    }
}
?>