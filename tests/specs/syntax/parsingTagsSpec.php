<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing tags", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("with top most tags", function () {

        $this->let("input", function () {
            return
                "div
a
p";
        });

        $this->let("expectedHtml", function () {
            return
                "<div /><a /><p />";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with nested tags", function () {

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

    $this->describe("with nested siblings tags", function () {

        $this->let("input", function () {
            return
                "div
    p
        a
    p
        a";
        });

        $this->let("expectedHtml", function () {
            return
                "<div><p><a /></p><p><a /></p></div>";
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});