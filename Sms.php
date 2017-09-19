<?php

namespace c006\sms;

use Yii;

/**
 * Class Sms
 * @package c006\sms
 */
class Sms
{
    /**
     * @var string
     */
    private $log = 'send-text.log';
    /**
     * @var string
     */
    private $lookup_url = 'https://carrierlookup.xminder.com/LCR-API/api.php';
    /**
     * @var string
     */
    private $lookup_key = 'ADD KEY';
    /**
     * @var string
     */
    private $phone = '';
    /**
     * @var bool
     */
    private $debug = FALSE;

    /**
     * @param bool $debug
     */
    function __construct($debug = FALSE)
    {
        $this->debug = $debug;
        $this->log = Yii::getAlias('@frontend') . '/runtime/' . $this->log;
    }

    /**
     * @param $phone_number
     * @param $subject
     * @param $message
     *
     * @return bool
     */
    public function send($phone_number ,$subject, $message)
    {

        $url = $this->lookup_url . '?uuid=' . $this->lookup_key . '&p1=' . $phone_number;

        $xml = simplexml_load_file($url) or error_log(date('[Y-m-d H:i e] ') . "Can't connect to xminder.com" . PHP_EOL, 3, $this->log);

        if (is_object($xml)) {

            if ($this->debug) {
                error_log(PHP_EOL . print_r($xml, TRUE) . PHP_EOL, 3, $this->log);
                print "Status           :" . $xml->results->result->status . "<br>";
                print "---------------------------------------------------<br>";
                print "Phone number     :" . $xml->results->result->number . "<br>";
                print "Wless            :" . $xml->results->result->wless . "<br>";
                print "Carrier          :" . $xml->results->result->carrier_name . "<br>";
                print "SMS addr         :" . $xml->results->result->sms_address . "<br>";
                print "MMS addr         :" . $xml->results->result->mms_address . "<br>";
            } else {

                return mail($xml->results->result->sms_address, $subject, $message);
            }
        } else {
            die(print_r($xml));
        }

        return FALSE;

    }
}