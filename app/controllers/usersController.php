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
 * Controlador users/
*/
class usersController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router, array(
            'users_logged' => true
        ));
        global $config;
		$u = new Model\People($router);
		
        switch($this->method){
            case 'create':
                $this->template->display('users/create');
            break;
            case 'edit':
                if($this->isset_id && false != ($data = $u->get(false))){
                    $this->template->display('users/edit', array(
                        'data' => $data[0]
                    ));
                }else{
                    Helper\Functions::redir($config['build']['url'] . 'users');
                }
            break;
            case 'delete':
                $u->delete();
            break;
            default:
                $this->template->display('users/users');
            break;
        }
    }
}