<?php

namespace Axm\Console;

/**
 * Axm Framework PHP
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @category   Axm
 * @package    Console
 *
 * @copyright  Copyright (c) 2005 - 2020 Axm Team (http://www.axm.com)
 * @license    https://github.com/axm/axm/blob/master/LICENSE   New BSD License
 */

use Axm;
use Axm\Exception\AxmCLIException;
use Axm\Console\CLI;

class ConsoleApplication
{
    private $version = '1.0.0';
    private $axmRaw  = null;
    private $commands;
    private $colors = [
        'dark_gray'    => '1;30',
        'blue'         => '0;34',
        'light_blue'   => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'purple'       => '0;35',
        'light_purple' => '1;35',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        Axm::iniApplication();
        $this->commands = new Commands();
    }

    /**
     * Runs the current command discovered on the CLI.
     *
     * @throws Exception
     *
     * @param bool $useSafeOutput Indica si se debe usar salida segura
     * @return mixed El resultado de la ejecución del comando
     */
    public function init()
    {
        try {

            // Obtener el comandos y los segmentos
            $command = CLI::getSegment(1) ?? 'list';
            $params  = CLI::getSegments();

            return $this->commands->run($command, $params);
        } catch (AxmCLIException $e) {
            throw new AxmCLIException('Ha ocurrido un error en la console:' . $e);
        }
    }


    /**
     * Devuelve el contenido del archivo de logo de Axm.
     *
     * @return string El contenido del archivo de logo de Axm.
     */
    public function rawLogo()
    {
        $this->axmRaw .= "
    _____                  
   /  _  \\ ___  ___ _____     ___ _    ___                                                              
  /  /_\  \\\\  \\/  //     \\   / __| |  |_ v{$this->version}                                            
 /    |    \\>    <|  Y Y  \\ | |__| |__ | |                                                      
 \\____|__  /__/\\_ \\__|_|  /  \\___|____|___|                                                           
         \\/      \\/     \\/ ";

        return $this->axmRaw;
    }




    /**
     * Muestra información básica sobre la consola.
     *
     * @param bool $suppress Si se establece en verdadero, se suprime la salida.
     */
    public function showHeader(bool $suppress = false)
    {
        if ($suppress) return;

        $color      = array_rand($this->colors);
        $logo       = $this->rawLogo();
        $serverTime = date('Y-m-d H:i:s');
        $timeZone   = date('P');

        CLI::write($logo, $color);
        CLI::write("Axm Command Line Tool - Server Time: {$serverTime} UTC{$timeZone}", $color);

        CLI::newLine();
    }
}
