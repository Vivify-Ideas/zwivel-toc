<?php

/**
 * Adds Zwivel_TOC_Widget widget.
 */
class Zwivel_TOC_Widget extends WP_Widget {

    private $shared;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'zwivel_toc_widget', // Base ID
			'Zwivel TOC Widget', // Name
			array( 'description' => 'Zwivel TOC Widget', ) // Args
		);

		$this->shared = new Zwivel_Toc_Shared();
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
        global $post;

        $tocOff = get_post_meta($post->ID, '_zwivel-toc-off', true);

        if ($tocOff) {
            return;
        }

        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );


        if (!empty($hTagsFromDB)) {

             $hTags = $this->shared->prepareHTags($hTagsFromDB);

             // @TODO treba implementirati
//            $hTags = $this->removeHeadings($hTagsFromDB);

            echo $this->shared->getTOC($hTags);

        }
	}


	/* @TODO F-ja preuzeta iz shared - treba je doraditi da filtrira i vraca odredjeni format */
    private function removeHeadings( &$matches ) {

        $levels = get_option( 'zwivel-toc-settings', array() );
        $levels = array_filter($levels, function($level) { return $level != 0; });

        echo '<pre>';
        var_dump($matches);
        echo '</pre>';

//        if ( count( $levels ) != 6 ) {

            $new_matches = array();
            $count       = count( $matches );

            for ( $i = 0; $i < $count; $i++ ) {

                if ( in_array( $matches[ $i ]['headings'], $levels ) ) {

                    $new_matches[] = $matches[ $i ];
                }
            }

            $matches = $new_matches;
//        }

        return $matches;
    }

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

	}

}