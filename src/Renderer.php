<?php

namespace Haijin\Haiku;

use Haijin\Parser\Parser;
use Haijin\Files_Cache;
use Haijin\File_Path;
use  Haijin\Haiku\Errors\File_Not_Found_Error;

class Renderer
{
    protected $cache;
    protected $pretty_html;

    /// Initializing

    public function __construct()
    {
        $this->cache = new Files_Cache();
        $this->pretty_html = false;
    }

    /// Configuring

    public function configure($callable)
    {
        $configuration_dsl = new Renderer_Configuration_DSL( $this );

        $configuration_dsl->configure( $callable );

        return $this;
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

    /// Rendering

    public function render_file($filename, $variables = [])
    {
        $this->ensure_manifest_folder_exists();
        $this->ensure_cache_folder_exists();

        return $this->cache->locking_do( function($cache) use($filename, $variables) {

            if( $cache->needs_caching( $filename ) ) {

                $php_contents = $this->parse_haiku(
                    $this->get_file_contents( $filename )
                );

                $cache->cache_file_contents(
                    $filename,
                    $php_contents,
                    $filename
                );

            }

            $php_filename = $this->cache->get_path_of( $filename );

            return $this->evaluate_php_file( $php_filename, $variables );

        });

        return $this->render(
            $this->get_file_contents( $filename ),
            $variables,
            $filename
        );
    }

    protected function evaluate_php_file($php_filename, $variables)
    {
        $sandbox = new Evaluation_Sandbox();

        return $sandbox->evaluate_file($php_filename, $variables);
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
        $sandbox = new Evaluation_Sandbox();

        return $sandbox->evaluate($php_script, $variables);
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
        return new Parser( Haiku_Parser_Definition::$definition );
    }

    protected function ensure_manifest_folder_exists()
    {
        if( $this->get_manifest_filename() === null ) {
            throw new \RuntimeException(
                "The manifest filename is missing. Seems like the Renderer has not been configured. Configure it by calling configure( function($confg) {...})."
            );
        }

        $folder = ( new File_Path( $this->get_manifest_filename() ) )->back();

        if( $folder->exists_folder() ) {
            return;
        }

        if( $folder->is_empty() || $folder->create_folder_path() === false ) {
            $this->raise_could_not_create_manifest_folder_error( $folder->to_string() );
        }
    }

    protected function ensure_cache_folder_exists()
    {
        if( $this->get_cache_folder() === null ) {
            throw new \RuntimeException(
                "The cache_folder is missing. Seems like the Renderer has not been configured. Configure it by calling configure( function($confg) {...})."
            );
        }

        $folder = new File_Path( $this->get_cache_folder() );

        if( $folder->exists_folder() ) {
            return;
        }

        if( $folder->is_empty() || $folder->create_folder_path() === false ) {
            $this->raise_could_not_create_cache_folder_error( $folder->to_string() );
        }
    }

    /// Raising errors

    protected function raise_file_not_found_error($filename)
    {
        throw new File_Not_Found_Error(
            "File '{$filename}' not found.",
            $filename
        );
    }

    protected function raise_could_not_create_manifest_folder_error($folder)
    {
        throw new \RuntimeException( "Could not create manifest folder '$folder'." );
    }
}