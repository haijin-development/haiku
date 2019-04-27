<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("a one liner PHP statement", function () {

        $this->describe("with an ending semicolon", function () {

            $this->let("input", function () {
                return
                    'div
    - $variable = "123";
';
            });

            $this->let("expectedHtml", function () {
                return
                    '<div><?php $variable = "123"; ?></div>';
            });


            $this->it("generates the PHP statement", function () {

                $html = $this->parser->parseString($this->input)->toHtml();

                $this->expect($html)->to()->equal($this->expectedHtml);

            });

        });

        $this->describe("with no ending semicolon", function () {

            $this->let("input", function () {
                return
                    'div
    - $variable = "123"
';
            });

            $this->let("expectedHtml", function () {
                return
                    '<div><?php $variable = "123"; ?></div>';
            });


            $this->it("generates the PHP statement", function () {

                $html = $this->parser->parseString($this->input)->toHtml();

                $this->expect($html)->to()->equal($this->expectedHtml);

            });

        });

    });

    $this->describe("a one liner PHP statement", function () {

        $this->describe("with no ending cr", function () {

            $this->let("input", function () {
                return
                    'div
    - $variable = "123";';
            });

            $this->let("expectedHtml", function () {
                return
                    '<div><?php $variable = "123"; ?></div>';
            });


            $this->it("generates the PHP statement", function () {

                $html = $this->parser->parseString($this->input)->toHtml();

                $this->expect($html)->to()->equal($this->expectedHtml);

            });

        });

    });

    $this->describe("a multiline PHP statements", function () {

        $this->describe("with an ending semicolon", function () {

            $this->let("input", function () {
                return
                    'div
    - {{
        $variable = 1;
        $variable += 10;
    }}
';
            });

            $this->let("expectedHtml", function () {
                return
                    '<div><?php $variable = 1;
        $variable += 10; ?></div>';
            });


            $this->it("generates the PHP statement", function () {

                $html = $this->parser->parseString($this->input)->toHtml();

                $this->expect($html)->to()->equal($this->expectedHtml);

            });

        });

    });

    $this->describe("a mutilines PHP statements", function () {

        $this->describe("with no ending cr", function () {

            $this->let("input", function () {
                return
                    'div
    - {{
        $variable = 1;
        $variable += 10;
    }}';
            });

            $this->let("expectedHtml", function () {
                return
                    '<div><?php $variable = 1;
        $variable += 10; ?></div>';
            });


            $this->it("generates the PHP statement", function () {

                $html = $this->parser->parseString($this->input)->toHtml();

                $this->expect($html)->to()->equal($this->expectedHtml);

            });

        });

    });

});