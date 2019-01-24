<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Haiku_Node
{
    protected $child_nodes;

    public function __construct($tag = null)
    {
        $this->child_nodes = Create::an( Ordered_Collection::class )->with();
    }

    public function add_child($child)
    {
        $this->child_nodes->add( $child );
    }

    public function last_child()
    {
        return $this->child_nodes[ -1 ];
    }

    protected function indent($indentation)
    {
        return str_repeat( "    ", $indentation );
    }
}