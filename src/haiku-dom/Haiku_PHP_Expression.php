<?php

namespace Haijin\Haiku;

class Haiku_PHP_Expression extends Haiku_Node
{
    protected $expression;

    public function __construct($expression = null)
    {
        parent::__construct();

        $this->expression = $expression;
    }

    public function to_html($indentation)
    {
        $html = $this->indent( $indentation ) . "<?php echo htmlspecialchars( {$this->expression} ); ?>";

        return $html;
    }
}