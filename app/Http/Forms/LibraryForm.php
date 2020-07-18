<?php


namespace App\Http\Forms;



use App\Library;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LibraryForm extends Form
{
    protected static $model = Library::class;

    public function build()
    {
        $this->add('name', TextType::class, [
            'rules' => 'required|string'
        ]);
        $this->add('slug', TextType::class, [
            'rules' => 'required|slug|min:3'
        ]);
        $this->add('create', SubmitType::class);
    }
}
