<?php

namespace Trovee\Repository\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'repository:install';

    protected $description = 'Install Trovee Repository package';

    public function handle()
    {
        $this->info('Installing Trovee Repository package...');

        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => "Trovee\Repository\RepositoryServiceProvider",
            '--tag' => 'config',
        ]);

        $this->info('Trovee Repository package installed successfully.');
    }
}
