<?php

declare(strict_types=1);

namespace ArtisanCommandApibResource;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ApibResourceGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'generate:apib';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generatet apib resource markup';

    /**
     * @return bool|void|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $classPath = $this->argument('class_path');
    }

    /**
     * @return array<array<int, string>>
     */
    protected function getArguments()
    {
        return array_merge(
            parent::getArguments(),
            [
                ['class_path', InputArgument::REQUIRED, 'the class path'],
            ]
        );
    }
}
