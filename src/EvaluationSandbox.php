<?php

namespace Haijin\Haiku;

class EvaluationSandbox
{
    public function evaluateFile($filename, $variables)
    {
        extract($variables);

        ob_start();

        require($filename);

        return ob_get_clean();
    }

    public function evaluate($phpScript, $variables)
    {
        extract($variables);

        ob_start();

        eval("?>\n" . $phpScript);

        return ob_get_clean();
    }
}