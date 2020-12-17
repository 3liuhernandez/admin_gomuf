<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace app\controllers;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;

/**
 * Controlador suscription/
*/
class suscriptionController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router);
        global $config;
		$s = new Model\Suscription($router);
		
        switch($this->method){
            case 'create':
                $this->template->display('suscription/create');
            break;
            case 'edit':
                if($this->isset_id){
                    $this->template->display('suscription/edit');
                }else{
                    Helper\Functions::redir($config['build']['url']);
                }
            break;
            case 'delete':
                $s->delete();
            break;
            default:
                $this->template->display('suscription/suscription', array(
                    'data' => $s->get()
                ));
            break;
        }
    }
}