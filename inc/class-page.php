<?php

namespace HM\Platform\Documentation;

class Page {
	/**
	 * Page content.
	 *
	 * Raw content for the page in Markdown format.
	 *
	 * @param string
	 */
	protected $content;

	/**
	 * Properties for the page.
	 *
	 * Metadata properties for the page, typically parsed from the file's
	 * header or filesystem information.
	 *
	 * @param string
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 * @param string $content Raw content of the page
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

	public function set_meta( string $key, $value ) {
		$this->meta[ $key ] = $value;
	}
}
