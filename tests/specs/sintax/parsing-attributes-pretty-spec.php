<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing tags attributes", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "parses a single attribute with no spaces", function() {

        $this->let( "input", function() {
            return
'div id="123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses a single attribute with spaces", function() {

        $this->let( "input", function() {
            return
'div id = "123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many attributes with no spaces", function() {

        $this->let( "input", function() {
            return
'div id="123",class="row"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" class="row" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many attributes with spaces", function() {

        $this->let( "input", function() {
            return
'div id = "123", class = "row"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" class="row" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many attributes with carriage returns", function() {

        $this->let( "input", function() {
            return
'div id = "123",
     class = "row"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" class="row" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses attribute value with many chars", function() {

        $this->let( "input", function() {
            return
'div id = "12.3 \'abc\' @()"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="12.3 \'abc\' @()" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses attribute value with double quotes", function() {

        $this->let( "input", function() {
            return
'div id = "123\"321"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123&quot;321" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "escapes html tags in attribute value", function() {

        $this->let( "input", function() {
            return
'div id = "<>\""
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="&lt;&gt;&quot;" />
';
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});