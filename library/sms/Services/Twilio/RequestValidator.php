
<!-- saved from url=(0084)https://raw.github.com/twilio/twilio-php/master/Services/Twilio/RequestValidator.php -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">&lt;?php

class Services_Twilio_RequestValidator
{

    protected $AuthToken;

    function __construct($token)
    {
        $this-&gt;AuthToken = $token;
    }
    
    public function computeSignature($url, $data = array())
    {
        // sort the array by keys
        ksort($data);

        // append them to the data string in order
        // with no delimiters
        foreach($data as $key =&gt; $value)
            $url .= "$key$value";
            
        // This function calculates the HMAC hash of the data with the key
        // passed in
        // Note: hash_hmac requires PHP 5 &gt;= 5.1.2 or PECL hash:1.1-1.5
        // Or http://pear.php.net/package/Crypt_HMAC/
        return base64_encode(hash_hmac("sha1", $url, $this-&gt;AuthToken, true));
    }

    public function validate($expectedSignature, $url, $data = array())
    {
        return $this-&gt;computeSignature($url, $data)
            == $expectedSignature;
    }

}
</pre></body></html>