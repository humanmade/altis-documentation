<?php
/**
 * Altis Documentation.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation;

use Altis;
use Altis\Module;
use DirectoryIterator;
use Spyc;

/**
 * Register module.
 */
function register() {
	Altis\register_module(
		'documentation',
		DIRECTORY,
		'Documentation',
		[
			'enabled' => true,
		],
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {
	UI\bootstrap();
}

/**
 * Get all documentation groups.
 *
 * @return Group[] Sorted list of groupse
 */
function get_documentation() : array {
	static $docs;

	if ( ! empty( $docs ) ) {
		return $docs;
	}

	$modules = Module::get_all();

	$other_docs = dirname( __DIR__ ) . '/other-docs';
	$docs = [
		'welcome' => new Group( 'Welcome' ),
		'getting-started' => new Group( 'Getting Started' ),
		'guides' => new Group( 'Guides' ),
	];
	$docs['welcome']->add_page( '', parse_file( $other_docs . '/welcome.md', $other_docs ) );
	add_docs_for_group( $docs['getting-started'], $other_docs . '/getting-started' );
	add_docs_for_group( $docs['guides'], $other_docs . '/guides' );

	uasort( $modules, function ( Module $a, Module $b ) : int {
		return $a->get_title() <=> $b->get_title();
	} );

	foreach ( $modules as $id => $module ) {
		$module_docs = generate_docs_for_module( $id, $module );
		if ( empty( $module_docs ) ) {
			continue;
		}

		$docs[ $id ] = $module_docs;
	}

	/**
	 * Filter available documentation groups.
	 *
	 * This allows modules to register additional documentation groups.
	 *
	 * @param Group[] $docs Map of group ID to Group object.
	 */
	$docs = apply_filters( 'altis.documentation.groups', $docs );

	return $docs;
}

/**
 * Get a specific documentation page by ID.
 *
 * @param string $group Group ID.
 * @param string $id Page ID.
 * @return Page|null Page if available.
 */
function get_page_by_id( string $group, string $id ) {
	$documentation = get_documentation();
	if ( empty( $documentation[ $group ] ) ) {
		return null;
	}

	return $documentation[ $group ]->get_page( $id );
}

/**
 * Generate documentation for a module.
 *
 * @param string $id Module ID.
 * @param Module $module Module object.
 * @return Group Documentation group for the module.
 */
function generate_docs_for_module( $id, Module $module ) : ?Group {
	$doc_dir = realpath( path_join( $module->get_directory(), 'docs' ) );
	if ( ! file_exists( $doc_dir ) ) {
		// Skip this module.
		return null;
	}

	$group = new Group( $module->get_title() );

	return add_docs_for_group( $group, $doc_dir );
}

/**
 * Generate documentation for a module.
 *
 * @param Group $group Group object.
 * @param string $doc_dir The directory to add files from to the Group.
 * @return Group Documentation group for the module.
 */
function add_docs_for_group( Group $group, string $doc_dir ) : Group {
	// Generate objects for each file.
	$iterator = new DirectoryIterator( $doc_dir );
	foreach ( $iterator as $leaf ) {
		/** @var \SplFileInfo $leaf */
		if ( $leaf->isDir() ) {
			if ( $leaf->isDot() ) {
				continue;
			}

			// Special handling for sub dirs, to add a page (and subpages).
			$doc = get_page_for_dir( $leaf->getPathname(), $doc_dir );
			if ( empty( $doc ) ) {
				continue;
			}

			$group->add_page( get_slug_from_path( $doc_dir, $leaf->getPathname() ), $doc );
			continue;
		}

		if ( $leaf->getExtension() !== 'md' ) {
			continue;
		}

		$file = $leaf->getRealPath();
		$doc = parse_file( $file, $doc_dir );
		$out_path = get_slug_from_path( $doc_dir, $file );
		$group->add_page( $out_path, $doc );
	}

	return $group;
}

/**
 * Get a page for a given directory.
 *
 * This will recurse into sub directories, adding all those as subpages of the Page object.
 *
 * @param string $dir       The directory to add the page for.
 * @param string $root_dir  The root directory of the group, used to calculate page ids.
 * @return Page|null
 */
function get_page_for_dir( string $dir, string $root_dir ) : ?Page {
	// A directory's page is always build from a README.md inside the dir.
	$readme = $dir . '/README.md';
	$doc = file_exists( $readme ) ? parse_file( $readme, $root_dir ) : new Page( '' );
	$has_subpages = false;

	$iterator = new DirectoryIterator( $dir );
	foreach ( $iterator as $leaf ) {
		/** @var \SplFileInfo $leaf */
		if ( $leaf->isDir() ) {
			if ( $leaf->isDot() ) {
				continue;
			}

			// Recurse directories, recursively calling this function.
			$subpage = get_page_for_dir( $leaf->getPathname(), $root_dir );
			if ( empty( $subpage ) ) {
				continue;
			}
		} elseif ( $leaf->getExtension() !== 'md' ) {
			continue;
		} elseif ( $leaf->getFilename() === 'README.md' ) {
			continue;
		} else {
			$subpage = parse_file( $leaf->getPathname(), $root_dir );
		}

		$has_subpages = true;
		$doc->add_subpage( get_slug_from_path( $root_dir, $leaf->getPathname() ), $subpage );
	}

	// If we don't have any actual files, bail.
	if ( ! file_exists( $readme ) && ! $has_subpages ) {
		return null;
	}

	return $doc;
}

/**
 * Convert a (relative) Markdown path to a slug.
 *
 * @param string $root A root path to match against the relative path.
 * @param string $path Path for Markdown file (relative to documentation root).
 * @return string Hierarchical slug for the document.
 */
function get_slug_from_path( $root, $path ) {
	if ( substr( $path, 0, strlen( $root ) ) !== $root ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( sprintf( 'Relative path %s is not within root %s', $path, $root ), E_USER_WARNING );
		return $path;
	}

	$out_path = substr( $path, strlen( $root ) );
	return trim( preg_replace( '/README\.md/i', '', $out_path ), '/' );
}

/**
 * Get a documentation group.
 *
 * @param string $id Group ID.
 * @return Group|null Group if available, null otherwise.
 */
function get_documentation_group( $id ) : ?Group {
	$docs = get_documentation();
	return $docs[ $id ] ?: null;
}

/**
 * Parse Markdown file into a Page.
 *
 * @param string $file Path to the file to parse.
 * @param string $root Root directory for the file's owner.
 * @return Page Parsed data for the file
 */
function parse_file( string $file, string $root ) : Page {
	// @codingStandardsIgnoreLine
	$raw = file_get_contents( $file );

	// Find YAML frontmatter.
	$meta = [];
	preg_match( '#^---(.+)---\n+#Us', $raw, $yaml_matches );
	if ( $yaml_matches ) {
		$meta = Spyc::YAMLLoadString( $yaml_matches[1] );
		// Strip YAML doc from the header.
		$raw = substr( $raw, strlen( $yaml_matches[0] ) );
	}

	// Default title if not set in YAML.
	if ( empty( $meta['title'] ) && preg_match( '/^\n*#\s([^\n]+)/', $raw, $matches ) ) {
		$meta['title'] = $matches[1];
	}

	$data = new Page( $raw );
	if ( ! empty( $meta ) ) {
		$data->set_all_meta( $meta );
	}
	$data->set_meta( 'path', $file );
	$data->set_meta( 'root', $root );

	return $data;
}

/**
 * Render a page from Markdown.
 *
 * @param Page $page Page to render.
 * @return string HTML content for the page.
 */
function render_page( Page $page ) : string {
	$parsedown = new MarkdownParser( $page );
	return $parsedown->text( $page->get_content() );
}

/**
 * Get the URL to view a given page.
 *
 * @param string $group_id Group ID.
 * @param string $page_id Page ID.
 * @return string Absolute URL to the page.
 */
function get_url_for_page( $group_id, $page_id ) {
	$base_url = admin_url( 'admin.php' );
	$args = [
		'page' => UI\PAGE_SLUG,
		'group' => $group_id,
		'id' => $page_id,
	];
	$url = add_query_arg( urlencode_deep( $args ), $base_url );

	/**
	 * Filter generated URL for a page.
	 *
	 * @param string $url Default generated URL.
	 * @param string $group_id Group ID.
	 * @param string $page_id Page ID.
	 */
	return apply_filters( 'altis.documentation.url_for_page', $url, $group_id, $page_id );
}

/**
 * Convert an internal link (e.g. docs://foo/bar) to a usable URL.
 *
 * Note that relative links are not resolved, as there is no context to use.
 *
 * @param string $url Raw link (e.g. `docs://foo/bar`).
 * @return string URL for usage in a browser.
 */
function convert_internal_link( $url ) {
	$parts = wp_parse_url( $url );
	if ( empty( $parts['scheme'] ) ) {
		return $url;
	}

	$host = $parts['host'] ?? '';
	$path = $parts['path'] ?? '';

	switch ( $parts['scheme'] ) {
		case 'docs':
			// Override href.
			$slug = get_slug_from_path( '', $path );
			$new_url = get_url_for_page( $host, $slug );
			break;

		case 'internal':
			$map = [
				'home' => 'home_url',
				'site' => 'site_url',
				'admin' => 'admin_url',
				'network-admin' => 'network_admin_url',
			];
			$function = $map[ $host ] ?? null;
			if ( empty( $function ) ) {
				return $url;
			}

			$new_url = call_user_func( $function, $path );
			if ( ! empty( $parts['query'] ) ) {
				$new_url .= '?' . $parts['query'];
			}
			break;

		default:
			return $url;
	}

	if ( ! empty( $parts['fragment'] ) ) {
		$new_url .= '#' . $parts['fragment'];
	}

	/**
	 * Filter generated URL for internal links.
	 *
	 * @param string $new_url Full URL after scheme parsing.
	 * @param string $url Original URL supplied by the user.
	 */
	return apply_filters( 'altis.documentation.internal_link', $new_url, $url );
}
