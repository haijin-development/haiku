<?php

namespace Haijin\Haiku\Dom;

class HaikuBrackedStatement extends HaikuNode
{
    protected $expression;

    public function __construct($expression = "")
    {
        parent::__construct();

        $this->expression = $expression;
    }

    public function toHtml($indentation)
    {
        $html = "<?php {$this->expression} { ?>";

        $html .= $this->childNodesToHtml($indentation);

        $html .= "<?php } ?>";

        return $html;
    }

    public function toPrettyHtml($indentation)
    {
        $html = $this->indent($indentation);

        $html .= "<?php {$this->expression} { ?>" . "\n";

        $html .= $this->childNodesToPrettyHtml($indentation);

        $html .= "<?php } ?>";

        return $html;
    }
}