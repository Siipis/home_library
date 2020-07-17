<?php

namespace App\Console\Commands;

use App\Console\GeneratorCommand;

class MakeLayout extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:layout {name} {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twig layout';

    /**
     * The type of file being generated.
     *
     * @var string
     */
    protected $type = 'Twig layout';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->laravel->basePath('stubs/layout.stub');
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    protected function getExtension()
    {
        return 'twig';
    }

    /**
     * Get the destination root.
     *
     * @return string
     */
    protected function getRootDestination()
    {
        return config('view.layouts_path');
    }
}
