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
 * Modelo Users
 */
class People extends Models implements IModels {
    use DBModel;

    /**
     * Revisa si las contraseñas son iguales
     *
     * @param string $pass : Contraseña sin encriptar
     * @param string $pass_repeat : Contraseña repetida sin encriptar
     *
     * @throws ModelsException cuando las contraseñas no coinciden
     */
    private function checkPassMatch(string $pass, string $pass_repeat) {
        if ($pass != $pass_repeat) {
            throw new ModelsException('Las contraseñas no coinciden.');
        }
    }

    /**
     * Verifica el email introducido, tanto el formato como su existencia en el sistema
     *
     * @param string $email: Email del usuario
     *
     * @throws ModelsException en caso de que no tenga formato válido o ya exista
     */
    private function checkEmail(string $email, $id_user = null) {
        # Formato de email
        if (!Helper\Strings::is_email($email)) {
            throw new ModelsException('El email no tiene un formato válido.');
        }
        # Existencia de email
        $email = $this->db->scape($email);
        $where = "email='$email'";

        # Editar
        if (null != $id_user) {
            $id_user = $this->db->scape($id_user);
            $where .= " AND id_user <> '$id_user'";
        }

        $query = $this->db->select('id_user', 'users', null, $where, 1);
        if (false !== $query) {
            throw new ModelsException('El email introducido ya existe.');
        }
    }

    /**
     * Verificar número de teléfono
     * 
     * @param string $phone : Número de teléfono
     * 
     * @return void
     */
    private function checkPhone(string $phone, string $message){
        # Caracteres a reemplazar
        $chars = ['.', '-'. ' ', ')', '('];

        # Verificación
        if (!is_numeric( str_replace($chars, '', $phone) )) {
            throw new ModelsException($message);
        }
    }

    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function create() : array {
         try {
            global $http, $config;

            # Obtener los datos $_POST
            $name = $http->request->get('name');
            $email = $http->request->get('email');
            $phone = $http->request->get('phone');
            $pass = $http->request->get('pass');
            $pass_repeat = $http->request->get('pass_repeat');

            # Verificar que no están vacíos
            if (Helper\Functions::e($name, $email, $phone, $pass, $pass_repeat)) {
                throw new ModelsException('Todos los campos con * son necesarios');
            }

            # Verificar email 
            $this->checkEmail($email);

            # Verificar teléfono
            $this->checkPhone($phone, 'Número de teléfono inválido');

            # Veriricar contraseñas
            $this->checkPassMatch($pass, $pass_repeat);

            # Registrar al usuario
            $id_user = $this->db->insert('users', array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'pass' => Helper\Strings::hash($pass)
            ));


            return array('success' => 1, 'message' => 'Guardado con éxito.', 'url' => $config['build']['url'] . 'users');
        } catch (ModelsException $e) {
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
            global $http, $config;

            # Obtener los datos $_POST
            $name = $http->request->get('name');
            $email = $http->request->get('email');
            $phone = $http->request->get('phone');
            $pass = $http->request->get('pass');
            $pass_repeat = $http->request->get('pass_repeat');
            $this->id = $http->request->get('id_user');

            # Verificar que no están vacíos
            if (Helper\Functions::e($name, $email, $phone)) {
                throw new ModelsException('Todos los campos con * son necesarios');
            }

            # Verificar email 
            $this->checkEmail($email, $this->id);

             # Verificar teléfono
            $this->checkPhone($phone, 'Número de teléfono inválido');

            $data = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            );


            if (!Helper\Functions::emp($pass) || !Helper\Functions::emp($pass)) {
                # Veriricar contraseñas
                $this->checkPassMatch($pass, $pass_repeat);

                # Guardar contraseña
                $data['pass'] = Helper\Strings::hash($pass);
            }

            

            # Registrar al usuario
            $id_user = $this->db->update('users', $data, "id_user = '$this->id'", 1);
                    
            return array('success' => 1, 'message' => 'Guardado con éxito.', 'url' => $config['build']['url'] . 'users/edit/'.$this->id);
        } catch(ModelsException $e) {
            return array('success' => 0, 'message' => $e->getMessage());
        }
    }

    /**
     * Obtiene a todos los usuarios o uno segun el id
     * 
     * @return array
    */ 
    public function get($multi = true) {
        # Obtener todos los usuarios
        if ($multi) {
            $users = $this->db->select('*', 'users');
            $result = [];

            # Si hay resultado
            if (false != $users) {
                # recorremos
                foreach ($users as $u) {

                    $delete = '';
                    if ($u['id_user'] != $this->id_user) {
                        $delete = '<a href="javascript:deleteElement(\'users\', \''.$u['id_user'].'\')">
                                <i data-toggle="tooltip" data-placement="top" title="Eliminar" class="fa fa-trash" title="Eliminar"></i>
                            </a>
                            &nbsp;';
                    }
                    
                    $result[] = [
                        $u['name'],
                        $u['email'],
                        $u['phone'],
                        '<div class="text-center">
                            '.$delete.'
                            <a href="users/edit/'.$u['id_user'].'">
                                <i data-toggle="tooltip" data-placement="top" title="Editar" class="fa fa-edit" title="Editar"></i>
                            </a>
                        </div>'
                    ];
                }
            }

            return $result;


        }
        return $this->db->select('*', 'users', null, "id_user = '$this->id'", 1);
    }

    /**
     * Elimina un usuario
     * 
     * @return void
    */ 
    public function delete() {
        global $config;

        $action = '?error=true';

        if ($this->id_user != $this->id) {
            $this->db->delete('users', "id_user = '$this->id'", 1);
            $action = '?success=true';
        }

        Helper\Functions::redir($config['build']['url'] . 'users'.$action);
    }


    /**
     * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
		$this->startDBConexion();
    }
}