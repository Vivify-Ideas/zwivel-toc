<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.vivifyideas.com/
 * @since      1.0.0
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/admin
 * @author     VivifyIdeas <contact@vivifyideas.com>
 */
class Zwivel_Toc_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $shared;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->shared = new Zwivel_Toc_Shared();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( 'zwivel-toc-admin-css', plugin_dir_url( __FILE__ ) . 'css/zwivel-toc-admin.css', array(), rand(1, 99999), 'all' );

	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( 'zwivel-toc-admin-js', plugin_dir_url( __FILE__ ) . 'js/zwivel-toc-admin.js', array('jquery'), rand(1, 99999), false );

	}


	public function adding_custom_meta_boxes()
    {
        add_meta_box(
            'zwivel_toc',
            'Table of Contents',
            [$this, 'table_of_contents'],
            'post',
            'side',
            'default'
        );
    }


    public function table_of_contents()
    {
        global $post;

        $value = get_post_meta($post->ID, '_zwivel-toc-off', true);
        $checked = !empty($value) ? 'checked="checked"' : '';

        echo '<div class="zw-toc-admin-checkbox">';
        echo '<label><input type="checkbox" value="1" ' . $checked . ' name="zwivel-toc-off" />Turn off TOC for this post</label>';
        echo '</div>';


        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );

        if (!empty($hTagsFromDB)) {
            $hTags = $this->shared->prepareHTags($hTagsFromDB);

            for ($i = 0; $i < count($hTags); $i++) {

                $checked = (!empty($hTags[$i]['exclude'])) ? 'checked="checked"' : '';

                echo '<div class="zw-c-admin-checkbox-container">';
                echo '<div class="zw-c-admin-checkbox-item">';
                echo '<input type="hidden" name="h-tags[exclude][' . $i . ']" value="0">';
                echo '<input class="zw-c-admin-checkbox" type="checkbox" '. $checked . '" name="h-tags[exclude][' . $i . ']" value="1">';
                echo '<label>H' . $hTags[$i]['heading'] . '</label>';
                echo '</div>';
                echo '<input type="hidden" name="h-tags[headings][' . $i . ']" value="' . $hTags[$i]['heading'] . '">';
                echo '<input type="hidden" name="h-tags[ids][' . $i . ']" value="' . $hTags[$i]['id'] . '">';
                echo '<input type="hidden" name="h-tags[default_values][' . $i . ']" value="' . $hTags[$i]['default_value'] . '">';
                echo '<input type="text" name="h-tags[values][' . $i . ']" value="' . (!empty($hTags[$i]['value']) ? $hTags[$i]['value'] : $hTags[$i]['default_value']) . '">';
                echo '<small>#' . str_replace( ' ', '_', $hTags[$i]['default_value'] )  . '</small>';

                echo '</div>';
            }
        } else {
            $headings = $this->shared->extractHeadings($post->post_content);

            for ($i = 0; $i < count($headings); $i++) {
                $checked = (isset($headings[$i]['exclude'])) ? 'checked="checked"' : '';

                echo '<div class="zw-c-admin-checkbox-container">';
                echo '<div class="zw-c-admin-checkbox-item">';
                echo '<input type="hidden" name="h-tags[exclude][' . $i . ']" value="0">';
                echo '<input class="zw-c-admin-checkbox" type="checkbox" '. $checked . '" name="h-tags[exclude][' . $i . ']" value="1">';
                echo '<label>H' . $headings[$i][2] . '</label>';
                echo '</div>';
                echo '<input type="hidden" name="h-tags[headings][' . $i . ']" value="' . $headings[$i][2] . '">';
                echo '<input type="hidden" name="h-tags[ids][' . $i . ']" value="' . $headings[$i]['id'] . '">';
                echo '<input type="hidden" name="h-tags[default_values][' . $i . ']" value="' . strip_tags($headings[$i][0]) . '">';
                echo '<input type="text" name="h-tags[values][' . $i . ']" value="' . strip_tags($headings[$i][0]) . '">';
                echo '<small>#' . str_replace( ' ', '_', strip_tags($headings[$i][0]) )  . '</small>';

                echo '</div>';
            }
        }

    }


    /**
     * Callback which saves the user preferences from the table of contents metaboxes.
     *
     *
     * @param int    $post_id The post ID.
     * @param object $post    The post object.
     * @param bool   $update  Whether this is an existing post being updated or not.
     */
    public function save($post_id, $post, $update)
    {
        if ( isset( $_REQUEST['h-tags'] ) && !empty( $_REQUEST['h-tags'] ) ) {
            update_post_meta( $post_id, '_zwivel-toc-h-tags', $_REQUEST['h-tags'] );
        }

        if ( isset( $_REQUEST['zwivel-toc-off'] ) && !empty( $_REQUEST['zwivel-toc-off'] ) ) {
            update_post_meta( $post_id, '_zwivel-toc-off', $_REQUEST['zwivel-toc-off'] );
        } else {
            delete_post_meta($post_id, '_zwivel-toc-off');
        }
    }

}
