<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("When parsing loop statements", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("with no spaces after 'while'", function () {

        $this->let("input", function () {
            return
                '- while( $variable != "123" ) do
    div
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php while( $variable != "123" ) { ?>
    <div />
<?php } ?>
';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with spaces after 'while'", function () {

        $this->let("input", function () {
            return
                '- while( $variable != "123" ) do  
    div
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php while( $variable != "123" ) { ?>
    <div />
<?php } ?>
';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with no spaces after 'foreach'", function () {

        $this->let("input", function () {
            return
                '- foreach( $variables as $key => $value ) do
    div
';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php foreach( $variables as $key => $value ) { ?>
    <div />
<?php } ?>
';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with no ending cr", function () {

        $this->let("input", function () {
            return
                '- while( $variable != "123" ) do';
        });

        $this->let("expectedHtml", function () {
            return
                '<?php while( $variable != "123" ) { ?>
<?php } ?>
';
        });


        $this->it("generates the PHP statement", function () {

            $html = $this->parser->parseString($this->input)->toPrettyHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});