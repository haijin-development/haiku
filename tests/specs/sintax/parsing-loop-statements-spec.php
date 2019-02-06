<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing loop statements", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with no spaces after 'while'", function() {

        $this->let( "input", function() {
            return
'- while( $variable != "123" ) do
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php while( $variable != "123" ) { ?><div /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with spaces after 'while'", function() {

        $this->let( "input", function() {
            return
'- while( $variable != "123" ) do  
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php while( $variable != "123" ) { ?><div /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with no spaces after 'foreach'", function() {

        $this->let( "input", function() {
            return
'- foreach( $variables as $key => $value ) do
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php foreach( $variables as $key => $value ) { ?><div /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with no ending cr", function() {

        $this->let( "input", function() {
            return
'- while( $variable != "123" ) do';
        });

        $this->let( "expected_html", function() {
            return
'<?php while( $variable != "123" ) { ?><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});