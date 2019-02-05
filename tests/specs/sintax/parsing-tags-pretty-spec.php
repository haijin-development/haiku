<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing tags", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with top most tags", function() {

        $this->let( "input", function() {
            return
"div
a
p";
        });

        $this->let( "expected_html", function() {
            return
"<div />
<a />
<p />
";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with nested tags", function() {

        $this->let( "input", function() {
            return
"div
    p
        a";
        });

        $this->let( "expected_html", function() {
            return
"<div>
    <p>
        <a />
    </p>
</div>
";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with nested siblings tags", function() {

        $this->let( "input", function() {
            return
"div
    p
        a
    p
        a";
        });

        $this->let( "expected_html", function() {
            return
"<div>
    <p>
        <a />
    </p>
    <p>
        <a />
    </p>
</div>
";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_pretty_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});