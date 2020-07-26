<?php

namespace App\Http\Forms;

use App\Category;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryForm extends Form
{
    protected $model = Category::class;

    public function build()
    {
        $this->add('name', TextType::class, [
            'rules' => 'required|string|max:20'
        ]);
        $this->add('color', ColorType::class, [
            'rules' => 'required|string'
        ]);
        $this->add($this->model()->id > 0 ? 'save' : 'create', SubmitType::class);
    }
}
