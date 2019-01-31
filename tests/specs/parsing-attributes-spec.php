<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing tags attributes", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "parses a single attribute with no spaces", function() {

        $this->let( "haiku", function() {
            return
'div id="123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses a single attribute with spaces", function() {

        $this->let( "haiku", function() {
            return
'div id = "123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many attributes with no spaces", function() {

        $this->let( "haiku", function() {
            return
'div id="123",class="row"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" class="row">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many attributes with spaces", function() {

        $this->let( "haiku", function() {
            return
'div id = "123", class = "row"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" class="row">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many attributes with carriage returns", function() {

        $this->let( "haiku", function() {
            return
'div id = "123",
     class = "row"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="123" class="row">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});