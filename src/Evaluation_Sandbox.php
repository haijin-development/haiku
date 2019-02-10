<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;

class Evaluation_Sandbox
{
    public function evaluate_file($filename, $variables)
    {
        extract( $variables );

        ob_start();

        $this_object = $this;

        require( $filename );

        return ob_get_clean();
    }

    public function evaluate($php_script, $variables)
    {
        extract( $variables );

        ob_start();

        $this_object = $this;

        eval( "?>\n" . $php_script );

        return ob_get_clean();
    }
}