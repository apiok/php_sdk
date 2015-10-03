<?php

/**
 * OkSDK class file.
 */

class OkSDK
{
    // API URL'S.

    const TOKEN_SERVICE_ADDRESS = "http://api.odnoklassniki.ru/oauth/token.do";
    const API_REQUSET_ADDRESS   = "http://api.odnoklassniki.ru/fb.do";

    // Default config array.

    public $config = array(
        'app_id'         => '',
        'app_public_key' => '',
        'app_secret_key' => '',
        'redirect_url'   => '',
    );

    private $access_token;
    private $refresh_token;

    // Init SDK.

    public function __construct($config = array())
    {
        if (!function_exists('curl_init')){
            throw new Exception('OkSDK needs the CURL PHP extension.');
        }

        if(empty($config)){
            throw new Exception('Config is empty.');
        }
            
        $this->config = $config;
    }

    /**
     * Get application id.
     * @return string application id.
     */
    
    public function getAppId()
    {
        return $this->config['app_id'];
    }

    /**
     * Get redirect url.
     * @return string redirect URL.
     */
    
    public function getRedirectUrl()
    {
        return $this->config['redirect_url'];
    }

    /**
     * Get code;
     * @return string code;
     */

    public function getCode()
    {
        if (!isset($_GET["code"])) return false;

        return $_GET["code"];
    }

    /**
     * Change code to token.
     * @param string $code.
     * @return boolean;
    */

    public function changeCodeToToken($code)
    {
        $curl = curl_init(self::TOKEN_SERVICE_ADDRESS);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'code=' . $code . '&redirect_uri=' . $this->config['redirect_url'] . '&grant_type=authorization_code&client_id=' . $this->config['app_id'] . '&client_secret=' . $this->config['app_secret_key']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $curl_response = curl_exec($curl);
        
        curl_close($curl);
        
        $response = json_decode($curl_response, true);

        if (!empty($response['access_token'])){
            $this->access_token = $response['access_token'];
        }

        if (!empty($response['refresh_token'])){
            $this->refresh_token = $response['refresh_token'];
        }

        return !empty($response['access_token']) && !empty($response['refresh_token']);
    }

    /**
     * Update access token with refresh token.
     * @return boolean.
    */

    public function updateAccessTokenWithRefreshToken()
    {
        $curl = curl_init(self::TOKEN_SERVICE_ADDRESS);
        
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'refresh_token=' . $this->refresh_token . '&grant_type=refresh_token&client_id=' . $this->config['app_id'] . '&client_secret=' . $this->config['app_secret_key']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $curl_response = curl_exec($curl);
        
        curl_close($curl);

        $response = json_decode($curl_response, true);
        
        if (empty($response['access_token'])) return false;

        $this->access_token = $response['access_token'];

        return true;
    }

    /**
     * Make request to API.
     * @param string $method_name.
     * @param array $parameters for method.
     * @return json response.
     */
    
    public function makeRequest($method_name, $parameters = null)
    {
        // If required config values is null return.

        foreach($this->config as $config_key => $config_value) {

            if($this->config[$config_key] == '') return false;
        }
        
        $config_parameters = array(
            'application_key' => $this->config['app_public_key'],
            'method'          => $method_name,
            'sig'             => $this->calcSignature($method_name, $parameters),
            'access_token'    => $this->access_token
        );

        $request_parameters = array_merge($parameters, $config_parameters);

        $request_uri = http_build_query($request_parameters);
        
        $curl = curl_init(self::API_REQUSET_ADDRESS . "?" . $request_uri);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $curl_response = curl_exec($curl);
        
        curl_close($curl);
        
        return json_decode($curl_response, true);
    }
    
    /**
     * Make request to API.
     * @param string $method_name.
     * @param string $parameters for method.
     * @return string signarure.
     */

    private function calcSignature($method_name, $parameters = null)
    {
        // If required config values is null return.

        foreach($this->config as $config_key => $config_value) {

            if($this->config[$config_key] == '') return false;
        }

        $config_parameters = array(
            'application_key' => $this->config['app_public_key'],
            'method'          => $method_name,
        );

        $signature_parameters = array_merge($parameters, $config_parameters);

        if (!ksort($signature_parameters)){

            return false;

        } else {

            $signature = "";

            foreach($signature_parameters as $parameter_name => $parameter_value){

                $signature .= $parameter_name . "=" . $parameter_value;
            }

            $signature .= md5($this->access_token . $this->config['app_secret_key']);
            
            return md5($signature);
        }
    }
}