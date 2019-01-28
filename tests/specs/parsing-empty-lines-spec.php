<?php

use Haijin\Haiku\Haiku_Parser;

$spec->xdescribe( "When parsing empty lines", function() {

    $this->let( "parser", function() {

        return new Haiku_Parser();

    });

    $this->describe( "with just a cr", function() {

        $this->let( "haiku", function() {
            return
"div

p
";
        });

        $this->let( "expected_html", function() {
            return
"<div>
</div>
<p>
</p>
";
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with spaces", function() {

        $this->let( "haiku", function() {
            return
"div
    
p
";
        });

        $this->let( "expected_html", function() {
            return
"<div>
</div>
<p>
</p>
";
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with tabs", function() {

        $this->let( "haiku", function() {
            return
"div
\t\t\t
p
";
        });

        $this->let( "expected_html", function() {
            return
"<div>
</div>
<p>
</p>
";
        });

        $this->it( "parses the haiku", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "in between nested tags", function() {

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

});