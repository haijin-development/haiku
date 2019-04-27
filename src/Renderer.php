<?php

namespace Haijin\Haiku;

use Haijin\Errors\FileNotFoundError;
use Haijin\Errors\HaijinError;
use Haijin\FilePath;
use Haijin\FilesCache;
use Haijin\Parser\Parser;

class Renderer
{
    protected $cache;
    protected $prettyHtml;

    /// Initializing

    public function __construct()
    {
        $this->cache = new FilesCache();
        $this->prettyHtml = false;
    }

    /// Configuring

    public function configure($callable)
    {
        $configurationDsl = new RendererConfigurationDSL($this);

        $configurationDsl->configure($callable);

        return $this;
    }

    /// Accessing

    public function setCacheFolder($folder)
    {
        $this->cache->setCacheFolder($folder);

        return $this;
    }

    public function getCacheFolder()
    {
        return $this->cache->getCacheFolder();
    }

    public function getCacheManifestFilename()
    {
        return $this->cache->getManifestFilename();
    }

    public function setCacheManifestFilename($filename)
    {
        $this->cache->setManifestFilename($filename);

        return $this;
    }

    public function setPrettyHtml($boolean)
    {
        $this->prettyHtml = $boolean;

        return $this;
    }

    public function isPrettyHtml()
    {
        return $this->prettyHtml;
    }

    /// Rendering

    public function renderFile($filename, $variables = [])
    {
        $this->ensureCacheFolderExists();
        $this->ensureManifestFolderExists();

        if (is_string($filename)) {
            $filename = new FilePath($filename);
        }

        return $this->cache->lockingDo(function ($cache) use ($filename, $variables) {

            if ($cache->needsCaching($filename)) {

                $phpContents = $this->parseHaiku(
                    $this->getFileContents($filename)
                );

                if ($filename->isAbsolute()) {

                    throw new HaijinError("Could not find a suiteable cached named for file '{$filename}'.");

                } else {

                    $cachedFilename = $filename;

                }

                $cache->writeFileContents(
                    $filename,
                    $phpContents,
                    $filename
                );

            }

            $phpFilename = $this->cache->getPathOf($filename);

            return $this->evaluatePhpFile($phpFilename, $variables);

        });

        return $this->render(
            $this->getFileContents($filename),
            $variables,
            $filename
        );
    }

    protected function evaluatePhpFile($phpFilename, $variables)
    {
        $sandbox = new EvaluationSandbox();

        return $sandbox->evaluateFile($phpFilename, $variables);
    }

    public function render($input, $variables = [])
    {
        $phpScript = $this->parseHaiku($input);

        return $this->evaluatePhpScript($phpScript, $variables);
    }

    protected function parseHaiku($input)
    {
        $haikuDocument = $this->newParser()->parseString($input);

        return $this->prettyHtml ?
            $haikuDocument->toPrettyHtml() : $haikuDocument->toHtml();
    }

    protected function evaluatePhpScript($phpScript, $variables)
    {
        $sandbox = new EvaluationSandbox();

        return $sandbox->evaluate($phpScript, $variables);
    }

    protected function getFileContents($filename)
    {
        $filepath = new FilePath($filename);

        if (!$filepath->existsFile()) {
            $this->raiseFileNotFoundError($filename);
        }

        return $filepath->readFileContents();
    }

    /// Creating instances

    protected function newParser()
    {
        return new Parser(HaikuParserDefinition::$definition);
    }

    protected function ensureManifestFolderExists()
    {
        if ($this->getCacheManifestFilename() === null) {
            throw new HaijinError(
                "The manifest filename is missing. Seems like the Renderer has not been configured. Configure it by calling \$renderer->configure( function(\$confg) {...})."
            );
        }

        $folder = $this->getCacheManifestFilename()->back();

        if ($folder->existsFolder() || $folder->isEmpty()) {
            return;
        }

        $folder->createFolderPath();
    }

    protected function ensureCacheFolderExists()
    {
        if ($this->getCacheFolder() === null) {
            throw new HaijinError(
                "The cacheFolder is missing. Seems like the Renderer has not been configured. Configure it by calling \$renderer->configure( function(\$confg) {...})."
            );
        }

        $folder = new FilePath($this->getCacheFolder());

        if ($folder->existsFolder() || $folder->isEmpty()) {
            return;
        }

        $folder->createFolderPath();
    }

    /// Raising errors

    protected function raiseFileNotFoundError($filename)
    {
        throw new FileNotFoundError(
            "File '{$filename}' not found.",
            $filename
        );
    }
}