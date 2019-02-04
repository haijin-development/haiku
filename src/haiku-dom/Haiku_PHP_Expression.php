<?php

namespace Haijin\Haiku;

class Haiku_PHP_Expression extends Haiku_Node
{
    protected $expression;
    protected $multiline;

    public function __construct($expression = "", $is_multiline = false)
    {
        parent::__construct();

        $this->expression = $expression;
        $this->is_multiline = $is_multiline;
    }

    public function to_html($indentation)
    {
        $ending_semicolon = $this->expression[ strlen( $this->expression) - 1 ] == ";" ?
            "" : ";";

        $html = $this->indent( $indentation ) .
            "<?php {$this->expression}{$ending_semicolon} ?>";

        return $html;
    }
}