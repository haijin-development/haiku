<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing a one liner PHP statement", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with an ending semicolon", function() {

        $this->let( "haiku", function() {
            return
'div
    - $variable = "123";
';
        });

        $this->let( "expected_html", function() {
            return
'<div>
    <?php $variable = "123"; ?>
</div>
';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with no ending semicolon", function() {

        $this->let( "haiku", function() {
            return
'div
    - $variable = "123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div>
    <?php $variable = "123"; ?>
</div>
';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});