<?php
/**
 * Created by PhpStorm.
 * User: joakimcarlsten
 * Date: 2017-09-25
 * Time: 19:40
 */


class TemplateLoader
{
    /**
     * TemplateLoader constructor.
     */
    public function __construct()
    {
    }
    public function locateTemplate($template_name, $template_path = '', $default_path = '' ) {
        if ( ! $template_path ) :
            $template_path = 'views/tracker/';
        endif;
        // Set default plugin templates path.
        if ( ! $default_path ) :
            $default_path = plugin_dir_path( __DIR__ ) . 'views/'; // Path to the template folder
        endif;
        // Search template file in theme folder.
        $template = locate_template( array(
            $template_path . $template_name,
            $template_name
        ) );
        // Get plugins template file.
        if ( ! $template ) :
            $template = $default_path . $template_name;
        endif;
        return $template;
    }
    public function getTemplate($template_name, $args = array(), $template_path = '', $default_path = '' ) {
        if ( is_array( $args ) && isset( $args ) ) :
            extract( $args );
        endif;
        $template_file = $this->locateTemplate( $template_name, $template_path, $default_path );
        if ( ! file_exists( $template_file ) ) :
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
            return;
        endif;
        include $template_file;
    }
}