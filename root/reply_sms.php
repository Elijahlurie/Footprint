<?php
require_once "vendor/autoload.php";
use Twilio\TwiML\MessagingResponse;

// Set the content-type to XML to send back TwiML from the PHP Helper Library
header("content-type: text/xml");

$response = new MessagingResponse();
$response->message(
    "Yoo thisis sicckk!"
);

echo $response;

//twilio phone-numbers:update "+19107734145" --sms-url="http://localhost:8000/reply_sms.php"
/*twilio api:core:incoming-phone-numbers:update --sid PN2150fab45e4eb3042573fdf41874bddc --sms-url http://localhost:8000/reply_sms.php*/
