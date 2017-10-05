<?php

require '../vendor/autoload.php';

$data = (new \eznio\xget\Xget(new \GuzzleHttp\Client()))
	->setUrl('http://yclist.com/')
	->parse([
		'domains' => '//tr/td/a/.'
	]);

var_dump($data);