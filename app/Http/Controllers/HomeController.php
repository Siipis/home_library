<?php

namespace App\Http\Controllers;

use App\User;
use Barryvdh\Form\Facade\FormFactory;
use Illuminate\Http\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HomeController extends Controller
{
    public function login(Request $request)
    {
        $user = User::first();

        $form = FormFactory::create(FormType::class, $user)
            ->add('name', TextType::class)
            ->add('password', PasswordType::class);

        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            return redirect('home');
        }

        return view('login', compact('form'));
    }
}
