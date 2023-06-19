<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Axm\Console\Commands\Housekeeping;

use Axm\Console\BaseCommand;
use Axm\Console\CLI;

/**
 * ClearDebugbar Command
 */
class ClearDebugbar extends BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'Housekeeping';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'debugbar:clear';

    /**
     * The Command's usage
     *
     * @var string
     */
    protected $usage = 'debugbar:clear';

    /**
     * The Command's short description.
     *
     * @var string
     */
    protected $description = 'Clears all debugbar JSON files.';

    /**
     * Actually runs the command.
     */
    public function run(array $params)
    {
        helpers('filesystem', true);

        if (!delete_files(STORAGE_PATH . '/framework/debugbar')) {
            // @codeCoverageIgnoreStart
            CLI::error('Error deleting the debugbar JSON files.');
            CLI::newLine();

            return;
            // @codeCoverageIgnoreEnd
        }

        CLI::write('Debugbar cleared.', 'green');
        CLI::newLine();
    }
}
