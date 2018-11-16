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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/zwivel-toc-public.css', array(), rand(1, 99999), 'all');

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

        global $post;

        $tocOff = get_post_meta($post->ID, '_zwivel-toc-off', true);
        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );

        if ($tocOff) {
            return;
        }

        if ($post->post_type === 'post' && !empty($hTagsFromDB)) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/zwivel-toc-public.js', array('jquery'), rand(1, 99999), false);
        }

    }

    public function the_content($content)
    {
        global $post;

        $tocOff = get_post_meta($post->ID, '_zwivel-toc-off', true);

        // bail if feed, search, archive or toc is turned off by admin
        if ( is_feed() || is_search() || is_archive() || $tocOff) {
            return $content;
        }

        $this->shared->headings = $this->shared->extractHeadings($content);

        $find = $this->shared->getHeadings();
        $replace = $this->shared->getHeadingsWithAnchors();

        return Zwivel_Toc_Shared::mb_find_replace( $find, $replace, $content );
    }


    public function zwivel_toc_sticky_dropdown()
    {
        global $post;

        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );

        if (!empty($hTagsFromDB)) {
            $hTags = $this->shared->prepareHTags($hTagsFromDB);

            echo $this->shared->createTOC($hTags);
        }

    }

    public function register_zwivel_toc_widget()
    {
        register_widget( 'Zwivel_TOC_Widget' );
    }

}