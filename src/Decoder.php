<?php

namespace Mhmmdq\Request;

class Decoder {
    /**
     * Decode JSON
     *
     * @return array
     */
    public static function decodeJson() {
        $args = func_get_args();
        $response = call_user_func_array('json_decode', $args);
        if ($response === null) {
            $response = $args['0'];
        }
        return $response;
    }
    /**
     * Decode XML
     *
     * @return array
     */
    public static function decodeXml()
    {
        $args = func_get_args();
        $response = @call_user_func_array('simplexml_load_string', $args);
        if ($response === false) {
            $response = $args['0'];
        }
        return $response;
    }
    
}