#!/usr/bin/php
<?php

require_once('nanoleaf.php');

$nl = new Nanoleaf('tokengoeshere', '192.168.1.123');

$nl->debug = true;

$nl->ApiGet('');

$nl->ApiPut('state', '{"on":{"value":true}}'."\r\n");
sleep(2);
$nl->ApiPut('state', '{"on":{"value":false}}'."\r\n");
?>