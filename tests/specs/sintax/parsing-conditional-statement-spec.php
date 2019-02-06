<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing an if statement", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with no spaces after 'do'", function() {

        $this->let( "input", function() {
            return
'- if( $variable == "123" ) do
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?><div /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with spaces after 'do'", function() {

        $this->let( "input", function() {
            return
'- if( $variable == "123" ) do  
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?><div /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with a following else", function() {

        $this->let( "input", function() {
            return
'- if( $variable == "123" ) do
    div
- else do
    p
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?><div /><?php } ?><?php else { ?><p /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with a following elseif", function() {

        $this->let( "input", function() {
            return
'- if( $variable == "123" ) do
    div
- elseif( $variable == "321" ) do
    p
- else do
    a
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?><div /><?php } ?><?php elseif( $variable == "321" ) { ?><p /><?php } ?><?php else { ?><a /><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with no spaces after 'do'", function() {

        $this->let( "input", function() {
            return
'- if( $variable == "123" ) do';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?><?php } ?>';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});