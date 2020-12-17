<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

use app\models as Model;

$app->get('/', function() use($app) {
    return $app->json(array()); 
});

/**
    * Obteiner usuarios
    *
    * @return json
*/  

$app->get('/users', function() use($app) {
	$u = new Model\People;
    return $app->json([
    	'data' => $u->get()
    ]); 
});

/**
    * Obteiner suscripciones
    *
    * @return json
*/  

$app->get('/suscription', function() use($app) {
	$s = new Model\Suscription;
    return $app->json([
    	'data' => $s->get()
    ]); 
});