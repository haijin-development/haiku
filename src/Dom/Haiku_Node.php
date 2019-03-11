<?php

namespace Haijin\Haiku\Dom;

use Haijin\Ordered_Collection;

class Haiku_Node
{
    protected $child_nodes;

    /// Initializing

    public function __construct()
    {
        $this->child_nodes = new Ordered_Collection();
    }

    /// Accessing

    public function last_child()
    {
        return $this->child_nodes[ -1 ];
    }

    /// Adding children

    public function add_child($child)
    {
        $this->child_nodes->add( $child );

        return $this;
    }

    public function add_children($children)
    {
        $children->each_do( function($child) {

            $this->add_child( $child );

        });

        return $this;
    }

    /// Displaying

    public function indent($indentation)
    {
        return str_repeat( "    ", $indentation );
    }

    protected function child_nodes_to_html($indentation)
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {
            $html .= $node->to_html( $indentation + 1 );
        }

        return $html;
    }

    protected function child_nodes_to_pretty_html($indentation)
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {
            $html .= $node->to_pretty_html( $indentation + 1 );
            $html .= "\n";
        }

        return $html;
    }
}