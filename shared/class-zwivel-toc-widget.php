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

        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );

        if (!empty($hTagsFromDB)) {
            $hTags = $this->shared->prepareHTags($hTagsFromDB);

            echo $this->getTOC($hTags);
        }
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








    public function getTOC($hTags)
    {
        $html = '';

        // add container, toc title and list items
        $html .= '<div id="zwivel-toc-container" class="">' . PHP_EOL;

        $html .= '<div class="zwivel-toc-title-container">' . PHP_EOL;

        $html .= '<h3 class="zwivel-toc-title">CONTENTS</h3>' . PHP_EOL;

        $html .= '</div>' . PHP_EOL;

        ob_start();
        $html .= ob_get_clean();
        $html .= '<nav>' . $this->getTOCList($hTags) . '</nav>';

        ob_start();
        $html .= ob_get_clean();
        $html .= '</div>' . PHP_EOL;

        return $html;

    }

    public function getTOCList($hTags)
    {
        $html = '';

//        if ( $this->hasTOCItems ) {
        $html .= $this->createTOC( $hTags );
        $html  = '<ul class="ez-toc-list">' . $html . '</ul>';
//        }

        return $html;
    }

    /**
     * Generate the TOC list items for a given page within a post.
     *
     * @access private
     * @since  2.0
     *
     * @param int   $page    The page of the post to create the TOC items for.
     * @param array $matches The heading from the post content extracted with preg_match_all().
     *
     * @return string The HTML list of TOC items.
     */
    private function createTOC( $hTags )
    {
        $html = '';

        $current_depth      = 100;    // headings can't be larger than h6 but 100 as a default to be sure
        $numbered_items     = array();
        $numbered_items_min = NULL;

        // find the minimum heading to establish our baseline
        for ( $i = 0; $i < count( $hTags ); $i ++ ) {
            if ( $current_depth > $hTags[ $i ]['heading'] ) {
                $current_depth = (int) $hTags[ $i ]['heading'];
            }
        }

        $numbered_items[ $current_depth ] = 0;
        $numbered_items_min = $current_depth;

        for ( $i = 0; $i < count( $hTags ); $i ++ ) {

            if ( $current_depth == (int) $hTags[ $i ]['heading'] ) {

                $html .= '<li>';
            }

            // start lists
            if ( $current_depth != (int) $hTags[ $i ]['heading'] ) {

                for ( $current_depth; $current_depth < (int) $hTags[ $i ]['heading']; $current_depth++ ) {

                    $numbered_items[ $current_depth + 1 ] = 0;
                    $html .= '<ul><li>';
                }
            }

            $title = !empty($hTags[ $i ]['value']) ? $hTags[ $i ]['value'] : $hTags[ $i ]['default_value'];

            $html .= $this->createTOCItemAnchor( $hTags[ $i ]['id'], $title );

            // end lists
            if ( $i != count( $hTags ) - 1 ) {

                if ( $current_depth > (int) $hTags[ $i + 1 ]['heading'] ) {

                    for ( $current_depth; $current_depth > (int) $hTags[ $i + 1 ]['heading']; $current_depth-- ) {

                        $html .= '</li></ul>';
                        $numbered_items[ $current_depth ] = 0;
                    }
                }

                if ( $current_depth == (int) @$hTags[ $i + 1 ]['heading'] ) {

                    $html .= '</li>';
                }

            } else {

                // this is the last item, make sure we close off all tags
                for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {

                    $html .= '</li>';

                    if ( $current_depth != $numbered_items_min ) {
                        $html .= '</ul>';
                    }
                }
            }
        }

        return $html;
    }


    /**
     * @access private
     * @since  2.0
     *
     * @param int    $page
     * @param string $id
     * @param string $title
     *
     * @return string
     */
    private function createTOCItemAnchor( $id, $title )
    {
        return sprintf(
            '<a href="%1$s" title="%2$s">' . $title . '</a>',
            esc_url( $this->createTOCItemURL( $id ) ),
            esc_attr( strip_tags( $title ) )
        );
    }


    /**
     * @access private
     * @since  2.0
     *
     * @param string $id
     * @param int    $page
     *
     * @return string
     */
    private function createTOCItemURL( $id )
    {
        return '#' . $id;
    }

}