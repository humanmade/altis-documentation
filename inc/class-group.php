<?php
/**
 * Altis Documentation Group.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation;

/**
 * Altis Documentation Group Object.
 *
 * @package altis/documentation
 */
class Group {
	/**
	 * Group title (typically a module name)
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Pages which belong to the group.
	 *
	 * @var Page[]
	 */
	protected $pages = [];

	/**
	 * Constructor.
	 *
	 * @param string $title Group title.
	 */
	public function __construct( string $title ) {
		$this->title = $title;
	}

	/**
	 * Get the group title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Add page to the documentation group.
	 *
	 * @param string $id Page ID.
	 * @param Page $page Page object.
	 */
	public function add_page( string $id, Page $page ) {
		$this->pages[ $id ] = $page;
	}

	/**
	 * Get all pages which belong to the group.
	 *
	 * @return Page[]
	 */
	public function get_pages() : array {
		// Sort pages by their order.
		$pages = $this->pages;
		uasort( $pages, function ( Page $a, Page $b ) {
			$order_a = $a->get_meta( 'order' ) ?? 0;
			$order_b = $b->get_meta( 'order' ) ?? 0;

			return $order_a <=> $order_b;
		} );

		return $pages;
	}

	/**
	 * Get a single page by ID.
	 *
	 * @param string $id The page path, also used as an ID.
	 * @return Page|null Page if set, null otherwise.
	 */
	public function get_page( $id ) : ?Page {
		// Parse IDs with slashes to be subpages. code-review/process.md means
		// "get the page with id: code-review, then get a subpage of code-review
		// with id: process.md".
		$parts = explode( '/', $id );
		$id    = array_shift( $parts );
		$page  = $this->pages[ $id ] ?? null;

		if ( ! $page ) {
			return null;
		}

		$current_path = $id;
		// Crawl through all the url parts (seperated by /) to get the
		// subpage from the parent at each step.
		while ( count( $parts ) > 0 ) {
			$subpage_id = array_shift( $parts );
			$current_path = $current_path . '/' . $subpage_id;
			$page = $page->get_subpage( $current_path );
		}

		return $page;
	}
}
