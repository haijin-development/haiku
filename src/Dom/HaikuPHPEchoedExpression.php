<?php

namespace Haijin\Haiku\Dom;

class HaikuPHPEchoedExpression extends HaikuNode
{
    protected $expression;
    protected $escaped;

    public function __construct($expression = "", $escaped = true)
    {
        parent::__construct();

        if ($expression[strlen($expression) - 1] != ";") {
            $this->expression = $expression;
        } else {
            $this->expression = \substr($expression, 0, strlen($expression) - 1);
        }


        $this->escaped = $escaped;
    }

    public function toHtml($indentation)
    {
        if ($this->escaped) {

            return "<?php echo htmlspecialchars( {$this->expression} ); ?>";

        } else {

            return "<?php echo {$this->expression}; ?>";

        }
    }

    public function toPrettyHtml($indentation)
    {
        if ($this->escaped) {

            return $this->indent($indentation) .
                "<?php echo htmlspecialchars( {$this->expression} ); ?>";

        } else {

            return $this->indent($indentation) .
                "<?php echo {$this->expression}; ?>";

        }
    }
}