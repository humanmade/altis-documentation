<?php

namespace HM\Platform\Documentation;

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
	 * @param string $title Group title
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
	 * @param string $id Page ID
	 * @param Page $page
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
		return $this->pages;
	}

	/**
	 * Get a single page by ID.
	 *
	 * @return Page|null Page if set, null otherwise.
	 */
	public function get_page( $id ) : ?Page {
		return $this->pages[ $id ] ?? null;
	}
}
