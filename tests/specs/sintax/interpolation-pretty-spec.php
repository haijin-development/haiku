<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When interpolating PHP statements", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "in a quoted string", function() {

        $this->let( "input", function() {
            return
'div data-id = "123({ "3 + 4" })321"
';
        });

        $this->let( "expected_html", function() {
            return
'<div data-id="123<?php echo htmlspecialchars( "3 + 4" ); ?>321" />
';
        });

        $this->it( "interpolates the PHP expression", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "at the beginning of an attribute name", function() {

        $this->let( "input", function() {
            return
'div ({ "id" })-data = "123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div <?php echo htmlspecialchars( "id" ); ?>-data="123" />
';
        });

        $this->it( "interpolates the PHP expression", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "in the middle of an attribute name", function() {

        $this->let( "input", function() {
            return
'div item-({ "id" })-data = "123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div item-<?php echo htmlspecialchars( "id" ); ?>-data="123" />
';
        });

        $this->it( "interpolates the PHP expression", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "at the end of an attribute name", function() {

        $this->let( "input", function() {
            return
'div data-({ "id" }) = "123"
';
        });

        $this->let( "expected_html", function() {
            return
'<div data-<?php echo htmlspecialchars( "id" ); ?>="123" />
';
        });

        $this->it( "interpolates the PHP expression", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "in an jquery id", function() {

        $this->let( "input", function() {
            return
'div#item-({ $id })
';
        });

        $this->let( "expected_html", function() {
            return
'<div id="item-<?php echo htmlspecialchars( $id ); ?>" />
';
        });

        $this->it( "interpolates the PHP expression", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "in an jquery class", function() {

        $this->let( "input", function() {
            return
'div.item-({ $id }).data-({ "1" })
';
        });

        $this->let( "expected_html", function() {
            return
'<div class="item-<?php echo htmlspecialchars( $id ); ?> data-<?php echo htmlspecialchars( "1" ); ?>" />
';
        });

        $this->it( "interpolates the PHP expression", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});