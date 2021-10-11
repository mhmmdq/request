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
     * @return self
     */
    public function request(string $url = null , string $method = null , $params = null , array $headers = [] , array $proxy = []) {

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

        if($this->method == 'GET') {
            $this->url .= '?' . http_build_query($this->params);
        }

        $this->curl_opation([
            CURLOPT_URL => $this->url,
            CURLOPT_USERAGENT => $this->useragent,
            CURLOPT_CUSTOMREQUEST => $this->method ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($this->params)
        ]);
        

        $responve = curl_exec($this->curl);
        $error = curl_error($this->curl);
        if(!$error)
            return $responve;
        

    }

}