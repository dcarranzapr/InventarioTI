<?php

class ProxyPeopleSoft
{
    //Credentials
    private $location;
    private $login;
    private $password;
    private $action;
    //Variables for use in process
    private $credentials;
    private $prefix;
    private $nonce;
    private $timestamp;
    private $headers;
    private $xml_data;
    private $curl;
    private $url;

    public function __construct() {
        $wsPeopleSoft = Yii::app()->params['webServicePeopleSoft'];
        $wsCountry = Yii::app()->params['defaultCountry'];
        $wsPeopleSoftCountry = $wsPeopleSoft[$wsCountry];

        $this->action = 'AltaColaboradores.VERSION_1';
        $this->location = 'http://xmlns.oracle.com/Enterprise/Tools/schemas/PH_ALTA_COLAB_REQ.VERSION_1';
        $this->url = $wsPeopleSoftCountry['url'];
        $this->login = $wsPeopleSoftCountry['user'];
        $this->password = $wsPeopleSoftCountry['password'];
    }

    private function generateHeader() {
        $this->credentials = base64_encode($this->login.":".$this->password);
        $this->prefix = gethostname();
        $this->nonce = base64_encode( substr( md5( uniqid( $this->prefix.'_', true)), 0, 16));
        $this->timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $this->headers = array( 
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept-Encoding: gzip,deflate",
            "SOAPAction: \"".$this->action."\"",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        );
    }
    
    public function sendRequest($data) {
        try {
            $this->generateHeader();
            $xml_data = $this->generateXmlConsult($data);
            
            $curlOptions = array();
            $curlOptions[CURLOPT_SSL_VERIFYPEER] =  1;
            $curlOptions[CURLOPT_URL] = $this->url;
            $curlOptions[CURLOPT_RETURNTRANSFER] = true;
            $curlOptions[CURLOPT_TIMEOUT] = 30;
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
            $curlOptions[CURLOPT_POSTFIELDS] = $xml_data;
            $curlOptions[CURLOPT_HTTPHEADER] = $this->headers;
                        
            $curlService = curl_init();
            curl_setopt_array($curlService, $curlOptions);
            $output = curl_exec($curlService);
            
            $string = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $output);
            $string = simplexml_load_string($string);
            $string = json_encode($string);
            $json = json_decode($string, true);

            curl_close($curlService); 
            
            return $json;
        } catch(\Exception $e){
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);   
        }
    }
    
    public function getDataConsult($json) {
        $msgData = $json['soapenvBody']['PH_ALTA_COLAB_RESP']['MsgData'];
        
        if (isset($msgData["Transaction"])) {
            if (array_key_exists("PH_COLAB_ACT_TB", $msgData["Transaction"])) {
                return $msgData["Transaction"]["PH_COLAB_ACT_TB"];
            } else {
                return $msgData["Transaction"][0]["PH_COLAB_ACT_TB"];
            }
        }

        return null;
    }

    public function generateXmlConsult($data) {
        $xml = '<soapenv:Envelope xmlns:ph="'.$this->location.'" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                    <soapenv:Header>
                        <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                            <wsse:UsernameToken>
                                <wsse:Username>'.$this->login.'</wsse:Username>
                                <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->password.'</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>
                    </soapenv:Header>
                    <soapenv:Body>
                    <ph:PH_ALTA_COLAB_REQ>
                        <ph:MsgData>
                            <ph:Transaction>
                                <ph:PH_COLAB_SAP_TB class="R">
                                    <ph:EMPLID IsChanged="?">'.$data.'</ph:EMPLID>
                                </ph:PH_COLAB_SAP_TB>                            
                            </ph:Transaction>
                        </ph:MsgData>
                    </ph:PH_ALTA_COLAB_REQ>
                    </soapenv:Body>
                </soapenv:Envelope>';
        return $xml;
    }    
}