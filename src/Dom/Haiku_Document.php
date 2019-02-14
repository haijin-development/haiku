<?php

namespace Haijin\Haiku\Dom;

class Haiku_Document extends Haiku_Node
{
    public function to_html()
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {
            $html .= $node->to_html( 0 );
        }

        return $html;
    }

    public function to_pretty_html()
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {
            $html .= $node->to_pretty_html( 0 );
            $html .= "\n";
        }

        return $html;
    }
}