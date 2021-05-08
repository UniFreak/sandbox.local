<?php
/**
 * NOTE: cannot get this to work...
 */
try {
    $client = new SoapClient("http://localhost:8080/wsdl");
    var_dump($client->getEvents());
} catch (SoapFault $e) {
    var_dump($e);
}