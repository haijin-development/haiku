<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Expression
{
    protected $name;
    protected $particles;
    protected $handler_closure;

    /// Initializing

    public function __construct($name)
    {
        $this->name = $name;
        $this->particles = Create::an( Ordered_Collection::class )->with();
        $this->handler_closure = null;
    }

    /// Accessing

    public function get_name()
    {
        return $this->name;
    }

    public function get_particles()
    {
        return $this->particles;
    }

    public function get_handler_closure()
    {
        return $this->handler_closure;
    }

    /// DSL

    public function matcher($closure)
    {
        $closure->call( $this );
    }

    public function m_regex($regex_string)
    {
        $this->particles->add(
            Create::a( Multiple_Regex_Particle::class )->with( $regex_string )
        );
    }

    public function regex($regex_string)
    {
        $this->particles->add(
            Create::a( Regex_Particle::class )->with( $regex_string )
        );
    }

    public function exp($expression_name)
    {
        $this->particles->add(
            Create::a( Expression_Particle::class )->with( $expression_name )
        );
    }

    public function handler($closure)
    {
        $this->handler_closure = $closure;
    }

}