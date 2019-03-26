<?php

namespace Haijin\Haiku\Dom;

class Haiku_Document extends Haiku_Node
{
    public function to_html()
    {
        return $this->child_nodes_to_html( -1 );
    }

    public function to_pretty_html()
    {
        return $this->child_nodes_to_pretty_html( -1 );
    }
}