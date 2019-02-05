<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Parser\Parser;

class Renderer
{
    /// Rendering

    public function render($input)
    {
        $php_script = $this->new_parser()->parse_string( $input )->to_html();

        return $this->evaluate_php_script( $php_script );
    }


    protected function evaluate_php_script($php_script)
    {
        try {

            ob_start();

            eval( "?>\n" . $php_script );

            return ob_get_contents();

        } finally {

            ob_clean();

        }
    }

    /// Creating instances

    protected function new_parser()
    {
        return Create::a( Parser::class )->with( Haiku_Parser_Definition::$definition );
    }
}