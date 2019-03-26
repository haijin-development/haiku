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

    /// Adding children

    public function add_child($child)
    {
        $this->child_nodes->add( $child );

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

            $node_html = $node->to_html( $indentation + 1 );

            if( preg_match( '/^<\?php +else/', $node_html ) ) {

                $html = preg_replace( '/\?>$/', '', $html );

                $node_html = preg_replace( '/^<\?php +else/', 'else', $node_html );

            }

            $html .= $node_html;

        }

        return $html;
    }

    protected function child_nodes_to_pretty_html($indentation)
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {

            $node_html = $node->to_pretty_html( $indentation + 1 );

            if( preg_match( '/^<\?php +else/', $node_html ) ) {

                $html = preg_replace( '/\?>$/', '', $html );

                $node_html = preg_replace( '/^<\?php +else/', 'else', $node_html );

            }

            $html .= $node_html;

            $html .= "\n";
        }

        return $html;
    }
}