<?php

namespace Haijin\Haiku\Dom;

use Haijin\Dictionary;

class Haiku_Tag extends Haiku_Node
{
    protected $tag;
    protected $attributes;

    public function __construct($tag = null)
    {
        parent::__construct();

        $this->tag = $tag;
        $this->attributes = new Dictionary();
    }

    public function set_attribute($attribute_name, $attribute_value)
    {
        $this->attributes[ $attribute_name ] = $attribute_value;
    }

    public function to_html($indentation)
    {
        $html = "<{$this->tag}";

        $html .= $this->attributes_to_html();

        if( $this->child_nodes->is_empty() ) {

            $html .= " />";

        } else {

            $html .= ">";

            $html .= $this->child_nodes_to_html( $indentation );

            $html .= "</{$this->tag}>";

        }

        return $html;
    }

    public function to_pretty_html($indentation)
    {
        $html = $this->indent( $indentation ) . "<{$this->tag}";

        $html .= $this->attributes_to_html();

        if( $this->child_nodes->is_empty() ) {

            $html .= " />";

        } else {

            $html .= ">" . "\n";

            $html .= $this->child_nodes_to_pretty_html( $indentation );

            $html .= $this->indent( $indentation ) . "</{$this->tag}>";

        }

        return $html;
    }

    protected function attributes_to_html()
    {
        if( $this->attributes->is_empty() ) {
            return "";
        }

        $strings = [];

        foreach( $this->attributes->to_array() as $name => $value ) {

            $strings[] =
                $name
                . "=" .
                '"' . $value . '"';

        }

        return " " . join( " ", $strings );
    }
}