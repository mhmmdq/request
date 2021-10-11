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
     * @return void
     */
    protected function curl_opation(array $opations) {

        # Enter each member of the array as the key and the value of the option
        foreach($opations as $opation => $value)
            curl_setopt($this->curl , $opation , $value);

    }

    /**
     * Get sending method
     *
     * @param string $method
     * @return void
     */
    public function method(string $method) {

        $this->method = $method;

        return $this;
    }

    /**
     * Get the parameters to be sent
     *
     * @param array $params
     * @return void
     */
    public function params(array $params) {

        $this->params = $params;

        return $this;

    }

    /**
     * Change the default useragent
     *
     * @param string $useragent
     * @return void
     */
    public function useragent(string $useragent) {

        $this->useragent = $useragent;

        return $this;

    }

    /**
     * Get a proxy server as an array
     *
     * @param array $proxy
     * @return void
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

    public function headers(array $headers) {

        $this->headers = $headers;

        return $this;

    }

    



}