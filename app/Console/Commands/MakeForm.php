<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MakeForm extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:form {name} {model?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form class';

    /**
     * The type of the class being generated.
     *
     * @var string
     */
    protected $type = 'Form';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->laravel->basePath('stubs/form.class.stub');
    }

    /**
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Forms';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $formNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->argument('model')) {
            $replace = $this->buildModelReplacements($replace);
        } else {
            $replace = $this->buildReplacementsWithoutModel($replace);
        }

        $replace["use {$formNamespace}\Form;\n"] = '';
        $replace["use [];\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the form without a model.
     *
     * @param array $replace
     * @return array
     */
    protected function buildReplacementsWithoutModel(array $replace)
    {
        return array_merge($replace, [
            '{{ namespacedModel }}' => '[]',
            '{{namespacedModel}}' => '[]',
            '{{ modelClass }}' => '[]',
            '{{modelClass}}' => '[]',
        ]);
    }

    /**
     * Build the model replacement values.
     *
     * @param array $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->argument('model'));

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ modelClass }}' => class_basename($modelClass) . '::class',
            '{{modelClass}}' => class_basename($modelClass) . '::class',
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $model;
        }

        return $model;
    }
}
