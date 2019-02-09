<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;

class Evaluation_Sandbox
{
    static public $php_filename;
    static public $haiku_filename;

    protected $errors;

    public function evaluate($php_script, $variables)
    {
        $this->errors = [];

        extract( $variables );

        ob_start();

        $this_object = $this;

        set_error_handler( function($severity, $message, $filename, $lineno) use($this_object) {

            if( error_reporting() == 0 ) {
                return;
            }

            if( error_reporting() & $severity ) {
                $error = new \stdclass();
                $error->message = $message;
                $error->line_number = $lineno;

                $this_object->collect_error( $error );
            }

        });

        eval( "?>\n" . $php_script );

        return ob_get_clean();
    }

    public function collect_error($error)
    {
        $this->errors[] = $error;
    }

    public function get_evaluation_errors()
    {
        return $this->errors;
    }
}

register_shutdown_function( function() {

    $error = error_get_last();

    if( $error === null ) {
        return;
    }

    $message = "\n\nHaiku evaluation error: " .
                    preg_replace( "/Stack trace.*/s", "", $error[ "message" ] ) .
                    "\n\n";

    $php_filename = Evaluation_Sandbox::$php_filename;
    $haiku_filename = Evaluation_Sandbox::$haiku_filename;

    if( Evaluation_Sandbox::$php_filename !== null ) {
        $message .= "in file {$php_filename}\n";
    }

    if( Evaluation_Sandbox::$haiku_filename !== null ) {
        $message .= "generated from haiku template {$haiku_filename}\n";
    }

    $message .= " at line {$error[ "line" ]}.\n";

    echo $message;
});
