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
    /**
     * Convert Http header string to array
     *
     * @param string $header
     * @return array
     */
    public static function http_parse_headers(string $header){
        
        $headers = array();

        $header_text = substr($header, 0, strpos($header, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
            if ($i === 0)
                $headers['http_code'] = $line;
            else
            {
                list ($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }

        return $headers;

     }
}