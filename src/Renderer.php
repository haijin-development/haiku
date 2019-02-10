<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Parser\Parser;
use Haijin\Files_Cache;
use Haijin\File_Path;

class Renderer
{
    protected $cache;
    protected $pretty_html;

    /// Initializing

    public function __construct()
    {
        $this->cache = Create::a( Files_Cache::class )->with();
        $this->pretty_html = false;
    }

    /// Accessing

    public function set_cache_folder($folder)
    {
        $this->cache->set_cache_folder( $folder );

        return $this;
    }

    public function get_cache_folder()
    {
        return $this->cache->get_cache_folder();
    }

    public function get_manifest_filename()
    {
        return $this->cache->get_manifest_filename();
    }

    public function set_cache_manifest_filename($filename)
    {
        $this->cache->set_manifest_filename( $filename );

        return $this;
    }

    public function set_pretty_html($boolean)
    {
        $this->pretty_html = $boolean;

        return $this;
    }

    public function is_pretty_html()
    {
        return $this->pretty_html;
    }

    /// Configuring

    public function configure($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $closure->call( $binding, $this );

        return $this;
    }

    public function __set($attribute, $value)
    {
        if( $attribute == "cache_folder" ) {
            $this->set_cache_folder( $value );
        }

        if( $attribute == "cache_manifest_filename" ) {
            $this->set_cache_manifest_filename( $value );
        }

        if( $attribute == "pretty_html" ) {
            $this->set_pretty_html( $value );
        }

        $this->$attribute = $value;
    }

    /// Rendering

    public function render_file($filename, $variables = [])
    {
        return $this->cache->locking_do( function($cache) use($filename, $variables) {

            if( $cache->needs_caching( $filename ) ) {

                $php_contents = $this->parse_haiku(
                    $this->get_file_contents( $filename )
                );

                if( ( new File_Path( $filename) )->is_absolute() ) {

                    $php_filename = str_replace( "/", "---", $filename );

                } else {

                    $php_filename = $filename;

                }

                $cache->cache_file_contents(
                    $filename,
                    $php_contents,
                    $php_filename
                );

            } else {
                $php_filename = $this->cache->get_path_of( $filename );

                $php_contents = file_get_contents( $php_filename );
            }

            Evaluation_Sandbox::$haiku_filename = $filename;
            Evaluation_Sandbox::$php_filename = $php_filename;

            return $this->evaluate_php_script( $php_contents, $variables, $filename );

        }, $this );

        return $this->render( $this->get_file_contents( $filename ), $variables, $filename );
    }

    public function render($input, $variables = [], $filename = null)
    {
        $php_script = $this->parse_haiku( $input );

        return $this->evaluate_php_script( $php_script, $variables, $filename );
    }

    protected function parse_haiku($input)
    {
        $haiku_document = $this->new_parser()->parse_string( $input );

        return $this->pretty_html ?
                $haiku_document->to_pretty_html() : $haiku_document->to_html();
    }

    protected function evaluate_php_script($php_script, $variables, $filename = null)
    {
        $sandbox = new Evaluation_Sandbox();

        $result = $sandbox->evaluate($php_script, $variables);

        foreach( $sandbox->get_evaluation_errors() as $error ) {

            $message = "Haiku evaluation error: '" . $error->message ."'";

            if( $filename !== null ) {
                $message .= "\n in file {$this->cache->get_path_of( $filename )}\n";
                $message .= "generated from haiku template {$filename}\n";
            }

            $message .= " at line {$error->line_number}.\n";

            echo $message;

        }

        return $result;
    }

    protected function get_file_contents($filename)
    {
        $filepath = new File_Path( $filename );

        if( ! $filepath->exists_file() ) {
            $this->raise_file_not_found_error( $filename );
        }

        return $filepath->file_contents();
    }

    /// Creating instances

    protected function new_parser()
    {
        return Create::a( Parser::class )->with( Haiku_Parser_Definition::$definition );
    }

    /// Raising errors

    protected function raise_file_not_found_error($filename)
    {
        throw new File_Not_Found_Error(
            "File '{$filename}' not found.",
            $filename
        );
    }
}