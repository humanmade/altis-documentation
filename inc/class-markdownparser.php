<?php

namespace HM\Platform\Documentation;

use Parsedown;
use WP_Http;

class MarkdownParser extends Parsedown {
	/**
	 * Current page being parsed.
	 *
	 * @var Page
	 */
	protected $current_page;

	/**
	 * Constructor.
	 *
	 * @param Page $page Current page, used to contextualise relative references.
	 * @param string $root Root directory for documentation.
	 */
	public function __construct( Page $page ) {
		$this->current_page = $page;
	}

	/**
	 * Parse an inline link.
	 *
	 * Overridden to resolve relative URLs based on the current page.
	 *
	 * @param mixed $data
	 * @return array
	 */
    protected function inlineLink( $data ) {
		$result = parent::inlineLink( $data );
		if ( $result['element']['name'] !== 'a' ) {
			return $result;
		}

		$href = $result['element']['attributes']['href'] ?? null;
		if ( empty( $href ) ) {
			return $result;
		}

		// Is the link relative?
		$parts = wp_parse_url( $href );
		if ( ! empty( $parts['host'] ) ) {
			return $result;
		}

		// Resolve relative to the current file.
		$base = $this->current_page->get_meta( 'path' );
		$root= $this->current_page->get_meta( 'root' );
		$resolved = realpath( path_join( dirname( $base ), $href ) );
		if ( empty( $resolved ) ) {
			return $result;
		}

		// Override href.
		$slug = get_slug_from_path( $root, $resolved );
		$url = add_query_arg( [ 'id' => urlencode( $slug ) ] );
		$result['element']['attributes']['href'] = $url;

		return $result;
	}

	/**
	 * Parse a header block
	 *
	 * Override the blockHeader method to add anchor links.
	 *
	 * @param array $data
	 * @return array
	 */
	protected function blockHeader( $data ) {
		$block = parent::blockHeader( $data );
		$id = str_replace( ' ', '-', strtolower( strip_tags( $block['element']['handler']['argument'] ) ) );
		return [
			'element' => [
				'name' => 'a',
				'attributes' => [
					'href' => '#' . $id,
					'id' => $id,
				],
				'elements' => [ $block ]
			]
		];
	}
}
