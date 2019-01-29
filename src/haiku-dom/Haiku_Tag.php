<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Dictionary;

class Haiku_Tag extends Haiku_Node
{
    protected $tag;
    protected $attributes;

    public function __construct($tag = null)
    {
        parent::__construct();

        $this->tag = $tag;
        $this->attributes = Create::a( Dictionary::class ) ->with();
    }

    public function set_attribute($attribute_name, $attribute_value)
    {
        $this->attributes[ $attribute_name ] = $attribute_value;
    }

    public function to_html($indentation)
    {
        $html = $this->indent( $indentation ) . "<{$this->tag}";

        if( $this->attributes->not_empty() ) {
            $html .= " ";

            $strings = [];

            foreach( $this->attributes->to_array() as $name => $value ) {

                $strings[] = \htmlspecialchars( $name ) . "=" .
                    '"' . \htmlspecialchars( $value ) . '"';

            }

            $html .= join( ", ", $strings );

        }

        $html .= ">" . "\n";

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