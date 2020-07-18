<?php


namespace App\Http\Forms;


use App\Http\Forms\Exceptions\AbstractFormException;
use App\Http\Forms\Exceptions\UnsentFormException;
use Barryvdh\Form\CreatesForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form as FormBuilder;

class Form
{
    use CreatesForms;

    /**
     * @var FormBuilder
     */
    protected $form;

    /**
     * @var MessageBag
     */
    private $messageBag;

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

        $this->messageBag = new MessageBag();
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
    public function handle(Request $request, Model $model = null)
    {
        $this->initForm([], $model);

        $this->form->handleRequest($request);

        if ($this->form->isSubmitted()) {
            if ($this->form->isValid()) {
                return $this->form->getData();
            }

            $errors = $this->form->getErrors(true);

            foreach ($errors as $error) {
                $this->messageBag->add($error->getOrigin()->getName(), $error->getMessage());
            }

            return false;
        }

        throw new UnsentFormException("The form " . get_called_class() . " contains no data.");
    }

    /**
     * Return a response with the errors attached.
     * @return RedirectResponse
     */
    public function back()
    {
        return redirect()->back()->withErrors($this->messageBag);
    }

    /**
     * Return the validation errors.
     * @return MessageBag
     */
    public function errors()
    {
        return $this->messageBag;
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
     * Prepare the form.
     * @param array $options
     * @param Model $model
     */
    protected function initForm(array $options = [], Model $model = null)
    {
        if (is_null($model)) {
            $model = new $this->model;
        }

        $this->form = $this->createForm(FormType::class, $model, $options);
        $this->build();

        $this->isInitialized = true;
    }
}
