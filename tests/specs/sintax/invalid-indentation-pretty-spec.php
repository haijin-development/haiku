<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;
use Haijin\Haiku\Errors\Not_Unique_Indentation_Char_Error;
use Haijin\Haiku\Errors\Indentation_Char_Missmatch_Error;
use Haijin\Haiku\Errors\Invalid_Indentation_Increment_Error;
use Haijin\Haiku\Errors\Unmatched_Indentation_Error;


$spec->describe( "When parsing indentations", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "that mixes tabs and spaces", function() {

        $this->let( "input", function() {
            return
"div
\t a
";
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input )->to_pretty_html();

            }) ->to() ->raise(
                Not_Unique_Indentation_Char_Error::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: 2 column: 3." );
            });

        });

    });

    $this->describe( "that uses tabs in a line and spaces in another one", function() {

        $this->let( "input", function() {
            return
"div
    a
\ta
";
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input )->to_pretty_html();

            }) ->to() ->raise(
                Indentation_Char_Missmatch_Error::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( "The template is indenting with spaces in one line and tabs in another one, use only tabs or only spaces in all lines. At line: 3 column: 2." );
            });

        });

    });

    $this->describe( "that are not multiple of a indentation unit", function() {

        $this->let( "input", function() {
            return
"div
    a
      p
";
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input )->to_pretty_html();

            }) ->to() ->raise(
                Unmatched_Indentation_Error::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( "The template is using indentation units of 4 spaces, but a line with 6 spaces was found. At line: 3 column: 7." );
            });

        });

    });

    $this->describe( "that are greater than the previous unit + 1", function() {

        $this->let( "input", function() {
            return
"div
    a
            p
";
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input )->to_pretty_html();

            }) ->to() ->raise(
                Invalid_Indentation_Increment_Error::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to()
                        ->equal( "Invalid indentation was found. An increment of only one unit was expected. At line: 3 column: 1." );
            });

        });

    });

});