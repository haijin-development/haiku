<?php

use Haijin\Haiku\Renderer;

$spec->describe("Integration test", function () {

    $this->let("renderer", function () {

        return new Renderer();

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
                '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it("renders the input", function () {

            $html = $this->renderer->render($this->input);

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

});