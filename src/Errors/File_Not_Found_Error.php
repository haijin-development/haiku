<?php

namespace Haijin\Haiku\Errors;

class File_Not_Found_Error extends Error
{
    protected $filename;

    /// Initializing

    public function __construct($message, $filename)
    {
        parent::__construct( $message );

        $this->filename = $filename;
    }


    /// Accessing

    public function get_filename()
    {
        return $this->filename;
    }
}