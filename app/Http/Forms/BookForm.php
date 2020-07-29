<?php

namespace App\Http\Forms;

use App\Book;
use App\Category;
use Illuminate\Validation\Rule;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookForm extends Form
{
    protected $model = Book::class;

    public function build()
    {
        $this->add('category_id', ChoiceType::class, [
            'choices' => Category::all(),
            'choice_value' => 'id',
            'choice_label' => 'name',
            'required' => false,

            'data' => \Request::route()->hasParameter('category') ?
                \Request::route()->parameter('category') : null,

            'rules' => 'exists:categories,id',
        ]);
        $this->add('local_id', TextType::class, [
            'rules' => [
                'string',
                Rule::unique('books')->where(function ($query) {
                    if (isset($this->model()->id)) {
                        $query->where('local_id', '!=', $this->model()->id);
                    }
                })
            ],
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);
        $this->add('isbn', TextType::class, [
            'rules' => 'string',
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);
        $this->add('title', TextType::class, [
            'rules' => 'required|string',
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
        $this->add('series', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('authors', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('publisher', TextType::class, [
            'rules' => 'string',
        ]);
        $this->add('year', TextType::class, [
            'rules' => 'string',
        ]);
        $this->add('description', TextareaType::class, [
            'rules' => 'string',
        ]);
        $this->add('keywords', TextType::class, [
            'rules' => 'string',
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);
        $this->add('cover', TextType::class, [
            'rules' => 'url',
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);
        $this->add('language', TextType::class, [
            'rules' => 'string',
        ]);
        $this->add('providers', HiddenType::class, [
            'rules' => 'json',
        ]);
        $this->add($this->model()->id > 0 ? 'save' : 'create', SubmitType::class);
    }
}
