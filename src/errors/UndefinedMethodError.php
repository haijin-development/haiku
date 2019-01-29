<?php

namespace Haijin\Haiku;

class UndefinedMethodError extends Error
{
    protected $method_name;
    protected $parser;

    /// Initializing

    public function __construct($message, $method_name, $parser)
    {
        parent::__construct( $message );

        $this->method_name = $method_name;
        $this->parser = $parser;
    }

    /// Accesing

    public function get_method_name()
    {
        return $this->method_name;
    }

    public function get_parser()
    {
        return $this->parser;
    }
}