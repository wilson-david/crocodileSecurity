<?php
/**
 * Created by: Rene Arteaga
 * Date: 2021-01-04
 * Content: This class is used for encoding and decoding jwt tokens
 */
namespace utils;
use Exception;

ini_set('display_errors', true );

class JwtUtils {
    /**
     * Created by: Rene Arteaga
     * Date: 2020-06-23
     * Content: Return JWT token
     * @param string $method encrition method, default sha256
     * @param array $datos
     * @param string $secret
     * @return string $hash
     */
    public static function get_jwt_token( $method = "sha256", $datos = array(), $secret = "" ){
        $header = array(
            "alg" => $method,
            "typ" => "jwt"
        );
        $header = json_encode( $header );
        $payload = json_encode( $datos );
        $token = array(
            str_replace("==", "", base64_encode( $header )),
            str_replace("==", "", base64_encode( $payload )),
            str_replace("==", "", base64_encode( hash_hmac($method, $payload, $secret ) ) )
        );

        return implode(".", $token );
    }

    /**
     * Created by: Rene Arteaga
     * Date: 2020-06-23
     * Content: Return data decode from a JWT token
     * @param string $token
     * @return array $array
     */
    public static function decode_jwt_token( $token = "" ){
        if( $token == "" ) return $token;

        try{
            $arrayTokens = explode( ".", $token );
            if( count($arrayTokens) !== 3  ){//The token isn't jwt
                throw new Exception("El token no tiene el estandar jwt");
            }

            $payload = json_decode( base64_decode( $arrayTokens[1] ), true ); //The payload is always at position 1        

            return $payload;
        }catch( \Exception $e){
            echo $e->getMessage();
            return;
        }
    }

    /**
     * Created by: Rene Arteaga
     * Date: 2020-06-23
     * Content: validate JWT token
     * @param string $token
     * @param string $secret
     * @return boolean
     */
    public static function validate_token_jwt( $token = "", $secret = "" ){
        if( $token == "" ) return $token;

        try{
            $arrayTokens = explode( ".", $token );
            if( count($arrayTokens) !== 3  ){//The token isn't jwt
                throw new Exception("El token no tiene el estandar jwt");
            }

            $header = json_decode( base64_decode( $arrayTokens[0] ), true ); //The header is always at position 0
            $payload = json_decode( base64_decode( $arrayTokens[1] ), true ); //The payload is always at position 1
            $signature = base64_decode( $arrayTokens[2] ); //The signature is always at position 2

            if( !isset($header['alg']) ){
                throw new Exception("La cabecera del token no es válida");
            }
            
            $newToken = self::get_jwt_token( $header['alg'], $payload, $secret );
            $arrayNewToken = explode(".", $newToken );

            return hash_equals( $signature, base64_decode( $arrayNewToken[2] ) );            
            
        }catch( \Exception $e){
            echo $e->getMessage();
            return;
        }
    }
}

function hash_equals($str1, $str2)
{
    if(strlen($str1) != strlen($str2))
    {
        return false;
    }
    else
    {
        $res = $str1 ^ $str2;
        $ret = 0;
        for($i = strlen($res) - 1; $i >= 0; $i--)
        {
            $ret |= ord($res[$i]);
        }
        return !$ret;
    }
}