<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.vivifyideas.com/
 * @since      1.0.0
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/includes
 * @author     VivifyIdeas <contact@vivifyideas.com>
 */
class Zwivel_Toc_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    Zwivel_Toc_Activator::saveHeadings();
	}

	private static function saveHeadings() {
	    $shared = new Zwivel_Toc_Shared();
        $posts = get_posts([
            'numberposts' => -1,
        ]);
        foreach ($posts as $post) {
            $zwivelTocHTags = get_post_meta($post->ID, '_zwivel-toc-h-tags', true);
            if (empty($zwivelTocHTags)) {
                $headings = $shared->extractHeadings($post->post_content);
                if (empty($headings)) {
                    update_post_meta( $post->ID, '_zwivel-toc-off', 1 );
                } else {
                    $formattedHeadingData = [
                        'exclude'           => [],
                        'headings'          => [],
                        'ids'               => [],
                        'default_values'    => [],
                        'values'            => []
                    ];
                    foreach ($headings as $heading) {
                        array_push($formattedHeadingData['exclude'], "0");
                        array_push($formattedHeadingData['headings'], $heading[2]);
                        array_push($formattedHeadingData['ids'], $heading['id']);
                        array_push($formattedHeadingData['default_values'], $heading[3]);
                        array_push($formattedHeadingData['values'], $heading[3]);
                    }
                    update_post_meta($post->ID, '_zwivel-toc-h-tags', $formattedHeadingData);
                }
            }
        }
    }

}
