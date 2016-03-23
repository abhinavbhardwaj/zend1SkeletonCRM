<?php

/**
 * @author Ashwini Agarwal <ashwini.agarwal@a3logics.in>
 */
require_once APPLICATION_PATH . '/../library/sms/Services/Twilio.php';

class My_Sms {

    protected $_accountSid = 'AC89a48ba1b8362c0aa5673b3e9d87268b';
    protected $_authToken = 'c77e41f2170824f79929d226567ee37c';
    protected $_authDate = '2010-04-01';
    protected $_twilioPhone = '+1 408-675-5246';
    protected $_client;

    public function __construct() {
        $http = new Services_Twilio_TinyHttp('https://api.twilio.com', array(
            'curlopts' => array(CURLOPT_SSL_VERIFYPEER => false))
        );
        $this->_client = new Services_Twilio($this->_accountSid, $this->_authToken, $this->_authDate, $http);
    }

    public function sendSms($message, $number) {
        try {
            return $this->_client->account->messages->sendMessage($this->_twilioPhone, $number, $message);
        } catch (Exception $e) {
            throw $e;
        }
    }

}
