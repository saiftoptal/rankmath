<?php

/**
 * This class provides necessary helper methods for the entire plugin
 */

class Rms_Helper_Controller {

	/**
	 * Since get_page_by_title() function is depricated in WordPress version of 6.2
	 */

	public static function get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) : ?object {
		$args  = array(
			'title'                  => $page_title,
			'post_type'              => $post_type,
			'post_status'            => get_post_stati(),
			'posts_per_page'         => 1,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'no_found_rows'          => true,
			'orderby'                => 'post_date ID',
			'order'                  => 'ASC',
		);
		$query = new WP_Query( $args );
		$pages = $query->posts;

		if ( empty( $pages ) ) {
			return null;
		}

		return get_post( $pages[0], $output );
	}
}