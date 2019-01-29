<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Parser\Parser_Definition;

class Haiku_Parser extends Parser
{
    /// Initializing

    public function __construct()
    {
        parent::__construct( $this->haiku_definition() );

        $this->indentation = null;
        $this->indentation_unit = null;
        $this->indentation_char = null;
    }

    /// Definition

    protected function haiku_definition()
    {
        return Create::a( Parser_Definition::class )->with()
            ->define_in_file( __DIR__ . "/haiku-definition.php" );
    }

    /// Raising errors

    protected function raise_unmatched_indentation_error($spaces_count, $unit)
    {
        if( $this->indentation_char == "\t" ) {
            $char = "tabs";
        } else {
            $char = "spaces";
        };

        throw Create::an( UnmatchedIndentationError::class )->with(
                "The template is using indentation units of {$unit} {$char}, but a line with {$spaces_count} {$char} was found. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
            );
    }

    protected function raise_not_unique_indentation_char_error()
    {
        throw Create::an( NotUniqueIndentationCharError::class )->with(
                "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
        );
    }

    protected function raise_indentation_char_missmatch_error($used_chars, $missmatched_chars)
    {
        throw Create::an( IndentationCharMissmatchError::class )->with(
                "The template is indenting with {$used_chars} in one line and {$missmatched_chars} in another one, use only tabs or only spaces in all lines. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
        );
    }

    protected function raise_invalid_indentation_increment_error()
    {
        throw Create::an( InvalidIndentationIncrementError::class )->with(
                "Invalid indentation was found. An increment of only one unit was expected. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
        );
    }

}