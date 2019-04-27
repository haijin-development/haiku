<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When interpolating PHP statements", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("at the beginning of an attribute name", function () {

        $this->let("input", function () {
            return
                'div {{ "id" }}-data = "123"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div <?php echo htmlspecialchars( "id" ); ?>-data="123" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("in the middle of an attribute name", function () {

        $this->let("input", function () {
            return
                'div item-{{ "id" }}-data = "123"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div item-<?php echo htmlspecialchars( "id" ); ?>-data="123" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("at the end of an attribute name", function () {

        $this->let("input", function () {
            return
                'div data-{{ "id" }} = "123"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div data-<?php echo htmlspecialchars( "id" ); ?>="123" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("as a whole attribute name", function () {

        $this->let("input", function () {
            return
                'div {{ "id" }} = "123"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div <?php echo htmlspecialchars( "id" ); ?>="123" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("at the beginning of an attribute value", function () {

        $this->let("input", function () {
            return
                'div data-id = "{{ "3 + 4" }}321"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div data-id="<?php echo htmlspecialchars( "3 + 4" ); ?>321" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("in the middle of an attribute value", function () {

        $this->let("input", function () {
            return
                'div data-id = "123{{ "3 + 4" }}321"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div data-id="123<?php echo htmlspecialchars( "3 + 4" ); ?>321" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("at the end of an attribute value", function () {

        $this->let("input", function () {
            return
                'div data-id = "123{{ "3 + 4" }}"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div data-id="123<?php echo htmlspecialchars( "3 + 4" ); ?>" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("as a whole attribute value", function () {

        $this->let("input", function () {
            return
                'div data-id = "{{ "3 + 4" }}"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div data-id="<?php echo htmlspecialchars( "3 + 4" ); ?>" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("in an jquery id", function () {

        $this->let("input", function () {
            return
                'div#item-{{ $id }}
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div id="item-<?php echo htmlspecialchars( $id ); ?>" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("in an jquery class", function () {

        $this->let("input", function () {
            return
                'div.item-{{ $id }}.data-{{ "1" }}
';
        });

        $this->let("expectedHtml", function () {
            return
                '<div class="item-<?php echo htmlspecialchars( $id ); ?> data-<?php echo htmlspecialchars( "1" ); ?>" />';
        });

        $this->it("interpolates the PHP expression", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});