<?php

/**
 * This file is part of Axm 4 framework.
 *
 * (c) Axm Foundation <admin@Axm.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Axm\Console;

use Axm\Console\CLI;
use Axm\Console\BaseCommand;
use Axm\Exception\AxmException;
use ReflectionClass;
use ReflectionException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Core functionality for running, listing, etc commands.
 */
class Commands
{
    /**
     * The found commands.
     *
     * @var array
     */
    public $commands    = [];
    private $classCache = [];

    const COMMANDS_FOLDER = 'console' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR;
    const COMMAND_EXTENSION = 'php';

    /**
     * Constructor
     *
     * @param Logger|null $logger
     */
    public function __construct()
    {
        return $this->discoverCommands();
    }


    /**
     * Runs a command given
     */
    public function run(string $command, array $params)
    {
        if (!$this->verifyCommand($command, $this->commands)) {
            return;
        }

        // The file would have already been loaded during the
        // createCommandList function...
        $className = $this->commands[$command]['class'];
        $class     = new $className();

        return $class->run($params);
    }

    /**
     * Provide access to the list of commands.
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }


    /**
     * Discovers all commands in the framework and within user code,
     * and collects instances of them to work with.
     */
    protected function discoverCommands(): void
    {
        if ($this->commands !== []) {
            return;
        }

        $commands_folder = AXM_PATH . DIRECTORY_SEPARATOR . self::COMMANDS_FOLDER;

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($commands_folder));

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile() || $fileInfo->getExtension() !== self::COMMAND_EXTENSION) {
                continue;
            }

            $className = self::getClassnameFromFile($fileInfo->getPathname());

            if (!$className || !class_exists($className)) {
                continue;
            }

            try {
                $class = new ReflectionClass($className);

                if (!$class->isInstantiable() || !$class->isSubclassOf(BaseCommand::class)) {
                    continue;
                }

                /** @var BaseCommand $class */
                $class = new $className();

                if (isset($class->group)) {
                    $this->commands[$class->name] = [
                        'class'       => $className,
                        'file'        => $fileInfo->getPathname(),
                        'group'       => $class->group,
                        'description' => $class->description,
                    ];
                }

                unset($class);
            } catch (ReflectionException $e) {
                CLI::error($e->getMessage());
            }
        }

        asort($this->commands);
    }


    private function getClassnameFromFile(string $filePath, bool $includeNamespace = true)
    {
        // Verificar si el resultado está en caché
        if (isset($this->classCache[$filePath][$includeNamespace])) {
            return $this->classCache[$filePath][$includeNamespace];
        }

        // Verificar si el archivo existe y se puede leer
        if (!file_exists($filePath)) {
            throw new AxmException("El archivo $filePath no existe.");
        }

        if (!is_readable($filePath)) {
            throw new AxmException("El archivo $filePath no se puede leer.");
        }

        // Leer el contenido del archivo
        $contents = file_get_contents($filePath);

        // Buscar el espacio de nombres de la clase
        $namespace = '';
        $namespaceRegex = '/^\s*namespace\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*)\s*;/m';
        if (preg_match($namespaceRegex, $contents, $matches)) {
            $namespace = '\\' . trim($matches[1], '\\');
        }

        // Buscar el nombre de la clase
        $class = '';
        $classRegex = '/^\s*(abstract\s+|final\s+)?class\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/m';
        if (preg_match($classRegex, $contents, $matches)) {
            $class = trim($namespace . '\\' . $matches[2], '\\');
        }

        // Almacenar en caché y devolver el resultado
        $this->classCache[$filePath][$includeNamespace] = $class;
        return $includeNamespace ? $class : substr(strrchr($class, "\\"), 1);
    }


    /**
     * Verifies if the command being sought is found
     * in the commands list.
     */
    public function verifyCommand(string $command, array $commands): bool
    {
        if (isset($commands[$command])) {
            return true;
        }

        $command = $command;
        $message = "Command Not Found: [$command]";

        if ($alternatives = $this->getCommandAlternatives($command, $commands)) {
            if (count($alternatives) === 1) {
                $message .= "\n\n" . 'Command in Singular' . "\n    ";
            } else {
                $message .= "\n\n" . 'CLI.altCommandPlural' . "\n    ";
            }

            $message .= implode("\n    ", $alternatives);
        }

        CLI::error($message);
        CLI::newLine();

        return false;
    }

    /**
     * Finds alternative of `$name` among collection
     * of commands.
     */
    protected function getCommandAlternatives(string $name, array $collection): array
    {
        $alternatives = [];

        foreach (array_keys($collection) as $commandName) {
            $lev = levenshtein($name, $commandName);

            if ($lev <= strlen($commandName) / 3 || strpos($commandName, $name) !== false) {
                $alternatives[$commandName] = $lev;
            }
        }

        ksort($alternatives, SORT_NATURAL | SORT_FLAG_CASE);

        return array_keys($alternatives);
    }
}
