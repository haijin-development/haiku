<?php

namespace Haijin\Haiku;

class Haiku_Document extends Haiku_Node
{
    public function to_html()
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {
            $html .= $node->to_html( 0 );
            $html .= "\n";
        }

        return $html;
    }
}