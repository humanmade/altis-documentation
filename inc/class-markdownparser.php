<?php
/**
 * Altis Documentation Markdown Parser.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation;

use Parsedown;

/**
 * Altis Documentation MarkdownParser Object.
 *
 * @package altis/documentation
 */
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
	 */
	public function __construct( Page $page ) {
		$this->current_page = $page;
	}

	/**
	 * Parse an inline image.
	 *
	 * Overridden to resolve relative URLs based on the current page.
	 *
	 * @param mixed $data Raw data from the Markdown tokeniser.
	 * @return array
	 */
	protected function inlineImage( $data ) {
		$result = parent::inlineImage( $data );
		if ( empty( $result ) ) {
			return $result;
		}

		// Re-parse the link data.
		$link = $data;
		$link['text'] = substr( $link['text'], 1 );
		$link_result = parent::inlineLink( $link );
		if ( empty( $link_result ) ) {
			return $result;
		}

		$src = $link_result['element']['attributes']['href'];

		// Is the link relative?
		$parts = wp_parse_url( $src );
		if ( ! empty( $parts['host'] ) ) {
			return $result;
		}

		// Resolve relative to the current file.
		$base = $this->current_page->get_meta( 'path' );
		$root = $this->current_page->get_meta( 'root' );
		$resolved = realpath( path_join( dirname( $base ), $parts['path'] ) );
		if ( empty( $resolved ) ) {
			return $result;
		}

		// Override src.
		$slug = get_slug_from_path( $root, $resolved );
		$result['element']['attributes']['src'] = plugins_url( $slug, $root . '/wp-is-dumb' );

		return $result;
	}

	/**
	 * Parse an inline link.
	 *
	 * Overridden to resolve relative URLs based on the current page.
	 *
	 * @param mixed $data Raw data from the Markdown tokeniser.
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
		if ( ! empty( $parts['scheme'] ) ) {
			$new_url = convert_internal_link( $href );

			// Allow shortcircuiting (e.g. for public display)
			if ( $new_url === null ) {
				$result['element']['name'] = 'span';
				$result['element']['attributes'] = [];
				return $result;
			}

			if ( $new_url !== $href ) {
				$result['element']['attributes']['href'] = $new_url;
			}
			return $result;
		}

		if ( ! empty( $parts['host'] ) ) {
			return $result;
		}

		// Is this a fragment link?
		if ( empty( $parts['path'] ) ) {
			return $result;
		}

		// Resolve relative to the current file.
		$base = $this->current_page->get_meta( 'path' );
		$root = $this->current_page->get_meta( 'root' );
		$resolved = realpath( path_join( dirname( $base ), $parts['path'] ) );
		if ( empty( $resolved ) ) {
			return $result;
		}

		// Override href.
		$slug = get_slug_from_path( $root, $resolved );
		$url = get_url_for_page( UI\get_current_group_id(), $slug );
		if ( ! empty( $parts['fragment'] ) ) {
			$url .= '#' . $parts['fragment'];
		}
		$result['element']['attributes']['href'] = $url;

		return $result;
	}

	/**
	 * Generate an internal link to another module.
	 *
	 * This handles generating links across modules. It transforms links in
	 * the format `docs://group/id` to `?group=&id=` internal URLs.
	 *
	 * @param array $result Parsed result from parent::inlineLink.
	 * @param array $parts Parsed data from the link's HREF.
	 * @return array Result with modified URL
	 */
	protected function inlineInternalLink( $result, $parts ) {
		$group = $parts['host'];
		$path = $parts['path'];

		// Override href.
		$slug = get_slug_from_path( '', $path );
		$url = get_url_for_page( $group, $slug );
		if ( ! empty( $parts['fragment'] ) ) {
			$url .= '#' . $parts['fragment'];
		}
		$result['element']['attributes']['href'] = $url;

		return $result;
	}

	/**
	 * Parse a header block
	 *
	 * Override the blockHeader method to add anchor links.
	 *
	 * @param array $data Element data.
	 * @return array
	 */
	protected function blockHeader( $data ) {
		$block = parent::blockHeader( $data );
		$id = sanitize_title_with_dashes( $block['element']['text'] );

		// Use a manual ID if provided.
		if ( preg_match( '/^(.+) ?\{#(.+?)\}$/', $block['element']['text'], $matches ) ) {
			$id = $matches[2];
			$block['element']['text'] = $matches[1];
		}

		return [
			'element' => [
				'name' => 'a',
				'attributes' => [
					'href' => '#' . $id,
					'id' => $id,
					'class' => 'header-anchor',
				],
				'handler' => 'elements',
				'text' => [ $block['element'] ],
			],
		];
	}
}
