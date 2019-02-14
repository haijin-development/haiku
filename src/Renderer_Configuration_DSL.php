<?php

namespace Haijin\Haiku;

class Renderer_Configuration_DSL
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
        $callable( $this );

        return $this->renderer;
    }

    /// DSL

    public function set_cache_folder($value)
    {
        $this->renderer->set_cache_folder( $value );
    }

    public function set_cache_manifest_filename($value)
    {
        $this->renderer->set_cache_manifest_filename( $value );
    }

    public function set_pretty_html($value)
    {
        $this->renderer->set_pretty_html( $value );
    }

    public function __set($attribute, $value)
    {
        $setter = "set_{$attribute}";

        $this->$setter( $value );
    }

    public function __get($attribute)
    {
        $getter = "get_{$attribute}";

        return $this->$getter();
    }
}