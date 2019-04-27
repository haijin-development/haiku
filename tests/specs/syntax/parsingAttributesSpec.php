<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing tags attributes", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("parses a single attribute with no spaces", function () {

        $this->let("input", function () {
            return
                'div id="123"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="123" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses a single attribute with spaces", function () {

        $this->let("input", function () {
            return
                'div id = "123"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="123" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses many attributes with no spaces", function () {

        $this->let("input", function () {
            return
                'div id="123",class="row"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="123" class="row" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses many attributes with spaces", function () {

        $this->let("input", function () {
            return
                'div id = "123", class = "row"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="123" class="row" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses many attributes with carriage returns", function () {

        $this->let("input", function () {
            return
                'div id = "123",
     class = "row"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="123" class="row" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses attribute value with many chars", function () {

        $this->let("input", function () {
            return
                'div id = "12.3 \'abc\' @()"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="12.3 \'abc\' @()" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses attribute value with double quotes", function () {

        $this->let("input", function () {
            return
                'div id = "123\"321"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="123&quot;321" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("escapes html tags in attribute value", function () {

        $this->let("input", function () {
            return
                'div id = "<>\""
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="&lt;&gt;&quot;" />';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});