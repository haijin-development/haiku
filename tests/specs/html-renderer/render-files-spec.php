<?php

use Haijin\Haiku\Renderer;
use Haijin\File_Path;
use Haijin\Errors\File_Not_Found_Error;
use Haijin\Errors\Haijin_Error;

$spec->describe( "When rendering a haiku template file", function() {

    $this->let( "cache_folder", function() {

        return "tests/tmp/cache";

    });

    $this->let( "samples_folder", function() {

        return "tests/samples/";

    });

    $this->let( "renderer", function() {

        return ( new Renderer() )->configure( function($renderer) {

            $renderer->cache_folder = $this->cache_folder;

        });

    });

    $this->describe( "without variables", function() {

        $this->let( "input_file", function() {

            return $this->samples_folder . "sample.haiku.html";

        });

        $this->let( "expected_html", function() {
            return
    '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "renders the input", function() {

            $html = $this->renderer->render_file( $this->input_file );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with variables", function() {

        $this->let( "input_file", function() {

            return $this->samples_folder . "sample-with-variables.haiku.html";

        });

        $this->let( "variables", function() {
            return [
                    "haiku" => [ "Entrar al ciruelo", "en base a olfato", "en base a ternura." ],
                    "author" => "Alberto Silva",
                    "book" => "El libro del haiku"
                ];
        });

        $this->let( "expected_html", function() {
            return
    '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "renders the input", function() {

            $html = $this->renderer->render_file( $this->input_file, $this->variables );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "when it changes", function() {

        $this->let( "input_file", function() {

            return new File_Path( $this->samples_folder . "temp-sample.haiku.html" );

        });

        $this->let( "expected_html", function() {
            return
    '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "updates the cache", function() {

            $this->input_file->write_file_contents( "div" );

            $html = $this->renderer->render_file( $this->input_file->to_string() );

            $this->expect( $html ) ->to() ->equal( "<div />" );

            sleep( 1 );

            $this->input_file->write_file_contents( "div\n\ta" );

            $html = $this->renderer->render_file( $this->input_file->to_string() );

            $this->expect( $html ) ->to() ->equal( "<div><a /></div>" );

        });

    });

    $this->describe( "when the file is absent", function() {

        $this->let( "input_file", function() {

            return $this->samples_folder . "absent-file.haiku.html";

        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->renderer->render_file( $this->input_file );

            }) ->to() ->raise(
                File_Not_Found_Error::class,
                function($error) {

                    $this->expect( $error->getMessage() )
                        ->to() ->equal( "File '{$this->samples_folder}absent-file.haiku.html' not found." );

                    $this->expect( $error->get_filename() )
                        ->to() ->equal( $this->samples_folder . "absent-file.haiku.html" );

            });

        });

    });

    $this->describe( "with an absolute path", function() {

        $this->let( "input_file", function() {

            return __DIR__ . "/../../samples/sample.haiku.html";

        });

        $this->let( "expected_html", function() {
            return
    '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->renderer->render_file( $this->input_file );

            }) ->to() ->raise(
                Haijin_Error::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->match(
                        "/Could not find a suiteable cached named for file '[\/]home[\/].+[\/]samples[\/]sample.haiku.html'./"
                    );

            });

        });

    });

    $this->describe( "with a relative path", function() {

        $this->let( "input_file", function() {

            return "tests/samples/sample.haiku.html";

        });

        $this->let( "expected_html", function() {
            return
    '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "renders the template", function() {

            $html = $this->renderer->render_file( $this->input_file );

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "when the file has php errors", function() {

        $this->let( "renderer", function() {

            return ( new Renderer() )->configure( function($renderer) {

                $renderer->cache_folder = $this->cache_folder;
                $renderer->pretty_html = true;

            });

        });

        $this->let( "input_file", function() {

            return $this->samples_folder . "sample-with-php-errors.haiku.html";

        });

        $this->xit( "raises an error", function() {

            $this->renderer->render_file( $this->input_file );

        });

    });

});