<?php
/******************
 * config file for
 * defined all setings like 
 * @param API key
 * @param Authetication key
 * @param sender phone numebr
 *  
 *  */
//set our AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "AC89a48ba1b8362c0aa5673b3e9d87268b";
$AuthToken = "c77e41f2170824f79929d226567ee37c";

//Enabled check Authentication
/*$http = new Services_Twilio_TinyHttp(
		'https://api.twilio.com',
		array('curlopts' => array(
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_CAINFO => getcwd() . "/twilio_ssl_certificate.crt")));
*/
//disabled check Authentication
$http = new Services_Twilio_TinyHttp(
		'https://api.twilio.com',
		array('curlopts' => array(CURLOPT_SSL_VERIFYPEER => false))
);
/**************************
 *date when api's are purched used to authentication 
 */
$authDate = '2010-04-01';
// Step 6: Change the 'From' number below to be a valid Twilio number
// that you've purchased, or the (deprecated) Sandbox number
//$twilioPhone = '+1 443-731-2027';
$twilioPhone = '+1 408-675-5246';
$dounloadUrl= APPS_PATH;

		