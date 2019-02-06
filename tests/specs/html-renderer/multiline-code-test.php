<?php

use Haijin\Haiku\Renderer;

$spec->describe( "When rendering a haiku template with a multiline statement", function() {

    $this->let( "renderer", function() {

        return new Renderer();

    });

    $this->describe( "with an html document", function() {

        $this->let( "input", function() {
            return
'html
    head
    body
        - ({
                $haiku  = [ "Entrar al ciruelo", "en base a olfato", "en base a ternura." ];
                $author = "Alberto Silva";
                $book   = "El libro del haiku";

            })
        div
            = $haiku[ 0 ]
            br
            = $haiku[ 1 ]
            br
            = $haiku[ 2 ]
        div
            = "Traducción de {$author} - {$book}"
';
        });

        $this->let( "expected_html", function() {
            return
'<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "renders the input", function() {

            $html = $this->renderer->render( $this->input );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});