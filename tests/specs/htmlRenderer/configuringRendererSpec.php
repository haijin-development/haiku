<?php

use Haijin\Haiku\Renderer;

$spec->describe("When rendering a haiku template file", function () {

    $this->let("renderer", function () {

        return new Renderer();

    });

    $this->describe("with default values", function () {

        $this->it("has a null cache folder", function () {

            $this->expect($this->renderer->getCacheFolder())->to()->be()->null();

        });

        $this->it("has a null manifest filename", function () {

            $this->expect($this->renderer->getCacheManifestFilename())->to()
                ->be()->null();

        });

        $this->it("prints html", function () {

            $this->expect($this->renderer->isPrettyHtml())->to()->be()->false();

        });

    });

    $this->describe("with a given cache folder", function () {

        $this->it("has the given cache folder", function () {

            $this->renderer->configure(function ($renderer) {

                $renderer->cacheFolder = "tests/tmp/cache";

            });

            $this->expect($this->renderer->getCacheFolder())
                ->to()->equal("tests/tmp/cache");

        });

        $this->it("has a default manifest filename", function () {

            $this->renderer->configure(function ($renderer) {

                $renderer->cacheFolder = "tests/tmp/cache";

            });

            $this->expect($this->renderer->getCacheManifestFilename()->toString())
                ->to()->match(
                    "/^.+tests[\/]tmp[\/]cache[\/]cachedFileManifest.json$/"
                );

        });

    });

    $this->describe("with a full configuration", function () {

        $this->it("has the given cache folder", function () {

            $this->renderer->configure(function ($renderer) {

                $renderer->cacheFolder = "tests/tmp/cache";
                $renderer->cacheManifestFilename = "tests/tmp/another-folder/manifest";
                $renderer->prettyHtml = true;

            });

            $this->expect($this->renderer->getCacheFolder())
                ->to()->equal("tests/tmp/cache");

            $this->expect($this->renderer->getCacheManifestFilename())
                ->to()->equal("tests/tmp/another-folder/manifest");

            $this->expect($this->renderer->isPrettyHtml())
                ->to()->be()->true();

        });

    });

    $this->describe("while configuring it", function () {

        $this->it("reads the values", function () {

            $this->renderer->configure(function ($renderer) {

                $renderer->cacheFolder = "tests/tmp/cache";
                $renderer->cacheManifestFilename = "tests/tmp/another-folder/manifest";
                $renderer->prettyHtml = true;

                $this->cacheFolder = $renderer->cacheFolder;

                $this->cacheManifest = $renderer->cacheManifestFilename;

                $this->prettyHtml = $renderer->prettyHtml;

            });

            $this->expect($this->cacheFolder)
                ->to()->equal("tests/tmp/cache");

            $this->expect($this->cacheManifest)
                ->to()->equal("tests/tmp/another-folder/manifest");

            $this->expect($this->prettyHtml)
                ->to()->be()->true();

        });

    });

});