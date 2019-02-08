<?php

use Haijin\Haiku\Renderer;
use Haijin\File_Path;

$spec->describe( "When rendering a haiku template file", function() {

    $this->after_all( function() {

        ( new File_Path( $this->cache_folder ) )->delete();

    });

    $this->let( "cache_folder", function() {

        return __DIR__ . "/../../cache";

    });

    $this->let( "renderer", function() {

        return ( new Renderer() )->configure( function($renderer) {

            $renderer->cache_folder = $this->cache_folder;

        }, $this );

    });

    $this->describe( "without variables", function() {

        $this->let( "input_file", function() {

            return __DIR__ . "/../../samples/sample.haiku.html";

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

            return __DIR__ . "/../../samples/sample-with-variables.haiku.html";

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

            return new File_Path( __DIR__ . "/../../samples/temp-sample.haiku.html" );

        });

        $this->let( "expected_html", function() {
            return
    '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it( "updates the cache", function() {

            $this->input_file->write_contents( "div" );

            $html = $this->renderer->render_file( $this->input_file->to_string() );

            $this->expect( $html ) ->to() ->equal( "<div />" );

            sleep( 1 );

            $this->input_file->write_contents( "div\n\ta" );

            $html = $this->renderer->render_file( $this->input_file->to_string() );

            $this->expect( $html ) ->to() ->equal( "<div><a /></div>" );

        });

    });

});