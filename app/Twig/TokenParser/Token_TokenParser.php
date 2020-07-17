<?php


namespace App\Twig\TokenParser;


use App\Twig\Node\TokenNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class Token_TokenParser extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $stream->expect(Token::BLOCK_END_TYPE);

        return new TokenNode($token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'token';
    }
}
