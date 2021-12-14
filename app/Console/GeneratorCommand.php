<?php


namespace App\Console;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

abstract class GeneratorCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The type of file being generated.
     *
     * @var string
     */
    protected $type = 'File';

    /**
     * Reserved names that cannot be used for generation.
     *
     * @var array
     */
    protected $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
    ];

    /**
     * Create a new controller creator command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->files = new Filesystem();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub();

    /**
     * Get the file extension.
     *
     * @return string
     */
    abstract protected function getExtension();

    /**
     * Get the destination root.
     *
     * @return string
     */
    abstract protected function getRootDestination();

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');

            return false;
        }

        $name = $this->getNameInput();

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->getNameInput() . ' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildFile());

        $this->info($this->type . ' ' . $name . ' created successfully.');
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($rawName));
    }

    /**
     * Get the destination file path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->getRootDestination() . '/' . str_replace('\\', '/', $name) . '.' . $this->getExtension();
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Build the file.
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFile()
    {
        $stub = $this->files->get($this->getStub());

        return $this->replacePlaceholders($stub);
    }

    /**
     * Get the list of stub placeholders.
     *
     * @return array
     */
    protected function getPlaceholders()
    {
        return [];
    }

    /**
     * Replace the stub placeholders.
     *
     * @param string $stub
     * @return string
     */
    protected function replacePlaceholders($stub)
    {
        $placeholders = $this->getPlaceholders();

        return str_replace(array_keys($placeholders), $placeholders, $stub);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }


    /**
     * Checks whether the given name is reserved.
     *
     * @param string $name
     * @return bool
     */
    protected function isReservedName($name)
    {
        $name = strtolower($name);

        return in_array($name, $this->reservedNames);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the file'],
        ];
    }
}
