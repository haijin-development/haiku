<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->xdescribe( "When parsing tags attributes", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "parses a single attribute", function() {

        $this->let( "haiku", function() {
            return
"div{ id: 123 }
";
        });

        $this->let( "expected_html", function() {
            return
'<div id="123">
</div>
';
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});