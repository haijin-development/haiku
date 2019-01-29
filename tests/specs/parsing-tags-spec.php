<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing tags", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with top most tags", function() {

        $this->let( "haiku", function() {
            return
"div
a
p";
        });

        $this->let( "expected_html", function() {
            return
"<div>
</div>
<a>
</a>
<p>
</p>
";
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with nested tags", function() {

        $this->let( "haiku", function() {
            return
"div
    p
        a
";
        });

        $this->let( "expected_html", function() {
            return
"<div>
    <p>
        <a>
        </a>
    </p>
</div>
";
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with nested siblings tags", function() {

        $this->let( "haiku", function() {
            return
"div
    p
        a
    p
        a
";
        });

        $this->let( "expected_html", function() {
            return
"<div>
    <p>
        <a>
        </a>
    </p>
    <p>
        <a>
        </a>
    </p>
</div>
";
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});