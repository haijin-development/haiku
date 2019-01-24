<?php

\Haijin\Specs\Specs_Runner::configure( function($specs) {

    $this->let( "templates_folder", function() {

        return __DIR__ . "/../templates";

    });

});