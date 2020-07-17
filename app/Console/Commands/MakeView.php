<?php

namespace App\Console\Commands;


use App\Console\GeneratorCommand;

class MakeView extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {name} {--layout=core} {--title=} {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twig view';

    /**
     * The type of file being generated.
     *
     * @var string
     */
    protected $type = 'Twig view';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->laravel->basePath('stubs/view.stub');
    }

    /**
     * Get the list of stub placeholders.
     *
     * @return array
     */
    protected function getPlaceholders()
    {
        return [
            '_LAYOUT_' => $this->option('layout'),
            '_TITLE_' => $this->option('title'),
        ];
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    protected function getExtension()
    {
        return "twig";
    }

    /**
     * Get the file destination path.
     *
     * @return string
     */
    protected function getRootDestination()
    {
        return resource_path('views');
    }
}
