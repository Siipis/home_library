<?php

namespace App\Http\Forms;

use App\Book;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookForm extends Form
{
    protected $model = Book::class;

    public function build()
    {
        $this->add('title', TextType::class, [
            'rules' => 'required|string'
        ]);
        $this->add('authors', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('publisher', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('description', TextareaType::class, [
            'rules' => 'string'
        ]);
        $this->add('year', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('isbn', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('language', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add('keywords', TextType::class, [
            'rules' => 'string'
        ]);
        $this->add($this->model()->id > 0 ? 'save' : 'create', SubmitType::class);
    }
}
