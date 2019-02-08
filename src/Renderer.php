<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Parser\Parser;
use Haijin\Files_Cache;

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

                $php_contents = $this->parse_haiku( file_get_contents( $filename ) );

                $cache->cache_file_contents(
                    $filename,
                    $php_contents,
                    str_replace( "/", "---", $filename )
                );

            } else {
                $php_contents = file_get_contents( $this->cache->get_path_of( $filename ) );
            }

            return $this->evaluate_php_script( $php_contents, $variables );

        }, $this );

        return $this->render( file_get_contents( $filename ), $variables );
    }

    public function render($input, $variables = [])
    {
        $php_script = $this->parse_haiku( $input );

        return $this->evaluate_php_script( $php_script, $variables );
    }

    protected function parse_haiku($input)
    {
        $haiku_document = $this->new_parser()->parse_string( $input );

        return $this->pretty_html ?
                $haiku_document->to_pretty_html() : $haiku_document->to_html();
    }

    protected function evaluate_php_script($php_script, $variables)
    {

        extract( $variables );

        ob_start();

        eval( "?>\n" . $php_script );

        return ob_get_clean();

    }

    /// Creating instances

    protected function new_parser()
    {
        return Create::a( Parser::class )->with( Haiku_Parser_Definition::$definition );
    }
}