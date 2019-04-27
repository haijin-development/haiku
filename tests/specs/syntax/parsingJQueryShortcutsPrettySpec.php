<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing tags with jquery shortcuts", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("parses a jquery id", function () {

        $this->let("input", function () {
            return
                'div#item-1
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="item-1" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses a implicit div id", function () {

        $this->let("input", function () {
            return
                '#item-1
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="item-1" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("id attribute overrides jquery id", function () {

        $this->let("input", function () {
            return
                'div#item-1 id = "item-10"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="item-10" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses a jquery class", function () {

        $this->let("input", function () {
            return
                'div.row
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div class="row" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses an implicity div class", function () {

        $this->let("input", function () {
            return
                '.row
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div class="row" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses many jquery classes", function () {

        $this->let("input", function () {
            return
                'div.row.item
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div class="row item" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("parses many implicit div classes", function () {

        $this->let("input", function () {
            return
                '.row.item
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div class="row item" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("attribute classes merges to jquery classes", function () {

        $this->let("input", function () {
            return
                'div.row.item class = "format space"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div class="row item format space" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with both jquery id and classes", function () {

        $this->let("input", function () {
            return
                'div#item-1.row.item
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="item-1" class="row item" />
';
        });

        $this->it("parses the input", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});