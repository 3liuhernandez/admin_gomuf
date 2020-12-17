<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Ocrend\Kernel\Generator\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Ocrend\Kernel\Helpers as Helper;

/**
 * Comando para crear modelos
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Crud extends Command {

    const FILE = [
        'controller' => './app/controllers/{{name}}Controller.php',
        'ajax' => './assets/jscontrollers/{{name}}/',
        'view' => './app/templates/{{name}}/',
        'model' => './app/models/'
    ];

    protected function configure() {
        $this
        ->setName('app:crud')
        ->setDescription('Crea un nuevo crud completo')
        ->setHelp('Este comando se ocupa para generar todos los archivos de un crud')
        ->addArgument('name', InputArgument::REQUIRED, 'El nombre del crud');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        # Nombre del crud
        $name = $input->getArgument('name');

        /*
        |--------------------------------------------------------------------------
        | Controlador
        |--------------------------------------------------------------------------
        */

        $this->CreateController(
            $name, 
            $input, 
            $output
        );
        

        /*
        |--------------------------------------------------------------------------
        | Vista
        |--------------------------------------------------------------------------
        */
        $this->CreateView(
            $name, 
            $input, 
            $output
        );

        /*
        |--------------------------------------------------------------------------
        | Ajax
        |--------------------------------------------------------------------------
        */

        $this->CreateAjax(
            $name, 
            $input, 
            $output
        );

        /*
        |--------------------------------------------------------------------------
        | Modelo
        |--------------------------------------------------------------------------
        */

        $this->CreateModel(
            $name, 
            $input, 
            $output
        );

        /*
        |--------------------------------------------------------------------------
        | API
        |--------------------------------------------------------------------------
        */
        $this->WritteInApi(
            $name, 
            $input, 
            $output
        );

        
    }

    protected function CreateController($name, InputInterface $input, OutputInterface $output){
        # Ruta del controlador
        $routeController = str_replace('{{name}}', $name, self::FILE['controller']);
        # Verificar si ya existe
        if(file_exists($routeController)) {
            $io = new SymfonyStyle($input, $output);
            $choice = $io->ask('ADVERTENCIA: El controlador ' . $name . ' ya existe, desdea sobreescribirlo? [si][no]');
            
            if($choice != 'si') {
                exit('Ha salido del generador.');
            }
        }

        # Obtener contenido
        $controllerContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/controller');

        # Funciones extra
        $extra_functions = "global \$config;\n\t\t$".$name[0].' = new Model\\' . ucfirst($name) . "(\$router);\n\t\t
        switch(\$this->method){
            case 'create':
                \$this->template->display('{$name}/create');
            break;
            case 'edit':
                if(\$this->isset_id){
                    \$this->template->display('{$name}/edit');
                }else{
                    Helper\\Functions::redir(\$config['build']['url']);
                }
            break;
            case 'delete':
                \${$name[0]}->delete();
            break;
            default:
                \$this->template->display('{$name}/{$name}', array(
                    'data' => \${$name[0]}->get()
                ));
            break;
        }";

        # Reemplazar lo elemental
        $controllerContent = str_replace(
            ['{{controller}}', '{{extra_functions}}'], 
            [$name, $extra_functions], 
            $controllerContent
        );

        # Crear controlador
        Helper\Files::write_file($routeController, $controllerContent);

        $output->writeln([
            '',
            'Controlador ' . $name . ' creado '
        ]);
    }


    protected function CreateView($name, InputInterface $input, OutputInterface $output){
        $name = strtolower($name);

        # Nombre para el posible javascript de ajax
        $viewAjaxFolder = str_replace('{{name}}', $name, self::FILE['ajax']);
        # Ajax para crear y editar
        $viewAjaxCreate = $viewAjaxFolder .'create.js';
        $viewAjaxEdit = $viewAjaxFolder .'edit.js';

        # Ruta de la vista
        $routeViewFolder = str_replace('{{name}}', $name, self::FILE['view']);;

        # Obtener contenido
        $viewFormContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/viewForm');
        $viewContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/view');


        # Scripts
        $script_create = '<script src="'.$viewAjaxCreate.'"></script>';
        $script_edit = '<script src="'.$viewAjaxEdit.'"></script>';

        # Crear vista
        $viewFormCreate = str_replace(
            ['{{ajax_content}}', '{{view}}'],
            [$script_create, $name],
            $viewFormContent
        );

        $viewFormeEdit = str_replace(
            ['{{ajax_content}}', '{{view}}'],
            [$script_edit, $name],
            $viewFormContent
        );

        $viewContent = str_replace('{{view}}',$name,$viewContent);

        # Crear directorios
        Helper\Files::create_dir($routeViewFolder);
        Helper\Files::create_dir($viewAjaxFolder);

        # Crear archivos
        Helper\Files::write_file($routeViewFolder . 'create.twig', $viewFormCreate);
        Helper\Files::write_file($routeViewFolder . 'edit.twig', $viewFormeEdit);
        Helper\Files::write_file($routeViewFolder . $name . '.twig', $viewContent);


        $output->writeln([
            '',
            'Vista ' . $name . '.twig creada '
        ]);
    }

    protected function CreateAjax($name, InputInterface $input, OutputInterface $output){
        $name = strtolower($name);

        # Nombre para el posible javascript de ajax
        $viewAjaxFolder = str_replace('{{name}}', $name, self::FILE['ajax']);

        # Crear el javascript
        $viewAjaxContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/crud/ajax');
        $viewAjaxContentCreate = str_replace(
            ['{{view}}', '{{url}}'],
            [$name, $name .'/create'],
            $viewAjaxContent
        );
        $viewAjaxContentEdit = str_replace(
            ['{{view}}', '{{url}}'],
            [$name, $name .'/edit'],
            $viewAjaxContent
        );


        Helper\Files::create_dir($viewAjaxFolder);
        Helper\Files::write_file($viewAjaxFolder . 'create.js', $viewAjaxContentCreate);
        Helper\Files::write_file($viewAjaxFolder . 'edit.js', $viewAjaxContentEdit);

        $output->writeln([
            '',
            'Fichero javascript ' . $name . ' creado'
        ]);
    }

    protected function CreateModel($name, InputInterface $input, OutputInterface $output){
        $name = ucfirst($name);

        # Ruta del modelo
        $routeModel = self::FILE['model'] . $name . '.php';

        # Obtener contenido
        $modelContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/model');

        $trait_db_model = 'use DBModel;';
        $trait_db_model_construct = "\n\t\t\$this->startDBConexion();";

        $content = "
    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function create() : array {
        try {
            global \$http;
                    
            return array('success' => 0, 'message' => 'Funcionando');
        } catch(ModelsException \$e) {
            return array('success' => 0, 'message' => \$e->getMessage());
        }
    }\n
    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function edit() : array {
        try {
            global \$http;
                    
            return array('success' => 0, 'message' => 'Funcionando');
        } catch(ModelsException \$e) {
            return array('success' => 0, 'message' => \$e->getMessage());
        }
    }\n
    /**
     * Obtiene a todos los 
     * 
     * @return array
    */ 
    public function get() {
        return array();
    }\n
    /**
     * Elimina un 
     * 
     * @return void
    */ 
    public function delete() {
        global \$config;
    }\n";
        $modelContent = str_replace(
            ['{{model}}','{{content}}', '{{trait_db_model}}', '{{trait_db_model_construct}}'], 
            [$name, $content, $trait_db_model, $trait_db_model_construct], 
            $modelContent
        );

        # Crear modelo
        Helper\Files::write_file($routeModel, $modelContent);

        $output->writeln([
            '',
            'Modelo ' . $name . ' creado '
        ]);
    }


    protected function WritteInApi($name, InputInterface $input, OutputInterface $output){
        # Escribir en la api
        $viewApiContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/crud/api');
        $viewApiContent = "\n".str_replace(
            ['{{view}}', '{{model}}', '{{model_var}}', '{{method}}'],
            [$name .'/create', ucfirst($name), $name[0], 'create'],
            $viewApiContent
        ) . "\n\n" . 
        str_replace(
            ['{{view}}', '{{model}}', '{{model_var}}', '{{method}}'],
            [$name .'/edit', ucfirst($name), $name[0], 'edit'],
            $viewApiContent
        ) . "\n\n";

        Helper\Files::write_in_file('./api/controllers/post.controllers.php', $viewApiContent);
        $output->writeln([
            '',
            'Fichero ./api/controllers/post.controllers.php modificado'
        ]);
    }
}