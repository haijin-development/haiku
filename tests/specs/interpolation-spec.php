<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When interpolating PHP statements", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "in a quoted string", function() {

        $this->let( "haiku", function() {
            return
'div data-id = "123{{"3 + 4"}}321"
';
        });

        $this->let( "expected_html", function() {
            return
'<div data-id="123<?php echo htmlspecialchars("3 + 4"); ?>321">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});