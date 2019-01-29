<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Haiku_Node
{
    protected $parent_node;
    protected $child_nodes;

    /// Initializing

    public function __construct()
    {
        $this->parent_node = null;
        $this->child_nodes = Create::an( Ordered_Collection::class )->with();
    }

    /// Accessing

    public function last_child()
    {
        return $this->child_nodes[ -1 ];
    }

    public function get_parent()
    {
        return $this->parent_node;
    }

    public function set_parent($parent_node)
    {
        $this->parent_node = $parent_node;
    }

    /// Adding children

    public function add_child($child)
    {
        //$child->set_parent( $this );

        $this->child_nodes->add( $child );

        return $this;
    }

    public function add_children($children)
    {
        $children->each_do( function($child) {

            $this->add_child( $child );

        }, $this );

        return $this;
    }

    /// Displaying

    public function indent($indentation)
    {
        return str_repeat( "    ", $indentation );
    }
}