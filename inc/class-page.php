<?php
/**
 * Altis Documentation Page.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation;

/**
 * Altis Documentation Page Object.
 *
 * @package altis/documentation
 */
class Page {
	/**
	 * Page content.
	 *
	 * Raw content for the page in Markdown format.
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Properties for the page.
	 *
	 * Metadata properties for the page, typically parsed from the file's
	 * header or filesystem information.
	 *
	 * @var array
	 */
	protected $meta;

	/**
	 * Subpages of the page.
	 *
	 * @var Page[]
	 */
	protected $subpages = [];

	/**
	 * Constructor.
	 *
	 * @param string $content Raw content of the page.
	 */
	public function __construct( string $content ) {
		$this->content = $content;
	}

	/**
	 * Get the raw page content.
	 *
	 * @return string Raw Markdown content of the page.
	 */
	public function get_content() : string {
		return $this->content;
	}

	/**
	 * Get the metadata for the page.
	 *
	 * @return array
	 */
	public function get_all_meta() : array {
		return $this->meta;
	}

	/**
	 * Get a single metadata value.
	 *
	 * @param string $id Metadata field ID.
	 * @return mixed|null Field value if available, otherwise null.
	 */
	public function get_meta( $id ) {
		return $this->meta[ $id ] ?? null;
	}

	/**
	 * Set the metadata for the page.
	 *
	 * @param array $meta Map of metadata properties to set.
	 */
	public function set_all_meta( array $meta ) {
		$this->meta = $meta;
	}

	/**
	 * Set page meta data by key.
	 *
	 * @param string $key The metadata key.
	 * @param mixed $value The value for the metadata.
	 * @return void
	 */
	public function set_meta( string $key, $value ) {
		$this->meta[ $key ] = $value;
	}

	/**
	 * Add subpage to the page.
	 *
	 * @param string $id Page ID.
	 * @param Page $page The page object.
	 */
	public function add_subpage( string $id, Page $page ) {
		$this->subpages[ $id ] = $page;
	}

	/**
	 * Get all pages which belong to the page.
	 *
	 * @return Page[]
	 */
	public function get_subpages() : array {
		return $this->subpages;
	}

	/**
	 * Get a single subpage by ID.
	 *
	 * @param string $id The subpage ID to find.
	 * @return Page|null Page if set, null otherwise.
	 */
	public function get_subpage( string $id ) : ?Page {
		return $this->subpages[ $id ] ?? null;
	}
}
