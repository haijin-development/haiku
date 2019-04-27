<?php

namespace Haijin\Haiku;

class RendererConfigurationDSL
{
    protected $renderer;

    /// Initializing

    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /// Configuring

    public function configure($callable)
    {
        $callable($this);

        return $this->renderer;
    }

    /// DSL

    public function setCacheFolder($value)
    {
        $this->renderer->setCacheFolder($value);
    }

    public function getCacheFolder()
    {
        return $this->renderer->getCacheFolder();
    }

    public function setCacheManifestFilename($value)
    {
        $this->renderer->setCacheManifestFilename($value);
    }

    public function getCacheManifestFilename()
    {
        return $this->renderer->getCacheManifestFilename();
    }

    public function setPrettyHtml($value)
    {
        $this->renderer->setPrettyHtml($value);
    }

    public function getPrettyHtml()
    {
        return $this->renderer->isPrettyHtml();
    }

    public function __set($attribute, $value)
    {
        $setter = "set{$attribute}";

        $this->$setter($value);
    }

    public function __get($attribute)
    {
        $getter = "get{$attribute}";

        return $this->$getter();
    }
}