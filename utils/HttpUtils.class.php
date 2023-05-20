<?php
namespace utils;

class HttpUtils {
    public $typeResponse;
    public $defaultErrorCode;
    public $defaultSuccessCode;
    public $defaultErrorMessage;

    public function __construct()
    {
        $this->typeResponse = "json";
        $this->defaultErrorCode = 400; //Bad request
        $this->defaultSuccessCode = 200;
        $this->defaultErrorMessage = "Petición no válida";
    }

    private function sendResponse( $message, $status = 200, $type = "json" )
    {       

        if( $type == 'json' ) {
            return print_r( json_encode( array( "success" => $status != $this->defaultErrorCode, "data" => $message ) ) );
        }

        return print_r( array( "success" => $status != $this->defaultErrorCode, "data" => $message ) );
    }

    public function sendSuccess( $message, $status = 200, $type = "json" )
    {
        return $this->sendResponse( $message, $status == 200 ? $this->defaultSuccessCode: $status, $type );
    }

    public function sendError( $message, $status = 400, $type = "json" )
    {
        return $this->sendResponse( $message == '' ? $this->defaultErrorMessage: $message, $status == 400 ? $this->defaultErrorCode: $status, $type );
    }
}