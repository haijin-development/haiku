<?php

use Haijin\Parser\Parser;
use Haijin\Parser\Errors\Unexpected_Expression_Error;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing an invalid expression", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->let( "input", function() {
        return
"div@invalid-expression
";
    });

    $this->it( "raises an Unexpected_Expression_Error error", function() {

        $this->expect( function() {

            $this->parser->parse_string( $this->input )->to_html();

        }) ->to() ->raise(
            Unexpected_Expression_Error::class,
            function($error) {

                $this->expect( $error->getMessage() ) ->to() ->equal(
                    'Unexpected expression "@invalid-expression". At line: 1 column: 4.'
                );
        });

    });

});