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

/**
    * Inicio de sesión
    *
    * @return json
*/  
$app->post('/login', function() use($app) {
    $u = new Model\Users;   

    return $app->json($u->login());   
});

/**
    * Registro de un usuario
    *
    * @return json
*/
$app->post('/register', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->register());   
});

/**
    * Recuperar contraseña perdida
    *
    * @return json
*/
$app->post('/lostpass', function() use($app) {
    $u = new Model\Users; 

    return $app->json($u->lostpass());   
});

/**
 * Endpoint para users/create
 *
 * @return json
*/
$app->post('/users/create', function() use($app) {
    $u = new Model\People; 

    return $app->json($u->create());   
});

/**
 * Endpoint para users/edit
 *
 * @return json
*/
$app->post('/users/edit', function() use($app) {
    $u = new Model\People; 

    return $app->json($u->edit());   
});



/**
 * Endpoint para suscription/create
 *
 * @return json
*/
$app->post('/suscription/create', function() use($app) {
    $s = new Model\Suscription; 

    return $app->json($s->create());   
});

/**
 * Endpoint para suscription/edit
 *
 * @return json
*/
$app->post('/suscription/edit', function() use($app) {
    $s = new Model\Suscription; 

    return $app->json($s->edit());   
});

