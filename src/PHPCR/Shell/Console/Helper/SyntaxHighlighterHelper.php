<?php

namespace PHPCR\Shell\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

class SyntaxHighlighterHelper extends Helper
{
    public function getName()
    {
        return 'syntax_highlighter';
    }

    public function highlightXml($string)
    {
        $highlighter = new \FSHL\Highlighter(new \FSHL\Output\SymfonyConsole());
        $highlighter->setLexer(new \FSHL\Lexer\Html());
        return $highlighter->highlight($string);
    }
}
