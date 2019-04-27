<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing an if statement", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("with no spaces after 'do'", function () {

        $this->let("input", function () {
            return
                '- if( $variable == "123" ) do
    div
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php if( $variable == "123" ) { ?><div /><?php } ?>';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with spaces after 'do'", function () {

        $this->let("input", function () {
            return
                '- if( $variable == "123" ) do  
    div
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php if( $variable == "123" ) { ?><div /><?php } ?>';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with a following else", function () {

        $this->let("input", function () {
            return
                '- if( $variable == "123" ) do
    div
- else do
    p
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php if( $variable == "123" ) { ?><div /><?php } else { ?><p /><?php } ?>';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with a following elseif", function () {

        $this->let("input", function () {
            return
                '- if( $variable == "123" ) do
    div
- elseif( $variable == "321" ) do
    p
- else do
    a
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php if( $variable == "123" ) { ?><div /><?php } elseif( $variable == "321" ) { ?><p /><?php } else { ?><a /><?php } ?>';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with no spaces after 'do'", function () {

        $this->let("input", function () {
            return
                '- if( $variable == "123" ) do';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php if( $variable == "123" ) { ?><?php } ?>';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});