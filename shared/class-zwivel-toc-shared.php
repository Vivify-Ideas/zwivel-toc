<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.vivifyideas.com/
 * @since      1.0.0
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/shared
 */

/**
 * The shared functionality of the plugin.
 *
 * @package    Zwivel_Toc
 * @subpackage Zwivel_Toc/shared
 * @author     VivifyIdeas <contact@vivifyideas.com>
 */
class Zwivel_Toc_Shared
{

    public $headings;

    public function __construct()
    {

    }


    public function extractHeadings($content)
    {
        $matches = $this->extractHeadingsFromHTML($content);
        $matches = $this->removeEmptyHeadings($matches);

        return $this->headingIDs($matches);
    }


    public function extractHeadingsFromHTML($content)
    {
        $matches = array();

        preg_match_all('/(<h([1-6]{1})[^>]*>)(.*)<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER);

        return $matches;
    }


    public function removeEmptyHeadings(&$matches)
    {
        $new_matches = array();
        $count = count($matches);

        for ($i = 0; $i < $count; $i++) {

            if (trim(strip_tags($matches[$i][0])) != FALSE) {

                $new_matches[] = $matches[$i];
            }
        }

        if (count($matches) != count($new_matches)) {

            $matches = $new_matches;
        }

        return $matches;
    }

    public function removeHeadingsDeselectedInSettings($hTags)
    {
        $tocSettings = get_option('zwivel-toc-settings');

        $headingTagsToExclude = array();
        foreach ($tocSettings as $key => $tocSetting) {
            if (empty($tocSetting)) {
                array_push($headingTagsToExclude, $key);
            }
        }

        $finalHeadings = array();
        foreach ($hTags as $hTag) {
            if (!in_array($hTag['heading'], $headingTagsToExclude)) {
                array_push($finalHeadings, $hTag);
            }
        }

        return $finalHeadings;
    }


    public function headingIDs(&$matches)
    {
        $count = count($matches);

        for ($i = 0; $i < $count; $i++) {

            $matches[$i]['id'] = $this->generateHeadingIDFromTitle($matches[$i][0]);
        }

        return $matches;
    }


    public function generateHeadingIDFromTitle($heading)
    {

        $return = FALSE;

        if ($heading) {

            // WP entity encodes the post content.
            $return = html_entity_decode($heading, ENT_QUOTES, get_option('blog_charset'));

            $return = trim(strip_tags($return));

            // Convert accented characters to ASCII.
            $return = remove_accents($return);

            // replace newlines with spaces (eg when headings are split over multiple lines)
            $return = str_replace(array("\r", "\n", "\n\r", "\r\n"), ' ', $return);

            // Remove `&amp;` and `&nbsp;` NOTE: in order to strip "hidden" `&nbsp;`,
            // title needs to be converted to HTML entities.
            // @link https://stackoverflow.com/a/21801444/5351316
            $return = htmlentities2($return);
            $return = str_replace(array('&amp;', '&nbsp;'), ' ', $return);
            $return = html_entity_decode($return, ENT_QUOTES, get_option('blog_charset'));

            // remove non alphanumeric chars
            $return = preg_replace('/[^a-zA-Z0-9 \-_]*/', '', $return);

            // convert spaces to _
            $return = preg_replace('/\s+/', '-', $return);

            // remove trailing - and _
            $return = rtrim($return, '-_');

            // lowercase everything
            $return = strtolower($return);
        }

        return $return;
    }


    public function getHeadings()
    {
        $headings = array();

        if (isset($this->headings)) {

            $headings = wp_list_pluck($this->headings, 0);
        }

        return $headings;
    }


    public function getHeadingsWithAnchors() {
        $headings = array();

        if ( isset( $this->headings ) ) {

            $matches = $this->headings;
            $count   = count( $matches );

            for ( $i = 0; $i < $count; $i++ ) {

                $anchor     = $matches[ $i ]['id'];
                $headings[] = str_replace(
                    array(
                        $matches[ $i ][1],                // start of heading
                        '</h' . $matches[ $i ][2] . '>'   // end of heading
                    ),
                    array(
                        $matches[ $i ][1] . '<span class="zwivel-toc-section" id="' . $anchor . '">',
                        '</span></h' . $matches[ $i ][2] . '>'
                    ),
                    $matches[ $i ][0]
                );
            }
        }

        return $headings;
    }


    public function prepareHTags($tagsFromDb) {
        $hTags = [];

        for ($i = 0; $i < count($tagsFromDb['headings']); $i++) {
            $hTags[$i]['heading'] = $tagsFromDb['headings'][$i];
        }

        for ($i = 0; $i < count($tagsFromDb['values']); $i++) {
            $hTags[$i]['value'] = $tagsFromDb['values'][$i];
        }

        for ($i = 0; $i < count($tagsFromDb['default_values']); $i++) {
            $hTags[$i]['default_value'] = $tagsFromDb['default_values'][$i];
        }

        for ($i = 0; $i < count($tagsFromDb['ids']); $i++) {
            $hTags[$i]['id'] = $tagsFromDb['ids'][$i];
        }

        if (isset($tagsFromDb['exclude'])) {
            for ($i = 0; $i < count($tagsFromDb['exclude']); $i++) {
                $hTags[$i]['exclude'] = $tagsFromDb['exclude'][$i];
            }
        }

        return $hTags;
    }


    public function getTOC($hTags)
    {
        $html = '';

        // add container, toc title and list items
        $html .= '<div id="zwivel-toc-container" class="sidebar-widget zw-toc-container clearfix">' . PHP_EOL;

        $html .= '<div class="widget-title-wrapper zwivel-toc-title-container">' . PHP_EOL;

        $html .= '<h3 class="widget-title zwivel-toc-title">CONTENTS</h3>' . PHP_EOL;

        $html .= '</div>' . PHP_EOL;

        ob_start();
        $html .= ob_get_clean();
        $html .= $this->getTOCList($hTags);

        ob_start();
        $html .= ob_get_clean();
        $html .= '</div>' . PHP_EOL;

        return $html;

    }

    public function getTOCList($hTags)
    {
        $html = '';

        $html .= $this->createTOC( $hTags );
        $html  = '<ul class="toc_widget_list no_bullets zw-toc-list">' . $html . '</ul>';

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
    public function createTOC( $hTags )
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

            if (!empty($hTags[$i]['exclude']) && $hTags[$i]['exclude'] != 0) {
                continue;
            }

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


    /**
     * Returns a string with all items from the $find array replaced with their matching
     * items in the $replace array.  This does a one to one replacement (rather than globally).
     *
     * This function is multibyte safe.
     *
     * $find and $replace are arrays, $string is the haystack.  All variables are passed by reference.
     *
     * @access private
     * @since  1.0
     * @static
     *
     * @param bool   $find
     * @param bool   $replace
     * @param string $string
     *
     * @return mixed|string
     */
    public static function mb_find_replace( &$find = FALSE, &$replace = FALSE, &$string = '' ) {

        if ( is_array( $find ) && is_array( $replace ) && $string ) {

            // check if multibyte strings are supported
            if ( function_exists( 'mb_strpos' ) ) {

                for ( $i = 0; $i < count( $find ); $i ++ ) {

                    $string = mb_substr(
                            $string,
                            0,
                            mb_strpos( $string, $find[ $i ] )
                        ) .    // everything before $find
                        $replace[ $i ] . // its replacement
                        mb_substr(
                            $string,
                            mb_strpos( $string, $find[ $i ] ) + mb_strlen( $find[ $i ] )
                        )    // everything after $find
                    ;
                }

            } else {

                for ( $i = 0; $i < count( $find ); $i ++ ) {

                    $string = substr_replace(
                        $string,
                        $replace[ $i ],
                        strpos( $string, $find[ $i ] ),
                        strlen( $find[ $i ] )
                    );
                }
            }
        }

        return $string;
    }

    /**
     * Wrapper function used to update TOC meta fields
     *
     * @param $post
     * @param $formattedHeadingData
     */
    public function updateTocMetaFields($post, $formattedHeadingData) {
        if (empty($formattedHeadingData)) {
            update_post_meta( $post->ID, '_zwivel-toc-off', 1 );
        } else {
            update_post_meta($post->ID, '_zwivel-toc-h-tags', $formattedHeadingData);
        }
    }

}