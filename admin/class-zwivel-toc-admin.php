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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zwivel-toc-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zwivel-toc-admin.js', array( 'jquery' ), $this->version, false );

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

        ?>

        <div class="zw-toc-admin-checkbox">
            <label><input type="checkbox" value="1" <?php checked($value, true, true); ?> name="zwivel-toc-off" />Turn off TOC for this post</label>
        </div>

        <?php

        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );

        if (!empty($hTagsFromDB)) {
            $hTags = $this->shared->prepareHTags($hTagsFromDB);

            //@TODO pretabaj da ide cist html umesto ovih echo-a
            foreach ($hTags as $hTag) {
                echo '<span>H' . $hTag['heading'] . '</span>';
                echo '<input type="hidden" name="h-tags[headings][]" value="' . $hTag['heading'] . '" class="widefat">';
                echo '<input type="hidden" name="h-tags[ids][]" value="' . $hTag['id'] . '" class="widefat">';
                echo '<input type="hidden" name="h-tags[default_values][]" value="' . $hTag['default_value'] . '" class="widefat">';
                echo '<input type="text" name="h-tags[values][]" value="' . $hTag['value'] . '" class="widefat">';
                echo '<small>#' . str_replace( ' ', '_', $hTag['default_value'] )  . '</small>';

                echo '<br/>';
            }
        } else {
            $headings = $this->shared->extractHeadings($post->post_content);

            foreach ($headings as $heading) {

                echo '<span>H' . $heading[2] . '</span>';
                echo '<input type="hidden" name="h-tags[headings][]" value="' . $heading[2] . '" class="widefat">';
                echo '<input type="hidden" name="h-tags[ids][]" value="' . $heading['id'] . '" class="widefat">';
                echo '<input type="hidden" name="h-tags[default_values][]" value="' . strip_tags( $heading[0] )  . '" class="widefat">';
                echo '<input type="text" name="h-tags[values][]" value="' . strip_tags( $heading[0] )  . '" class="widefat">';
                echo '<small>#' . str_replace( ' ', '_', strip_tags($heading[0] ))  . '</small>';

                echo '<br/>';
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
