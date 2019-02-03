<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing tags with jquery shortcuts", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "parses a jquery id", function() {

        $this->let( "haiku", function() {
            return
'div#item-1
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="item-1">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses a implicit div id", function() {

        $this->let( "haiku", function() {
            return
'#item-1
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="item-1">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "id attribute overrides jquery id", function() {

        $this->let( "haiku", function() {
            return
'div#item-1 id = "item-10"
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="item-10">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses a jquery class", function() {

        $this->let( "haiku", function() {
            return
'div.row
';
        });

        $this->let( "expected_html", function() {
            return
'<div class="row">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses an implicity div class", function() {

        $this->let( "haiku", function() {
            return
'.row
';
        });

        $this->let( "expected_html", function() {
            return
'<div class="row">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many jquery classes", function() {

        $this->let( "haiku", function() {
            return
'div.row.item
';
        });

        $this->let( "expected_html", function() {
            return
'<div class="row item">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "parses many implicit div classes", function() {

        $this->let( "haiku", function() {
            return
'.row.item
';
        });

        $this->let( "expected_html", function() {
            return
'<div class="row item">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "attribute classes merges to jquery classes", function() {

        $this->let( "haiku", function() {
            return
'div.row.item class = "format space"
';
        });

        $this->let( "expected_html", function() {
            return
'<div class="row item format space">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with both jquery id and classes", function() {

        $this->let( "haiku", function() {
            return
'div#item-1.row.item
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="item-1" class="row item">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});