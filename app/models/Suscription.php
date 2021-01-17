<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Suscription
 */
class Suscription extends Models implements IModels {
    use DBModel;

    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function create() : array {
        try {
            global $http;
                    
            return array('success' => 0, 'message' => 'Funcionando');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function edit() : array {
        try {
            global $http;
                    
            return array('success' => 0, 'message' => 'Funcionando');
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Obtiene todas las suscripcioens
     * 
     * @return array
    */ 
    public function get() {
        $suscription = $this->db->select('*','suscripcion');
        $result = [];
        $result_libro = [];

        # Si hay reusltados
        if (false != $suscription) {
            # Recorrer
            foreach ($suscription as $s) {
                $id_libro = $s['id_libro'];
                $book = $this->db->select('*', 'libros', null, "id_libro = '$id_libro'", 1);

                if (false != $book) {
                    foreach ($book as $b) {
                        $libro_name = $b['nombre'];
                    }
                }

                $result[] = array(
                    $libro_name,
                    $s['name'],
                    $s['email'],
                    $s['message'],
                    $s['download'],
                );
            }
        }

        return $result;
    }

    /**
     * Elimina un 
     * 
     * @return void
    */ 
    public function delete() {
        global $config;
    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}