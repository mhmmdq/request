<?php

namespace Mhmmdq\Request;

use Mhmmdq\Request\Exception\Exception;

class Curl {
    /**
     * A variable to hold the curl resource
     *
     */
    protected $curl;
    /**
     * Variable for maintaining the destination url
     *
     * @var string
     */
    protected $url;
    /**
     * Variable for useragent maintenance
     *
     * @var string
     */
    protected $useragent;
    /**
     * An Array to store options curl
     *
     * @var array
     */
    protected $curl_opations;
    /**
     * An array for storing http proxy specifications
     *
     * @var array
     */
    protected $proxy;
    /**
     * The variable that determines where cookies are stored
     *
     * @var string
     */
    protected $cookie_path = './cookie/cookie.txt';
    /**
     * A variable that indicates the presence or absence of an error
     *
     * @var boolean
     */
    protected $haveError = false;
    /**
     * An array of error lists
     *
     * @var array
     */
    protected $errors;
    /**
     * The variable that determines the sending method
     *
     * @var string
     */
    protected $method;
    /**
     * An array that holds the submitted parameters
     *
     * @var array
     */
    protected $params;
    /**
     * A variable that stores sent headers
     *
     * @var array
     */
    protected $headers;


    /**
     * Set up basic tasks
     *
     * @param string|null $url
     */
    public function __construct(string $url = null)
    {
        # Check for Curl
        if (!extension_loaded('curl')) {
            throw new Exception('cURL library is not loaded');
        }

        # If the url was registered by __construct
        if(!empty($url)) {
            $this->url = $url;
        }

        # Save curl
        $this->curl = curl_init();
        # Set default useragent
        $this->useragent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36';

        return $this;

    }

    /**
     * Get an array of curl options and auto-adjust
     *
     * @param array $opations
     * 
     */
    protected function curl_opation(array $opations) {

        curl_setopt_array($this->curl , $opations);

    }

    /**
     * Add custom option to curl
     *
     * @param $opation
     * @param mixed $value
     * @return void
     */
    public function curl_set_opt($opation , $value) {
        curl_setopt($this->curl , $opation , $value);
    }

    /**
     * Get sending method
     *
     * @param string $method
     * 
     */
    public function method(string $method) {

        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Get the parameters to be sent
     *
     * @param mixed $params
     * 
     */
    public function params($params) {

        $this->params = $params;

        return $this;

    }

    /**
     * Change the default useragent
     *
     * @param string $useragent
     * 
     */
    public function useragent(string $useragent) {

        $this->useragent = $useragent;

        return $this;

    }

    /**
     * Get a proxy server as an array
     *
     * @param array $proxy
     * 
     */
    public function proxy(array $proxy) {

        $this->proxy = $proxy;

        return $this;

    }

    /**
     * Edit the default cookie storage
     *
     * @param string $path
     * @return void
     */
    public function cookie_path(string $path) {

        $this->cookie_path = $path;

    }

    /**
     * Receive and save custom headers
     *
     * @param array $headers
     * 
     */
    public function headers(array $headers) {

        $this->headers = $headers;

        return $this;

    }

    /**
     * Prepare values ​​to send requests
     *
     * @param string $url
     * @param string $method
     * @param mixed $params
     * @param array $headers
     * @param array $proxy
     * 
     */
    public function request(string $method = null , $params = null , string $url = null ,array $headers = [] , array $proxy = []) {

        if(!is_null($url)) {
            $this->url = $url;
        }

        if(!is_null($method)) {
            $this->method(strtoupper($method));
        }

        if(!empty($params)) {
            $this->params($params);
        }

        if(!empty($headers)) {
            $this->headers($headers);
        }

        if(!empty($proxy)) {
            $this->proxy($proxy);
        }

        

        return $this;

    }

    /**
     * Send data and return output
     *
     * @return string
     */
    public function send() {

        if($this->method == 'GET' && !empty($this->params) && is_array($this->params)) {
            
            $this->url .= '?' . http_build_query($this->params);
        }

        $this->curl_opation([
            CURLOPT_URL => $this->url,
            CURLOPT_USERAGENT => $this->useragent,
            CURLOPT_CUSTOMREQUEST => $this->method ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => is_array($this->params) ? http_build_query($this->params) : $this->params
        ]);
        

        $response = curl_exec($this->curl);
        
        if(curl_errno($this->curl))
            throw new Exception(curl_error($this->curl));

        return $response;
            
        

    }
    /**
     * Enable cookie usage
     *
     * @return void
     */
    public function useCookieFile() {

        $this->curl_opation([
            CURLOPT_COOKIEJAR => $this->cookie_path,
            CURLOPT_COOKIE => "cookiename=0",
            CURLOPT_COOKIEFILE => $this->cookie_path
        ]);

    }
    /**
     * Send request with post method
     *
     * @param string $url
     * @param array $params
     * 
     */
    public function post(string $url , array $params = []) {
        return  $this->request('post' , $params , $url);
    }
    /**
     * Send request with get method
     *
     * @param string $url
     * @param array $params
     * 
     */
    public function get(string $url , array $params = []) {
        return $this->request('get' , $params , $url);
    }
    /**
     * Send request with put method
     *
     * @param string $url
     * @param array $params
     * 
     */
    public function put(string $url , array $params = []) {
        return  $this->request('put' , $params , $url);
    }
    /**
     * Send request with delete method
     *
     * @param string $url
     * @param array $params
     * 
     */
    public function delete(string $url , array $params = []) {
        return  $this->request('delete' , $params , $url);
    }
    /**
     * Send request with patch method
     *
     * @param string $url
     * @param array $params
     * 
     */
    public function patch(string $url , array $params = []) {
        return $this->request('patch' , $params , $url);
    }
    /**
     * Send request with opations method
     *
     * @param string $url
     * @param array $params
     * 
     */
    public function opations(string $url , array $params = []) {
        return  $this->request('get' , $params , $url);
    }
    /**
     * Connect to a proxy server
     *
     * @param array $proxy
     * @return void
     */
    public function setProxy(array $proxy) {
        
       
       $this->curl_opation([
            CURLOPT_HTTPPROXYTUNNEL => 1,
            CURLOPT_PROXY => $proxy['ip'],
            CURLOPT_PROXYPORT => $proxy['port']
        ]);
        if(isset($proxy['userpass'])) {
            $this->curl_set_opt(CURLOPT_PROXYUSERPWD , $proxy['userpass']);
        }

    }
    /**
     * file download
     *
     * @param string $url
     * @param string $path
     * @return string
     */
    public function download(string $url ,string $path) {

        $f = fopen($path , 'w+');
        $this->curl_opation([
            CURLOPT_TIMEOUT => 50,
            CURLOPT_FILE => $f , 
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $fileContent = $this->request('get' , [] , $url)->send();
        fwrite($f , $fileContent , strlen($fileContent));
        fclose($f);

        return $path;

    }

}