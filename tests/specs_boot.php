<?php

use Haijin\Debugger;
use Haijin\File_Path;

\Haijin\Specs\Specs_Runner::configure( function($specs) {

    $this->before_each( function() {

        ( new File_Path( "tests/tmp" ) )->delete();

    });

    $this->after_all( function() {

        ( new File_Path( "tests/tmp" ) )->delete();

    });

});

function inspect($object)
{
    Debugger::inspect( $object );
}