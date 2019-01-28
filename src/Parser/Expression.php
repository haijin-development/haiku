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
        $this->handler_closure = null;
        $this->particle_options = Create::an( Ordered_Collection::class )->with();
        $this->particle_options->add(
            Create::an( Ordered_Collection::class )->with()
        );
    }

    /// Accessing

    public function get_name()
    {
        return $this->name;
    }

    public function get_particle_options()
    {
        return clone $this->particle_options;
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
        $this->add_particle(
            Create::a( Multiple_Regex_Particle::class )->with( $regex_string )
        );

        return $this;
    }

    public function regex($regex_string)
    {
        $this->add_particle(
            Create::a( Regex_Particle::class )->with( $regex_string )
        );

        return $this;
    }

    public function exp($expression_name)
    {
        $this->add_particle(
            Create::a( Expression_Particle::class )->with( $expression_name )
        );

        return $this;
    }

    public function lit($literal_string)
    {
        $this->add_particle(
            Create::a( Literal_Particle::class )->with( $literal_string )
        );

        return $this;
    }

    public function or()
    {
        $this->particle_options->add(
            Create::an( Ordered_Collection::class )->with()
        );

        return $this;
    }

    public function handler($closure)
    {
        $this->handler_closure = $closure;
    }

    protected function add_particle($particle)
    {
        $this->particle_options->last()->add( $particle );
    }
}