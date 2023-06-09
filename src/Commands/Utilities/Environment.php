<?php

/**
 * This file is part of Axm framework.
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

/**
 * Command to display the current environment,
 * or set a new one in the `.env` file.
 */
final class Environment extends BaseCommand
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
    protected $name = 'env';

    /**
     * The Command's short description
     *
     * @var string
     */
    protected $description = 'Retrieves the current environment, or set a new one.';

    /**
     * The Command's usage
     *
     * @var string
     */
    protected $usage = 'env [<environment>]';

    /**
     * The Command's arguments
     *
     * @var array<string, string>
     */
    protected $arguments = [
        'environment' => '[Optional] The new environment to set. If none is provided, this will print the current environment.',
    ];

    /**
     * The Command's options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Allowed values for environment. `testing` is excluded
     * since spark won't work on it.
     *
     * @var array<int, string>
     */
    private static $knownTypes = [
        'production',
        'debug',
    ];

    /**
     * {@inheritDoc}
     */
    public function run(array $params)
    {
        array_shift($params);

        $environment = Axm::getEnvironment();
        if ($params === []) {
            CLI::write(sprintf('Your environment is currently set as %s.', CLI::color($environment, 'green')));
            CLI::newLine();

            return;
        }

        $env = strtolower(array_shift($params));

        if ($env === 'testing') {
            CLI::error('The "testing" environment is reserved for PHPUnit testing.', 'light_gray', 'red');
            CLI::error('You will not be able to run axm under a "testing" environment.', 'light_gray', 'red');
            CLI::newLine();

            return;
        }

        if (!in_array($env, self::$knownTypes, true)) {
            CLI::error(sprintf('Invalid environment type "%s". Expected one of "%s".', $env, implode('" and "', self::$knownTypes)), 'light_gray', 'red');
            CLI::newLine();

            return;
        }

        if (!$this->writeNewEnvironmentToConfigFile($env)) {
            CLI::error('Error in writing new environment to .env file.', 'light_gray', 'red');
            CLI::newLine();

            return;
        }

        CLI::write(sprintf('Environment is successfully changed to "%s".', $env), 'green');
        CLI::write('The ENVIRONMENT constant will be changed in the next script execution.');
        CLI::newLine();
    }

    /**
     * @see https://regex101.com/r/4sSORp/1 for the regex in action
     */
    function writeNewEnvironmentToConfigFile($mode)
    {
        $modeValues = [
            'production' => true,
            'debug' => false,
        ];

        if (!array_key_exists($mode, $modeValues)) {
            return false;
        }

        $fileConfig = APP_PATH . '/Config/Environment.php';

        if (!is_file($fileConfig)) {
            CLI::write('El archivo de configuracion "Environment" no se encuentra.', 'yellow');
            CLI::newLine();

            return false;
        }

        $config = file_get_contents($fileConfig);
        $newConfig = preg_replace("/('PRODUCTION'\s*=>\s*)(true|false)/", '${1}' . var_export($modeValues[$mode], true), $config, -1, $count);

        if ($count === 0) {
            return false;
        }

        return file_put_contents($fileConfig, $newConfig) !== false;
    }
}
