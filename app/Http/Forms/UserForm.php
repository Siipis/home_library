<?php

namespace App\Http\Forms;

use App\User;
use Illuminate\Validation\Rule;
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
            'rules' => [
                'required', 'email',
                Rule::unique('users')->ignore($this->model()),
            ]
        ]);
        $this->add('password', PasswordType::class, [
            'rules' => $this->getPasswordRules(),
            'label' => trans('fields.' . ($this->modelExists() ? 'password_change' : 'password')),
            'empty_data' => $this->model()->password,
        ]);
        $this->add('password_confirmation', PasswordType::class, [
            'rules' => 'required_with:password',
        ]);
        $this->add($this->modelExists() ? 'save' : 'create', SubmitType::class);
    }

    /**
     * @return string[]
     */
    private function getPasswordRules()
    {
        $rules = [
            'confirmed',
        ];

        if ($this->modelIsNew()) {
            array_push($rules, 'required');
        }

        return $rules;
    }
}
