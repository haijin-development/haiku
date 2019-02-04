<?php

namespace Haijin\Haiku;

class Haiku_PHP_Echoed_Expression extends Haiku_Node
{
    protected $expression;
    protected $escaped;

    public function __construct($expression = "", $escaped = true)
    {
        parent::__construct();

        if( $expression[ strlen( $expression ) - 1 ] != ";" ) {
            $this->expression = $expression;
        } else {
            $this->expression = \substr( $expression, 0, strlen( $expression ) - 1 );
        }


        $this->escaped = $escaped;
    }

    public function to_html($indentation)
    {
        if( $this->escaped ) {

            return $this->indent( $indentation ) .
                "<?php echo htmlspecialchars( {$this->expression} ); ?>";

        } else {

            return $this->indent( $indentation ) .
                "<?php echo {$this->expression}; ?>";

        }
    }
}