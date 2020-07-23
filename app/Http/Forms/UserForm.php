<?php

namespace App\Http\Forms;

use App\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserForm extends Form
{
    protected $model = User::class;

    public function build()
    {
        $this->add('name', TextType::class, [
            'rules' => 'required|string'
        ]);
        $this->add('email', EmailType::class, [
            'rules' => 'required|email'
        ]);
        $this->add('password', PasswordType::class, [
            'rules' => 'required|confirmed',
        ]);
        $this->add('password_confirmation', PasswordType::class, [
            'rules' => 'required',
        ]);
        $this->add('create', SubmitType::class);
    }
}
