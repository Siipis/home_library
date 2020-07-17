<?php


namespace App\Twig\Node;


use Twig\Compiler;
use Twig\Node\Node;

class TokenNode extends Node
{
    public function __construct(int $lineno = 0, string $tag = null)
    {
        parent::__construct([], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("echo '<input type=\"hidden\" name=\"_token\" value=\"'. csrf_token() .'\" />'")
            ->raw(";\n")
        ;
    }
}
