<?php 

namespace Mhmmdq\Request;

use Mhmmdq\Request\Exception\Exception;
use Mhmmdq\Request\Curl;
use Mhmmdq\Request\Encoder;
use Mhmmdq\Request\Decoder;

class Request {
    /**
     * This variable is sent to save the location
     *
     * @var string
     */
    protected $curlUrl;
    /**
     * This variable is sent to maintain the method
     *
     * @var string
     */
    protected $curlMethod;
    /**
     * This array holds the data needed to send
     *
     * @var array
     */
    protected $curlParams;
    /**
     * This variable is for maintaining the user's IP
     *
     * @var string
     */
    protected $ip;
    /**
     * This variable is for maintaining the current uri
     *
     * @var string
     */
    protected $currentUrl;
    /**
     * This variable displays the full uri
     *
     * @var string
     */
    protected $fullUrl;
    /**
     * This variable holds the header received from CURL
     *
     * @var string
     */
    protected $httpـheadersـreceived;
    /**
     * This variable stores the current method
     *
     * @var string
     */
    protected $currentMethod;
    /**
     * A function for the initial settings that is optional
     *
     * @param string $method
     * @param string $url
     * @param array $params
     */
    public function __construct(string $method = "" , string $url = "" , array $params = []) {

        $this->curlMethod = $method;
        $this->curlUrl = $url;
        $this->curlParams = $params;

        $this->currentMethod = strtolower($_SERVER['REQUEST_METHOD']); 

        $this->currentUri = rawurldecode(strtok($_SERVER['REQUEST_URI'] , '?'));

        $this->ip();

    }
    /**
     * A function to send a request with a curl to the destination uri
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param string $type
     *
     */
    public function request(string $method = "" , string $url = "" , array $params = [] , $type = '') {

        $curl = new Curl();
        if(!empty($method)) {
            $this->curlMethod = $method;
        }

        if(!empty($url)) {
            $this->curlUrl = $url;
        }

        if(!empty($params)) {
            $this->curlParams = $params;
        }

        $this->reuqestType($type);
        

        return $this;

    }
    /**
     * Function to set the parameters sent by curl
     *
     * @param array $params
     * @param string $type
     * 
     */
    public function params(array $params , string $type = '') {

        $this->curlParams = $params;
        $this->reuqestType($type);

        return $this;

    }
    /**
     * Send request and receive output with curl
     *
     * @param string $type
     * @return string
     */
    public function send($type = '') {

        $curl = new Curl($this->curlUrl);
        $response =  $curl->request($this->curlMethod , $this->curlParams , $this->curlUrl)->send();

        if($type == 'json') {
            return Decoder::decodeJson($response);
        }

        if($type == 'xml'){
            return Decoder::decodeXml($response);
        }

        $this->httpـheadersـreceived = $curl->getHttpHeaders();

        return $response;

    }
    /**
     * Set the data type of the submitted data
     *
     * @param string $type
     * @return void
     */
    protected function reuqestType(string $type) {

        switch ($type) {
            case 'json':
                $this->curlParams = Encoder::encodeJson($this->curlParams);
                break;
            case 'xml':
                $this->curlParams = Encoder::encodeXml($this->curlParams);
                break;
        }

    }
    /**
     * Receive inputs named individually
     *
     * @param string $key
     * @return mixed
     */
    public function input(string $key) {
        return $this->all()[$key];
    }
    /**
     * Get all inputs
     *
     * @return array
     */
    public function all() {
       return parse_str(file_get_contents('php://input'));
    }
    /**
     * Get IP client
     *
     * @return string
     */
    public function getIp() {
        return $this->ip();
    }
    /**
     * Move the user to a new page
     *
     * @param string $url
     * @param integer $statusCode
     * @return void
     */
    public function redirect(string $url , int $statusCode = 303) {
        header('location: ' . $url , true , $statusCode);
        die();
    }
    /**
     * Receive incoming headers from the curl as a array
     *
     * @return array
     */
    public function headers() {
        return $this->httpـheadersـreceived;
    }
    /**
     * get user browser useragent
     *
     * @return string
     */
    public function useragent() {
        return $_SERVER['useragent'];
    }
    /**
     * Find the client ip
     *
     * @return void
     */
    protected function ip() {
        $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
        foreach($keys as $k)
        {
            if (!empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP))
            {
                $this->ip = $_SERVER[$k];
            }
        }
    }
    /**
     * Set a new header
     *
     * @param array $headers
     * @return void
     */
    public function set_header(array $headers) {
        foreach($headers as $header) {
            header($header);
        }
    }
    /**
     * Set the output type for the browser
     *
     * @param string $type
     * @return void
     */
    public function content_type(string $type) {
        $this->set_header(["Content-Type: {$type}"]);
    }
    /**
     * Set the json output type for the browser
     *
     * @return void
     */
    public function content_type_json() {
        $this->content_type('Content-Type: application/json; charset=utf-8');
    }
    /**
     * Set header 404 for browser
     *
     * @return void
     */
    public function set_404() {
        $this->set_header(['HTTP/1.1 404 Not Found']);
    }
    /**
     * Get the current uri
     *
     * @return string
     */
    public function currentUri() {
        return $this->currentUri;
    }
    /**
     * Check for ssl
     *
     * @return boolean
     */
    public function is_https() {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
    }
    /**
     * Get the full uri
     *
     * @return string
     */
    public function fullUri() {
        return $this->is_https() ? 'https' : 'http' . "//$_SERVER[HTTP_HOST]" . $this->currentUri(); 
    }
    /**
     * Get the current method
     *
     * @return void
     */
    public function method() {
        return $this->currentMethod;
    }
    /**
     * Check if the method you want is equal to the current method or
     *
     * @param string $method
     * @param string $callback
     * @return boolean
     */
    public function is_method(string $method , $callback = '') {
        $result = $method == $this->method();

        if(is_callable($callback)) {
            if(!$result) {
                call_user_func($callback);
            }
        }else {
            return $result;
        }
    }

}