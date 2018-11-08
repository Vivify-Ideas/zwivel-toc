<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.vivifyideas.com/
 * @since      1.0.0
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/public
 * @author     VivifyIdeas <contact@vivifyideas.com>
 */
class Zwivel_Toc_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $headings;
    private $shared;


    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->shared = new Zwivel_Toc_Shared();

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Zwivel_Toc_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Zwivel_Toc_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/zwivel-toc-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Zwivel_Toc_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Zwivel_Toc_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/zwivel-toc-public.js', array('jquery'), $this->version, false);

    }

    public function the_content($content)
    {
        // bail if feed, search or archive
        if ( is_feed() || is_search() || is_archive() ) {
            return $content;
        }

        $this->shared->headings = $this->shared->extractHeadings($content);

        $find = $this->shared->getHeadings();
        $replace = $this->shared->getHeadingsWithAnchors();

//        $hTags = $this->shared->prepareHTags();
//        $html = $this->getTOC($hTags);

        return Zwivel_Toc_Shared::mb_find_replace( $find, $replace, $content );
//        return $html . Zwivel_Toc_Shared::mb_find_replace( $find, $replace, $content );
    }


//    public function getTOC($hTags)
//    {
//        $html = '';
//
//        // add container, toc title and list items
//        $html .= '<div id="zwivel-toc-container" class="">' . PHP_EOL;
//
//        $html .= '<div class="zwivel-toc-title-container">' . PHP_EOL;
//
//        $html .= '<p class="zwivel-toc-title">Table of Contents</p>' . PHP_EOL;
//
//        $html .= '</div>' . PHP_EOL;
//
//        ob_start();
//        $html .= ob_get_clean();
//        $html .= '<nav>' . $this->getTOCList($hTags) . '</nav>';
//
//        ob_start();
//        $html .= ob_get_clean();
//        $html .= '</div>' . PHP_EOL;
//
//        return $html;
//
//    }
//
//    public function getTOCList($hTags)
//    {
//        $html = '';
//
////        if ( $this->hasTOCItems ) {
//            $html .= $this->createTOC( $hTags );
//            $html  = '<ul class="ez-toc-list">' . $html . '</ul>';
////        }
//
//        return $html;
//    }
//
//    /**
//     * Generate the TOC list items for a given page within a post.
//     *
//     * @access private
//     * @since  2.0
//     *
//     * @param int   $page    The page of the post to create the TOC items for.
//     * @param array $matches The heading from the post content extracted with preg_match_all().
//     *
//     * @return string The HTML list of TOC items.
//     */
//    private function createTOC( $hTags )
//    {
//        $html = '';
//
//        $current_depth      = 100;    // headings can't be larger than h6 but 100 as a default to be sure
//        $numbered_items     = array();
//        $numbered_items_min = NULL;
//
//        // find the minimum heading to establish our baseline
//        for ( $i = 0; $i < count( $hTags ); $i ++ ) {
//            if ( $current_depth > $hTags[ $i ]['heading'] ) {
//                $current_depth = (int) $hTags[ $i ]['heading'];
//            }
//        }
//
//        $numbered_items[ $current_depth ] = 0;
//        $numbered_items_min = $current_depth;
//
//        for ( $i = 0; $i < count( $hTags ); $i ++ ) {
//
//            if ( $current_depth == (int) $hTags[ $i ]['heading'] ) {
//
//                $html .= '<li>';
//            }
//
//            // start lists
//            if ( $current_depth != (int) $hTags[ $i ]['heading'] ) {
//
//                for ( $current_depth; $current_depth < (int) $hTags[ $i ]['heading']; $current_depth++ ) {
//
//                    $numbered_items[ $current_depth + 1 ] = 0;
//                    $html .= '<ul><li>';
//                }
//            }
//
//            $title = !empty($hTags[ $i ]['value']) ? $hTags[ $i ]['value'] : $hTags[ $i ]['default_value'];
//
//            $html .= $this->createTOCItemAnchor( $hTags[ $i ]['id'], $title );
//
//            // end lists
//            if ( $i != count( $hTags ) - 1 ) {
//
//                if ( $current_depth > (int) $hTags[ $i + 1 ]['heading'] ) {
//
//                    for ( $current_depth; $current_depth > (int) $hTags[ $i + 1 ]['heading']; $current_depth-- ) {
//
//                        $html .= '</li></ul>';
//                        $numbered_items[ $current_depth ] = 0;
//                    }
//                }
//
//                if ( $current_depth == (int) @$hTags[ $i + 1 ]['heading'] ) {
//
//                    $html .= '</li>';
//                }
//
//            } else {
//
//                // this is the last item, make sure we close off all tags
//                for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {
//
//                    $html .= '</li>';
//
//                    if ( $current_depth != $numbered_items_min ) {
//                        $html .= '</ul>';
//                    }
//                }
//            }
//        }
//
//        return $html;
//    }
//
//
//    /**
//     * @access private
//     * @since  2.0
//     *
//     * @param int    $page
//     * @param string $id
//     * @param string $title
//     *
//     * @return string
//     */
//    private function createTOCItemAnchor( $id, $title )
//    {
//        return sprintf(
//            '<a href="%1$s" title="%2$s">' . $title . '</a>',
//            esc_url( $this->createTOCItemURL( $id ) ),
//            esc_attr( strip_tags( $title ) )
//        );
//    }
//
//
//    /**
//     * @access private
//     * @since  2.0
//     *
//     * @param string $id
//     * @param int    $page
//     *
//     * @return string
//     */
//    private function createTOCItemURL( $id )
//    {
//        return '#' . $id;
//    }


    public function register_zwivel_toc_widget()
    {
        register_widget( 'Zwivel_TOC_Widget' );
    }

}