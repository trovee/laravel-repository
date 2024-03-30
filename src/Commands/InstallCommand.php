<?php

namespace Trovee\Repository\Commands;

use Illuminate\Console\Command;
use Trovee\Repository\RepositoryServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'repository:install {--force : Overwrite any existing files} {--interactive : The command will ask which tags should be published}';

    protected $description = 'Install Trovee Repository package';

    public function handle()
    {

        $this->components->info('Installing Trovee Repository package...');

        $this->call('vendor:publish', [
            '--provider' => RepositoryServiceProvider::class,
            '--tag' => 'repository-config',
        ] + ($this->getOptions()));

        $this->components->info('Trovee Repository package installed successfully.');
    }
}
