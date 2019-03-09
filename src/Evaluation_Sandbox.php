<?php

namespace Haijin\Haiku;

class Evaluation_Sandbox
{
    public function evaluate_file($filename, $variables)
    {
        try {

            extract( $variables );

            ob_start();

            require( $filename );

        } finally {

            return ob_get_clean();

        }
    }

    public function evaluate($php_script, $variables)
    {
        try {

            extract( $variables );

            ob_start();

            eval( "?>\n" . $php_script );

        } finally {

            return ob_get_clean();

        }
    }
}