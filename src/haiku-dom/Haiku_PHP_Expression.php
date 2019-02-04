<?php

namespace Haijin\Haiku;

class Haiku_PHP_Expression extends Haiku_Node
{
    protected $expression;

    public function __construct($expression = "")
    {
        parent::__construct();

        $this->expression = $expression;
    }

    public function to_html($indentation)
    {
        $ending_semicolon = $this->expression[ strlen( $this->expression) - 1 ] == ";" ?
            "" : ";";

        return $this->indent( $indentation ) .
            "<?php {$this->expression}{$ending_semicolon} ?>";
    }
}