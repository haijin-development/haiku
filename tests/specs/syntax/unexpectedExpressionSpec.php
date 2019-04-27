<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Errors\UnexpectedExpressionError;
use Haijin\Parser\Parser;

$spec->describe("When parsing an invalid expression", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->let("input", function () {
        return
            "div@invalid-expression
";
    });

    $this->it("raises an UnexpectedExpressionError error", function () {

        $this->expect(function () {

            $this->parser->parseString($this->input)->toHtml();

        })->to()->raise(
            UnexpectedExpressionError::class,

            function ($error) {

                $this->expect($error->getMessage())->to()->equal(
                    'Unexpected expression "@invalid-expression". At line: 1 column: 4.'
                );
            }
        );

    });

});