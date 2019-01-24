<?php

use Haijin\Haiku\Parser;

$spec->describe( "Integration test", function() {

    $this->let( "parser", function() {

        return new Parser();

    });

    $this->describe( "with an html document", function() {

        $this->let( "haiku", function() {
            return
'html
    head
    body
        div
            = "Entrar al ciruelo"
            br
            = "en base a ternura"
            br
            = "en base a olfato."
        div
            = "Traducción de Alberto Silva - El libro del haiku"
';
        });

        $this->let( "expected_html", function() {
            return
'<html>
    <head>
    </head>
    <body>
        <div>
            <?php echo htmlspecialchars( "Entrar al ciruelo" ); ?>
            <br>
            </br>
            <?php echo htmlspecialchars( "en base a ternura" ); ?>
            <br>
            </br>
            <?php echo htmlspecialchars( "en base a olfato." ); ?>
        </div>
        <div>
            <?php echo htmlspecialchars( "Traducción de Alberto Silva - El libro del haiku" ); ?>
        </div>
    </body>
</html>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});