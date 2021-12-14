<?php


namespace App\Http\Forms;


use App\Http\Forms\Exceptions\AbstractFormException;
use App\Http\Forms\Exceptions\UnsentFormException;
use Barryvdh\Form\CreatesForms;
use Barryvdh\Form\ValidatesForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form as FormBuilder;

class Form
{
    use CreatesForms, ValidatesForms;

    /**
     * @var FormBuilder
     */
    protected $form;

    /**
     * @var Model|array
     */
    protected $model;

    /**
     * @var string
     */
    protected $size = 'default';

    /**
     * @var bool
     */
    private $isInitialized = false;

    /**
     * Form constructor.
     *
     * @throws AbstractFormException
     */
    public function __construct()
    {
        if (is_null($this->model)) {
            throw new AbstractFormException("The class attribute 'model' is required.");
        }
    }

    /**
     * Add fields to the form.
     *
     * @return void
     */
    public function build()
    {
        // Add no fields by default.
        // the CSRF token is added automatically.
    }

    /**
     * Return the form.
     *
     * @param array $options
     * @param Model|null $model
     * @return FormBuilder
     */
    public function make($options = [], Model $model = null)
    {
        $this->initForm($options, $model);

        return $this->form();
    }

    /**
     * Handle the form request.
     *
     * @param Request $request
     * @param Model|null $model
     * @return Model|boolean
     * @throws UnsentFormException
     */
    public function get(Request $request, Model $model = null)
    {
        $this->initForm([], $model);

        $this->form->handleRequest($request);

        if (!$this->form->isSubmitted()) {
            $this->form->submit($request->get($this->form->getName()));

            $this->form->handleRequest($request);
        }

        if ($this->isValid($request)) {
            return $this->model();
        }

        return false;
    }

    /**
     * Add a field to the form. The name must be unique,
     * otherwise the field is overwritten.
     *
     * @param string $name
     * @param string $type
     * @param array $options
     */
    protected function add(string $name, string $type, array $options = [])
    {
        // If no label has been set, attempt to automatically translate it
        if (!array_key_exists('label', $options)) {
            $trans = 'fields.' . $name;

            if (Lang::has($trans)) {
                $options['label'] = $trans;
            }
        }

        if (!array_key_exists('required', $options) && array_key_exists('rules', $options)) {
            $options['required'] = Str::contains('required', $options['rules']);
        }

        $this->form->add($name, $type, $options);
    }

    /**
     * Remove the field with the given name.
     *
     * @param string $name
     * @return FormBuilder
     */
    protected function remove(string $name)
    {
        return $this->form->remove($name);
    }

    /**
     * Return whether a field with the given name exists.
     *
     * @param string $name
     * @return bool
     */
    protected function has(string $name)
    {
        return $this->form->has($name);
    }

    /**
     * Get the form data as a Model.
     *
     * @return Model
     */
    public function model()
    {
        if (!$this->form->getData() instanceof Model) return null;

        return $this->form->getData();
    }

    /**
     * @return bool
     */
    public function modelExists()
    {
        return $this->model()->exists;
    }

    /**
     * @return bool
     */
    public function modelIsNew()
    {
        return !$this->modelExists();
    }

    /**
     * Get the form data as an array.
     *
     * @return array
     */
    public function array()
    {
        return collect($this->form->getData())->toArray();
    }

    /**
     * Return the underlying FormBuilder instance.
     *
     * @return FormBuilder
     */
    public function form()
    {
        if (!$this->isInitialized) {
            $this->initForm();
        }

        return $this->form;
    }

    /**
     * Return all fields.
     *
     * @return array
     */
    protected function fields()
    {
        return $this->form->all();
    }

    /**
     * Return a field by name.
     *
     * @param string $name
     * @return FormBuilder
     */
    protected function field(string $name)
    {
        return $this->form->get($name);
    }

    /**
     * Return all validation rules in the form.
     *
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        foreach ($this->form->all() as $field) {
            if ($field->getConfig()->hasOption('rules')) {
                $r = $field->getConfig()->getOption('rules');

                $rules[$field->getName()] = array_unique($r);
            }
        }

        return $rules;
    }

    /**
     * Prepare the form.
     *
     * @param array $options
     * @param Model $model
     */
    protected function initForm(array $options = [], Model $model = null)
    {
        $this->form = $this->createNamed(Str::snake(class_basename($this)),
            FormType::class,
            $this->makeModel($model),
            $options
        );

        $this->build();

        $this->isInitialized = true;
    }

    /**
     * Validate the form.
     *
     * @param Request $request
     * @return bool
     * @throws UnsentFormException
     */
    public function isValid(Request $request)
    {
        if (!empty($request->all())) {
            if ($this->form->isDisabled()) return true;

            $this->validateForm($this->form, $request, $this->rules());

            return true;
        }

        throw new UnsentFormException("The form " . get_called_class() . " contains no data.");
    }

    /**
     * Return the model instance.
     *
     * @param Model|null $model
     * @return Model|array
     */
    private function makeModel(Model $model = null)
    {
        // If the model is an array, do nothing.
        if (is_array($this->model)) return $this->model;

        // If the model is already instantiated, do nothing.
        if ($model instanceof Model) return $model;

        // Attempt to create a new model instance from the class config.
        $className = is_null($model) ? $this->model : $model;
        return new $className;
    }
}
