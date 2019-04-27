<?php

namespace Haijin\Haiku\Dom;

class HaikuPHPExpression extends HaikuNode
{
    protected $expression;

    public function __construct($expression = "")
    {
        parent::__construct();

        $this->expression = $expression;
    }

    public function toHtml($indentation)
    {
        $endingSemicolon = $this->expression[strlen($this->expression) - 1] == ";" ?
            "" : ";";

        return "<?php {$this->expression}{$endingSemicolon} ?>";
    }

    public function toPrettyHtml($indentation)
    {
        $endingSemicolon = $this->expression[strlen($this->expression) - 1] == ";" ?
            "" : ";";

        return $this->indent($indentation) .
            "<?php {$this->expression}{$endingSemicolon} ?>";

    }
}