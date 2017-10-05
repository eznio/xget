<?php

require '../vendor/autoload.php';

$data = (new \eznio\xget\Xget(new \GuzzleHttp\Client()))
	->setUrl('http://yclist.com/')
	->parse([
		'@' => '//tbody/tr[@class="operating"]',
		'name' => '//td[not(*)]/.',
		'domain' => '//td/a/.',
	]);

var_dump($data);