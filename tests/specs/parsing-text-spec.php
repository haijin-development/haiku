<?php

use Haijin\Haiku\Parser;

$spec->describe( "When parsing text", function() {

    $this->let( "parser", function() {

        return new Parser();

    });

    $this->let( "haiku", function() {
        return
"div
    = '123'
";
    });

    $this->let( "expected_html", function() {
        return
"<div>
    <?php echo htmlspecialchars( '123' ); ?>
</div>
";
    });

    $this->it( "parses the text", function() {

        $html = $this->parser->parse_string( $this->haiku );

        $this->expect( $html ) ->to() ->equal( $this->expected_html );

    });

});