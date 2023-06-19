<?php

namespace Axm\Console\Commands\Utilities;

use Axm\Console\BaseCommand;

class Git extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Utilities';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'git';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Este comando ejecuta git';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'git <method>';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Run a Git Command
     *
     * @param string $command The Git command to run
     * @param bool $output Whether to output the command result
     * @return string|null The command result or null if $output is false
     */
    public function runCommand($command, $output = true)
    {
        $outputRedirect = $output ? '' : ' > /dev/null 2>&1';
        $command = 'git ' . $command . $outputRedirect;
        exec($command, $outputLines, $status);

        if ($status === 0) {
            return implode(PHP_EOL, $outputLines);
        }

        return null;
    }

    /**
     * Check if the current directory is a Git repository
     *
     * @return bool
     */
    public function isRepository()
    {
        return $this->runCommand('rev-parse --is-inside-work-tree', false) !== null;
    }

    /**
     * Get the current branch name
     *
     * @return string|null The current branch name or null if not in a repository
     */
    public function getCurrentBranch()
    {
        return $this->runCommand('rev-parse --abbrev-ref HEAD', false);
    }

    /**
     * Fetch the latest changes from the remote repository
     *
     * @return string|null The fetch result or null if not in a repository or fetch failed
     */
    public function fetch()
    {
        return $this->runCommand('fetch');
    }

    /**
     * Switch to a different branch
     *
     * @param string $branch The branch name to switch to
     * @return string|null The switch result or null if not in a repository or switch failed
     */
    public function switchBranch($branch)
    {
        return $this->runCommand('checkout ' . $branch);
    }

    /**
     * Create a new branch
     *
     * @param string $branch The branch name to create
     * @return string|null The create result or null if not in a repository or create failed
     */
    public function createBranch($branch)
    {
        return $this->runCommand('branch ' . $branch);
    }

    /**
     * Delete a branch
     *
     * @param string $branch The branch name to delete
     * @return string|null The delete result or null if not in a repository or delete failed
     */
    public function deleteBranch($branch)
    {
        return $this->runCommand('branch -D ' . $branch);
    }

    /**
     * Get the status of the repository
     *
     * @return string|null The repository status or null if not in a repository or fetch failed
     */
    public function getStatus()
    {
        return $this->runCommand('status');
    }

    /**
     * Commit changes in the repository
     *
     * @param string $message The commit message
     * @return string|null The commit result or null if not in a repository or commit failed
     */
    public function commit($message)
    {
        return $this->runCommand('commit -m "' . $message . '"');
    }

    /**
     * Push changes to a remote repository
     *
     * @param string $remote The remote repository name
     * @param string $branch The branch to push
     * @return string|null The push result or null if not in a repository or push failed
     */
    public function push($remote = 'origin', $branch = 'master')
    {
        return $this->runCommand('push ' . $remote . ' ' . $branch);
    }

    /**
     * Pull changes from a remote repository
     *
     * @param string $remote The remote repository name
     * @param string $branch The branch to pull
     * @return string|null The pull result or null if not in a repository or pull failed
     */
    public function pull($remote = 'origin', $branch = 'master')
    {
        return $this->runCommand('pull ' . $remote . ' ' . $branch);
    }

    /**
     * 
     */
    public function run(array $var = null)
    {
        return;
    }
}
