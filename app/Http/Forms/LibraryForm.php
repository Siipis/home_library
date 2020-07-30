<?php

namespace App\Http\Forms;


use App\Library;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Illuminate\Validation\Rule;

class LibraryForm extends Form
{
    protected $model = Library::class;

    public function build()
    {
        $this->add('name', TextType::class, [
            'rules' => 'required|string'
        ]);

        $this->add('slug', TextType::class, [
            'rules' => [
                'required', 'slug', 'min:3',
                Rule::unique('libraries')->ignore($this->model()),
            ]
        ]);

        $this->add($this->modelExists() ? 'save' : 'create', SubmitType::class);
    }
}
