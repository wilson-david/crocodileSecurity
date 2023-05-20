<?php
namespace utils;

use Exception;

class ArrayUtils {

    const ERR_SIZE = 100001;

    public static function arrayCombineColumns() {
        $args = func_get_args();
        $arrSize = -1;

        foreach ($args as $arg) {
            if (count($arg) != $arrSize && $arrSize != -1) {
                throw new Exception("Todos los arreglos deben tener el mismo tamanio", self::ERR_SIZE);
            } elseif ($arrSize == -1) {
                $arrSize = count($arg);
            }
        }

        $arrResponse = array();

        for ($i = 0; $i < $arrSize; $i++) {
            foreach ($args as $arg) {
                $arrResponse[$i][] = $arg[$i];
            }
        }

        return $arrResponse;
    }

    public static function searchAssociative($needles, $haystack, $idxs, $allOccurrences = false) {
        $occurrences = array();
        foreach ($haystack as $key => $value) {
            $trueCount = count($needles);
            if (!is_array($value)) {
                $value = (array)$value;
            }
            foreach ($needles as $key2 => $needle) {
                if ($value[$idxs[$key2]] == $needle) {
                    $trueCount--;
                }
            }
            if ($trueCount == 0) {
                if ($allOccurrences) {
                    $occurrences[] = $key;
                } else {
                    return $key;
                }
            }
        }
        return ($allOccurrences) ? $occurrences : null;
    }

    public static function arrayColumn($array, $indexColumn){
        $response = array();
        foreach ($array as $idx => $data) {
            if(isset($data[$indexColumn])){
                $response[$idx] = $data[$indexColumn];
            }else{
                $response[$idx] = "";
            }
        }
        return $response;

    }

    public static function searchValueColumnInMap($map, $nameColumn, $valueColumn){
        return $filteredItems = array_filter($map, function($elem) use($nameColumn,$valueColumn){
            return $elem[$nameColumn] == $valueColumn;
        });        
    }
}

?>