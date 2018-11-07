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


    public function prepareHTags() {
        global $post;

        $hTagsFromDB = get_post_meta( $post->ID, '_zwivel-toc-h-tags', TRUE );
        $hTags = [];

        for ($i = 0; $i < count($hTagsFromDB['headings']); $i++) {
            $hTags[$i]['heading'] = $hTagsFromDB['headings'][$i];
        }

        for ($i = 0; $i < count($hTagsFromDB['values']); $i++) {
            $hTags[$i]['value'] = $hTagsFromDB['values'][$i];
        }

        for ($i = 0; $i < count($hTagsFromDB['default_values']); $i++) {
            $hTags[$i]['default_value'] = $hTagsFromDB['default_values'][$i];
        }

        for ($i = 0; $i < count($hTagsFromDB['ids']); $i++) {
            $hTags[$i]['id'] = $hTagsFromDB['ids'][$i];
        }

        return $hTags;
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


}