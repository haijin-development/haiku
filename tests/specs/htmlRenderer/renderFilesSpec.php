<?php

use Haijin\Errors\FileNotFoundError;
use Haijin\Errors\HaijinError;
use Haijin\FilePath;
use Haijin\Haiku\Renderer;

$spec->describe("When rendering a haiku template file", function () {

    $this->let("cacheFolder", function () {

        return "tests/tmp/cache";

    });

    $this->let("samplesFolder", function () {

        return "tests/samples/";

    });

    $this->let("renderer", function () {

        return (new Renderer())->configure(function ($renderer) {

            $renderer->cacheFolder = $this->cacheFolder;

        });

    });

    $this->describe("without variables", function () {

        $this->let("inputFile", function () {

            return $this->samplesFolder . "sample.haiku.html";

        });

        $this->let("expectedHtml", function () {
            return
                '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it("renders the input", function () {

            $html = $this->renderer->renderFile($this->inputFile);

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("with variables", function () {

        $this->let("inputFile", function () {

            return $this->samplesFolder . "sampleWithVariables.haiku.html";

        });

        $this->let("variables", function () {
            return [
                "haiku" => ["Entrar al ciruelo", "en base a olfato", "en base a ternura."],
                "author" => "Alberto Silva",
                "book" => "El libro del haiku"
            ];
        });

        $this->let("expectedHtml", function () {
            return
                '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it("renders the input", function () {

            $html = $this->renderer->renderFile($this->inputFile, $this->variables);

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("when it changes", function () {

        $this->let("inputFile", function () {

            return new FilePath($this->samplesFolder . "tempSample.haiku.html");

        });

        $this->let("expectedHtml", function () {
            return
                '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it("updates the cache", function () {

            $this->inputFile->writeFileContents("div");

            $html = $this->renderer->renderFile($this->inputFile->toString());

            $this->expect($html)->to()->equal("<div />");

            sleep(1);

            $this->inputFile->writeFileContents("div\n\ta");

            $html = $this->renderer->renderFile($this->inputFile->toString());

            $this->expect($html)->to()->equal("<div><a /></div>");

        });

    });

    $this->describe("when the file is absent", function () {

        $this->let("inputFile", function () {

            return $this->samplesFolder . "absent-file.haiku.html";

        });

        $this->it("raises an error", function () {

            $this->expect(function () {

                $this->renderer->renderFile($this->inputFile);

            })->to()->raise(
                FileNotFoundError::class,
                function ($error) {

                    $this->expect($error->getMessage())
                        ->to()->equal("File '{$this->samplesFolder}absent-file.haiku.html' not found.");

                    $this->expect($error->getFilename())
                        ->to()->equal($this->samplesFolder . "absent-file.haiku.html");

                });

        });

    });

    $this->describe("with an absolute path", function () {

        $this->let("inputFile", function () {

            return __DIR__ . "/../../samples/sample.haiku.html";

        });

        $this->let("expectedHtml", function () {
            return
                '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it("raises an error", function () {

            $this->expect(function () {

                $this->renderer->renderFile($this->inputFile);

            })->to()->raise(
                HaijinError::class,
                function ($error) {

                    $this->expect($error->getMessage())->to()->match(
                        "/Could not find a suiteable cached named for file '[\/]home[\/].+[\/]samples[\/]sample.haiku.html'./"
                    );

                });

        });

    });

    $this->describe("with a relative path", function () {

        $this->let("inputFile", function () {

            return "tests/samples/sample.haiku.html";

        });

        $this->let("expectedHtml", function () {
            return
                '<html><head /><body><div>Entrar al ciruelo<br />en base a olfato<br />en base a ternura.</div><div>Traducción de Alberto Silva - El libro del haiku</div></body></html>';
        });

        $this->it("renders the template", function () {

            $html = $this->renderer->renderFile($this->inputFile);

            $this->expect($html)->to()->equal($this->expectedHtml);

        });

    });

    $this->describe("when the file has php errors", function () {

        $this->let("renderer", function () {

            return (new Renderer())->configure(function ($renderer) {

                $renderer->cacheFolder = $this->cacheFolder;
                $renderer->prettyHtml = true;

            });

        });

        $this->let("inputFile", function () {

            return $this->samplesFolder . "sample-with-php-errors.haiku.html";

        });

        $this->xit("raises an error", function () {

            $this->renderer->renderFile($this->inputFile);

        });

    });

    $this->describe("when the cache folder was not defined", function () {

        $this->let("renderer", function () {

            return (new Renderer())->configure(function ($renderer) {

                $renderer->cacheFolder = $this->cacheFolder;
                $renderer->cacheFolder = null;

            });

        });

        $this->it("raises an error", function () {

            $this->expect(function () {

                $this->renderer->renderFile("tests/samples/sample.haiku.html");

            })->to()->raise(
                HaijinError::class,
                function ($error) {
                    $this->expect($error->getMessage())->to()->equal(
                        "The cacheFolder is missing. Seems like the Renderer has not been configured. Configure it by calling \$renderer->configure( function(\$confg) {...})."
                    );
                }
            );

        });

    });

    $this->describe("when the manifest file was not defined", function () {

        $this->let("renderer", function () {

            return (new Renderer())->configure(function ($renderer) {

                $renderer->cacheFolder = $this->cacheFolder;
                $renderer->cacheManifestFilename = null;

            });

        });

        $this->it("raises an error", function () {

            $this->expect(function () {

                $this->renderer->renderFile("tests/samples/sample.haiku.html");

            })->to()->raise(
                HaijinError::class,
                function ($error) {
                    $this->expect($error->getMessage())->to()->equal(
                        "The manifest filename is missing. Seems like the Renderer has not been configured. Configure it by calling \$renderer->configure( function(\$confg) {...})."
                    );
                }
            );

        });

    });

    $this->describe("when the cache folder does not exist", function () {

        $this->let("renderer", function () {

            return (new Renderer())->configure(function ($renderer) {

                $renderer->cacheFolder = $this->cacheFolder;

            });

        });

        $this->it("creates it", function () {

            $this->renderer->renderFile("tests/samples/sample.haiku.html");

            $this->expect($this->cacheFolder)->to()->be()->a_folder();
        });

    });

    $this->describe("when the cache manifest file does not exist", function () {

        $this->let("renderer", function () {

            return (new Renderer())->configure(function ($renderer) {

                $renderer->cacheFolder = $this->cacheFolder;
                $renderer->cacheManifestFilename =
                    $this->cacheFolder . '/subfolder/manifest.txt';

            });

        });

        $this->it("creates it", function () {

            $this->renderer->renderFile("tests/samples/sample.haiku.html");

            $this->expect($this->cacheFolder . '/subfolder/manifest.txt')
                ->to()->be()->a_file();
        });

    });

});