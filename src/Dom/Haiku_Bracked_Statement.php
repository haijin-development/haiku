<?php

namespace Haijin\Haiku\Dom;

class Haiku_Bracked_Statement extends Haiku_Node
{
    protected $expression;

    public function __construct($expression = "")
    {
        parent::__construct();

        $this->expression = $expression;
    }

    public function to_html($indentation)
    {
        $html = "<?php {$this->expression} { ?>";

        $html .= $this->child_nodes_to_html( $indentation );

        $html .= "<?php } ?>";

        return $html;
    }

    public function to_pretty_html($indentation)
    {
        $html = $this->indent( $indentation );

        $html .= "<?php {$this->expression} { ?>" . "\n";

        $html .= $this->child_nodes_to_pretty_html( $indentation );

        $html .= "<?php } ?>";

        return $html;
    }
}