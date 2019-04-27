<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing empty lines", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("with just a cr", function () {

        $this->let("input", function () {
            return
                "div

p";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with just a cr at the end of stream", function () {

        $this->let("input", function () {
            return
                "div
p
";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with spaces", function () {

        $this->let("input", function () {
            return
                "div
    
p";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with spaces at the end of the stream", function () {

        $this->let("input", function () {
            return
                "div
p
   ";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with tabs", function () {

        $this->let("input", function () {
            return
                "div
\t\t\t
p";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with tabs at the end of the stream", function () {

        $this->let("input", function () {
            return
                "div
p
\t\t\t";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("in between nested tags", function () {

        $this->let("input", function () {
            return
                "div

    p

        a";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><p><a /></p></div>";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});