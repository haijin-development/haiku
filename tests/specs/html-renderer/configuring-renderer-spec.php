<?php

use Haijin\Haiku\Renderer;
use Haijin\File_Path;

$spec->describe( "When rendering a haiku template file", function() {

    $this->let( "renderer", function() {

        return new Renderer();

    });

    $this->describe( "with default values", function() {

        $this->it( "has a null cache folder", function() {

            $this->expect( $this->renderer->get_cache_folder() ) ->to() ->be() ->null();

        });

        $this->it( "has a null manifest filename", function() {

            $this->expect( $this->renderer->get_manifest_filename() ) ->to() ->be() ->null();

        });

        $this->it( "prints html", function() {

            $this->expect( $this->renderer->is_pretty_html() ) ->to() ->be() ->false();

        });

    });

    $this->describe( "with a given cache folder", function() {

        $this->it( "has the given cache folder", function() {

            $this->renderer->configure( function() {

                $this->cache_folder = "tests/tmp/cache";

            });

            $this->expect( $this->renderer->get_cache_folder() )
                    ->to() ->equal( "tests/tmp/cache" );

        });

        $this->it( "has a default manifest filename", function() {

            $this->renderer->configure( function() {

                $this->cache_folder = "tests/tmp/cache";

            });

            $this->expect( $this->renderer->get_manifest_filename() )
                    ->to() ->equal( "tests/tmp/cache/cached_file_manifest.json" );

        });

    });

    $this->describe( "with a full configuration", function() {

        $this->it( "has the given cache folder", function() {

            $this->renderer->configure( function() {

                $this->cache_folder = "tests/tmp/cache";
                $this->cache_manifest_filename = "tests/tmp/another-folder/manifest";
                $this->pretty_html = true;

            });

            $this->expect( $this->renderer->get_cache_folder() )
                    ->to() ->equal( "tests/tmp/cache" );

            $this->expect( $this->renderer->get_manifest_filename() )
                    ->to() ->equal( "tests/tmp/another-folder/manifest" );

            $this->expect( $this->renderer->is_pretty_html() )
                ->to()->be() ->true();

        });

    });

});