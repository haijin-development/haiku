<?php

use Haijin\Haiku\HaikuParserDefinition;
use Haijin\Parser\Parser;

$spec->describe("Integration test", function () {

    $this->let("parser", function () {

        return new Parser(HaikuParserDefinition::$definition);

    });

    $this->describe("with an html document", function () {

        $this->let("input", function () {
            return
                'html
    head
    body
        div
            = "Entrar al ciruelo"
            br
            = "en base a olfato"
            br
            = "en base a ternura."
        div
            = "Traducción de Alberto Silva - El libro del haiku"
';
        });

        $this->let("expectedHtml", function () {
            return
                '<html><head /><body><div><?php echo htmlspecialchars( "Entrar al ciruelo" ); ?><br /><?php echo htmlspecialchars( "en base a olfato" ); ?><br /><?php echo htmlspecialchars( "en base a ternura." ); ?></div><div><?php echo htmlspecialchars( "Traducción de Alberto Silva - El libro del haiku" ); ?></div></body></html>';
        });

        $this->it("parses the haiku", function () {

            $html = $this->parser->parseString($this->input)->toHtml();

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});