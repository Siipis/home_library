<?php


namespace App\Http\Forms;


use App\Http\Forms\Exceptions\AbstractFormException;
use App\Http\Forms\Exceptions\FormException;
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
     * @var Request
     */
    protected $request;

    /**
     * @var Model
     */
    protected static $model;

    /**
     * @var FormBuilder
     */
    protected $form;

    /**
     * Form constructor.
     * @param Request $request
     * @param array $options
     * @param Model|null $model
     * @throws AbstractFormException
     */
    public function __construct(Request $request, array $options = [], Model $model = null)
    {
        if (is_null($this::$model)) {
            throw new AbstractFormException("The class attribute 'model' is required.");
        }

        if (is_null($model)) {
            $class = $this::$model;
            $model = new $class;
        }

        $this->request = $request;
        $this->form = $this->createForm(FormType::class, $model, $options);

        $this->build();
    }

    /**
     * Prepare the form for rendering.
     * @return void
     */
    public function build()
    {
        // Add no fields by default
    }

    /**
     * Render the form.
     * @return FormBuilder
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * Return the form.
     * @param Request $request
     * @param array $options
     * @param Model|null $model
     * @return FormBuilder
     */
    public static function get(Request $request, array $options = [], Model $model = null)
    {
        $self = self::getInstance($request, $options, $model);

        return $self->form();
    }

    /**
     * Handle the form request.
     * @param Request $request
     * @param Model|null $model
     * @return Model|boolean
     * @throws FormException
     */
    public static function handle(Request $request, Model $model = null)
    {
        $self = self::getInstance($request, [], $model);

        if ($self instanceof self) {
            $form = $self->form();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    return $model;
                }

                return false;
            }
        }

        throw new UnsentFormException("The form " . get_called_class() . " contains no data.");
    }

    /**
     * Return a response with errors attached.
     * @param Request $request
     * @param Model|null $model
     * @return bool|RedirectResponse
     * @throws UnsentFormException
     */
    public static function back(Request $request, Model $model = null)
    {
        $self = self::getInstance($request, [], $model);

        if ($self instanceof self) {
            $form = $self->form();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    return false;
                }

                $messageBag = new MessageBag();
                $errors = $form->getErrors(true);

                foreach ($errors as $error) {
                    $messageBag->add($error->getOrigin()->getName(), $error->getMessage());
                }

                return redirect()->back()->withErrors($messageBag);
            }
        }

        throw new UnsentFormException("The form " . get_called_class() . " contains no data.");
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
     * Return an instance of the called class.
     * @param Request $request
     * @param array $options
     * @param Model|null $model
     * @return mixed
     */
    private static function getInstance(Request $request, array $options = [], Model $model = null)
    {
        $class = get_called_class();
        return new $class($request, $options, $model);
    }
}
