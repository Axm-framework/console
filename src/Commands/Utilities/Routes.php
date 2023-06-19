<?php

/**
 * This file is part of Axm 4 framework.
 *
 * (c) Axm Foundation <admin@Axm.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Axm\Console\Commands\Utilities;

use Axm;
use Axm\Console\BaseCommand;
use Axm\Console\CLI;
use Axm\Http\Response;
use Axm\Http\Request;
use Axm\Http\Router;



/**
 * Lists all of the user-defined routes. This will include any Routes files
 * that can be discovered, but will NOT include any routes that are not defined
 * in a routes file, but are instead discovered through auto-routing.
 */
class Routes extends BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'Axm';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'routes';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Displays all of user-defined routes. Does NOT display auto-detected routes.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'routes';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * 
     */
    private ?Router $router;


    /**
     * Displays the help for the spark cli script itself.
     */
    public function run(array $params)
    {
        $collection = $this->router();
        $methods    = $collection::$verbs;   // obtine los verbos['get',post,head....]

        $tbody = [];
        foreach ($methods as $method) {
            $routes = $collection::getRoutes($method);

            foreach ($routes as $route => $handler) {
                // filter for strings, as callbacks aren't displayable
                if (is_string($handler)) {
                    $tbody[] = [
                        $method,
                        $route,
                        $handler,
                    ];
                }
                if (is_array($handler)) {
                    $tbody[] = [
                        $method,
                        $route,
                        $this->getProcessAddressHandle($handler)
                    ];
                }
            }
        }

        $thead = [
            'Method',
            'Route',
            'Handler|Dir',
        ];

        CLI::table($tbody, $thead);
    }


    private function router(): Router
    {
        if (!isset($this->routes)) {

            $app = Axm::app();
            $app->openRoutesUser();           //abrir la configuración de rutas usuario
            $this->router = $app->router;
        }

        return $this->router;
    }


    /**
     * devuelve una cadena que representa el manejador de una ruta en función de los datos 
     * proporcionados.Se utiliza para identificar el controlador y método asociados a una ruta
     *  determinada en un sistema web.
     */
    public function getProcessAddressHandle($data): string
    {
        if (is_object($data)) {
            $output = 'Object(' . get_class($data) . ')';
        } elseif (is_array($data)) {
            $output = '';
            if (is_object($data[0])) {
                $output .= 'Object(' . get_class($data[0]) . ')';
            } else {
                $output .= $data[0];
            }
            $output .= '::' . $data[1];
        } else {
            $output = '';
        }
        return $output;
    }
}
