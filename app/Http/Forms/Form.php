<?php


namespace App\Http\Forms;


use App\Http\Forms\Exceptions\AbstractFormException;
use App\Http\Forms\Exceptions\UnsentFormException;
use Barryvdh\Form\CreatesForms;
use Barryvdh\Form\ValidatesForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
     * @var Model
     */
    protected $model;

    /**
     * @var bool
     */
    private $isInitialized = false;

    /**
     * Form constructor.
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
     * @return void
     */
    public function build()
    {
        // Add no fields by default.
        // the CSRF token is added automatically.
    }

    /**
     * Return the form.
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
     * @param Request $request
     * @param Model|null $model
     * @return Model|boolean
     * @throws UnsentFormException
     */
    public function get(Request $request, Model $model = null)
    {
        $this->initForm([], $model);

        $this->form->handleRequest($request);

        if ($this->isValid($request)) {
            return $this->form->getData();
        }

        return false;
    }

    /**
     * Add a field to the form. The name must be unique,
     * otherwise the field is overwritten.
     * @param string $name
     * @param string $type
     * @param array $options
     */
    protected function add(string $name, string $type, array $options = [])
    {
        // If no label has been set, attempt to automatically translate it
        if (!array_key_exists('label', $options)) {
            $trans = 'fields.' . $name;

            if (\Lang::has($trans)) {
                $options['label'] = $trans;
            }
        }

        $this->form->add($name, $type, $options);
    }

    /**
     * Remove the field with the given name.
     * @param string $name
     * @return FormBuilder
     */
    protected function remove(string $name)
    {
        return $this->form->remove($name);
    }

    /**
     * Return whether a field with the given name exists.
     * @param string $name
     * @return bool
     */
    protected function has(string $name)
    {
        return $this->form->has($name);
    }

    /**
     * Return the underlying FormBuilder instance.
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
     * @return array
     */
    protected function fields()
    {
        return $this->form->all();
    }

    /**
     * Return a field by name.
     * @param string $name
     * @return FormBuilder
     */
    protected function field(string $name)
    {
        return $this->form->get($name);
    }

    /**
     * Return all validation rules in form.
     * @return array
     */
    protected function rules()
    {
        $rules = [];

        foreach ($this->form->all() as $field) {
            if($field->getConfig()->hasOption('rules')) {
                $rules[$field->getName()] = $field->getConfig()->getOption('rules');
            }
        }

        return $rules;
    }

    /**
     * Prepare the form.
     * @param array $options
     * @param Model $model
     */
    protected function initForm(array $options = [], Model $model = null)
    {
        if (is_null($model)) {
            $model = is_array($this->model) ? $this->model : new $this->model;
        }

        $this->form = $this->createForm(FormType::class, $model, $options);
        $this->build();

        $this->isInitialized = true;
    }

    /**
     * Validate the form.
     * @param Request $request
     * @return bool
     * @throws UnsentFormException
     */
    public function isValid(Request $request)
    {
        if ($this->isSubmitted()) {
            if ($this->isDisabled()) return true;

            $this->validateForm($this->form, $request, $this->rules());

            return true;
        }

        throw new UnsentFormException("The form " . get_called_class() . " contains no data.");
    }

    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->form->isSubmitted();
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->form->isDisabled();
    }
}
