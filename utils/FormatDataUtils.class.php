<?php
namespace utils;

use Exception;
use DateTime;

class FormatDataUtils {
    const SUCCESS = 1;
    const WRONG_DATE = 2;
    const DATE_TOO_OLD = 3;

    /**
     * Created by: Gustavo Silva
     * Date: 25/01/2020
     * Método que permite la validacion de formato y longevidad de fecha
     * 
     * @param date $date
     * @param string $format
     * @return string
     */
    public static function validateDate($date,$format = "d/m/Y"){
        $formatedDate = DateTime::createFromFormat($format, $date);
        $today = DateTime::createFromFormat($format, date($format, strtotime("today")));
        if ($formatedDate) {
            if ($formatedDate < $today) {
                return 'DATE_TOO_OLD';
            } else {
                return 'SUCCESS';
            }
        } else {
            return 'WRONG_DATE';
        }
    }

    /**
     * Created by: S. Delgado
     * Date: 01/07/2020
     * Método para validar el texto ingresado sea un correo electrónico
     * 
     * @param string $email
     * @return boolean
     */
    public static function validateEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

}