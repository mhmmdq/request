<?php

namespace Mhmmdq\Request;

use Mhmmdq\Request\Exception\Exception;
use SimpleXMLElement;

abstract class Encoder {

    /**
     * Encode JSON
     * 
     * Wrap json_encode() to throw error when the value being encoded fails.
     * 
     *
     * @return string
     * @throws Exception
     */
    protected function encodeJson() {

        $args = func_get_args();
        $value = call_user_func_array('json_encode' , $args);

        if(json_last_error() !== JSON_ERROR_NONE) {

            $error_message = 'json_encode error: ' . json_last_error_msg();
            throw new Exception($error_message);

        }
        return $value;
    }

    /**
     * Encode XML
     * 
     * Convert array to XML
     * 
     * 
     * @param array $data
     * @return string
     */
    protected function encodeXml(array $data) {

        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($data , [$xml , 'addChild']);
        return $xml->asXml();
        
    }

}