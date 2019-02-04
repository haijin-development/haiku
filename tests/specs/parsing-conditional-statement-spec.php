<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing an if statement", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with no spaces after 'do'", function() {

        $this->let( "haiku", function() {
            return
'- if( $variable == "123" ) do
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?>
    <div>
    </div>
<?php } ?>
';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with spaces after 'do'", function() {

        $this->let( "haiku", function() {
            return
'- if( $variable == "123" ) do  
    div
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?>
    <div>
    </div>
<?php } ?>
';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with a following else", function() {

        $this->let( "haiku", function() {
            return
'- if( $variable == "123" ) do
    div
- else do
    p
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?>
    <div>
    </div>
<?php } ?>
<?php else { ?>
    <p>
    </p>
<?php } ?>
';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with a following elseif", function() {

        $this->let( "haiku", function() {
            return
'- if( $variable == "123" ) do
    div
- elseif( $variable == "321" ) do
    p
- else do
    a
';
        });

        $this->let( "expected_html", function() {
            return
'<?php if( $variable == "123" ) { ?>
    <div>
    </div>
<?php } ?>
<?php elseif( $variable == "321" ) { ?>
    <p>
    </p>
<?php } ?>
<?php else { ?>
    <a>
    </a>
<?php } ?>
';
        });


        $this->it( "generates the PHP statement", function() {

            $html = $this->parser->parse_string( $this->haiku );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});