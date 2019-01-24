<?php

namespace Haijin\Haiku;

class Haiku_Tag extends Haiku_Node
{
    protected $tag;

    public function __construct($tag = null)
    {
        parent::__construct();

        $this->tag = $tag;
    }

    public function to_html($indentation)
    {
        $html = $this->indent( $indentation ) . "<{$this->tag}>" . "\n";

        $html .= $this->child_nodes_to_html( $indentation );

        $html .= $this->indent( $indentation ) . "</{$this->tag}>";

        return $html;
    }

    protected function child_nodes_to_html($indentation)
    {
        $html = "";

        foreach( $this->child_nodes->to_array() as $i => $node ) {
            $html .= $node->to_html( $indentation + 1 );
            $html .= "\n";
        }

        return $html;        
    }
}