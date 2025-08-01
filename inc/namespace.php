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

const DEV_DOCS_SET_ID = 'dev-docs';
const USER_DOCS_SET_ID = 'user-docs';

/**
 * Register module.
 */
function register() {
	Altis\register_module(
		'documentation',
		DIRECTORY,
		'Documentation',
		[
			'defaults' => [
				'enabled' => true,
			],
		],
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {
	// Only add dev docs on dev/local.
	if ( in_array( Altis\get_environment_type(), [ 'development', 'local' ], true ) ) {
		add_filter( 'altis.documentation.sets', __NAMESPACE__ . '\filter_add_dev_docs_set' );
	}

	// Always add user docs set.
	add_filter( 'altis.documentation.sets', __NAMESPACE__ . '\\filter_add_user_docs_set' );

	UI\bootstrap();
}

/**
 * Return the list of all sets. Filtered on hook 'altis.documentation.sets'.
 *
 * @return Set[] array
 */
function get_documentation_sets() : array {
	/**
	 * All the documentation sets.
	 *
	 * @var Set[] $_all_sets All the documentation sets.
	 */
	static $_all_sets = [];

	/**
	 * Filter available documentation sets.
	 *
	 * This allows modules to register additional documentation sets or replace the requested set.
	 *
	 * @param Set[] $doc_sets Map of set ID to Set object.
	 */
	return apply_filters( 'altis.documentation.sets', $_all_sets );
}

/**
 * Get the required documentation set.
 * Returns an empty set if not found.
 *
 * @param string $set_id The required set id.
 * @return Set The required Documentation Set or a new empty one.
 */
function get_documentation_set( string $set_id ) : Set {
	$sets = get_documentation_sets();

	if ( ! empty( $sets[ $set_id ] ) ) {
		return $sets[ $set_id ];
	}

	return new Set();
}

/**
 * Get the default set id (the first one registered).
 *
 * @return string
 */
function get_default_set_id() : string {
	return array_keys( get_documentation_sets() )[0];
}

/**
 * Check if the passed string is a set id.
 *
 * @param string $id The potential set id.
 * @return bool
 */
function is_set_id( string $id ) : bool {
	return get_documentation_set( $id )->get_id() !== '';
}

/**
 * Filter the $all_sets array to add the developer docs Set if it doesn't yet exist.
 *
 * @param Set[] $sets The array of Documentation sets.
 * @return Set[] array
 */
function filter_add_dev_docs_set( array $sets ) : array {

	// Are we already set up?
	if ( ! empty( $sets[ DEV_DOCS_SET_ID ] ) ) {
		return $sets;
	}

	// Generate the default set.
	$dev_set = new Set( DEV_DOCS_SET_ID, __( 'Developer Documentation', 'altis' ) );

	$other_docs = dirname( __DIR__ ) . '/other-docs';

	$welcome = new Group( __( 'Welcome', 'altis' ) );
	$welcome->add_page( '', parse_file( $other_docs . '/welcome.md', $other_docs ) );
	$dev_set->add_group( 'welcome', $welcome );
	$dev_set->set_default_group_id( 'welcome' );

	$getting_started = new Group( __( 'Getting Started', 'altis' ) );
	add_docs_for_group( $getting_started, $other_docs . '/getting-started' );
	$dev_set->add_group( 'getting-started', $getting_started );

	$guides = new Group( __( 'Guides', 'altis' ) );
	add_docs_for_group( $guides, $other_docs . '/guides' );
	$dev_set->add_group( 'guides', $guides );

	// Add all the registered modules.
	$modules = Module::get_all();
	uasort( $modules, function ( Module $a, Module $b ) : int {
		return $a->get_title() <=> $b->get_title();
	} );

	foreach ( $modules as $id => $module ) {
		$module_docs = generate_docs_for_module( $id, $module );
		if ( $module_docs === null ) {
			continue;
		}

		$dev_set->add_group( $id, $module_docs );
	}

	// Add the FAQs at the end.
	$faq = new Group( __( 'FAQ', 'altis' ) );
	add_docs_for_group( $faq, $other_docs . '/faq' );
	$dev_set->add_group( 'faq', $faq );

	$sets[ DEV_DOCS_SET_ID ] = $dev_set;

	return $sets;
}

/**
 * Filter the $all_sets array to add the user guides Set if it doesn't yet exist.
 *
 * @param Set[] $sets The array of Documentation sets.
 * @return Set[] array
 */
function filter_add_user_docs_set( array $sets ) : array {

	// Are we already set up?
	if ( ! empty( $sets[ USER_DOCS_SET_ID ] ) ) {
		return $sets;
	}

	// Generate the default set.
	$user_set = new Set( USER_DOCS_SET_ID, __( 'User Guides', 'altis' ) );
	$user_set->set_default_group_id( 'documentation' );

	// Add all the registered modules.
	$modules = Module::get_all();
	uasort( $modules, function ( Module $a, Module $b ) : int {
		return $a->get_title() <=> $b->get_title();
	} );

	// Force docs module to the top.
	unset( $modules['documentation'] );
	$modules = array_merge( [ 'documentation' => Module::get( 'documentation' ) ], $modules );

	foreach ( $modules as $id => $module ) {
		$module_docs = generate_docs_for_module( $id, $module, 'user-docs' );
		if ( $module_docs === null ) {
			continue;
		}

		$user_set->add_group( $id, $module_docs );
	}

	$sets[ USER_DOCS_SET_ID ] = $user_set;

	return $sets;
}

/**
 * Generate documentation for a module.
 *
 * @param string $id Module ID.
 * @param Module $module Module object.
 * @param string $path The path to docs in the module.
 * @return Group Documentation group for the module.
 */
function generate_docs_for_module( $id, Module $module, string $path = 'docs' ) : ?Group {
	$doc_dir = realpath( path_join( $module->get_directory(), $path ) );
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
		/**
		 * Current iterator file object.
		 *
		 * @var \SplFileInfo $leaf
		 */
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

		// If this is the readme.md file, update the group's title.
		if ( strtolower( $leaf->getBasename() ) === 'readme.md' ) {
			$title = $doc->get_meta( 'title' );
			if ( ! empty( $title ) ) {
				$group->set_title( $title );
			}
		}

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
 * @param string $dir The directory to add the page for.
 * @param string $root_dir The root directory of the group, used to calculate page ids.
 * @return Page|null
 */
function get_page_for_dir( string $dir, string $root_dir ) : ?Page {
	// A directory's page is always build from a README.md inside the dir.
	$readme = $dir . '/README.md';
	$doc = file_exists( $readme ) ? parse_file( $readme, $root_dir ) : new Page( '' );
	$has_subpages = false;

	$iterator = new DirectoryIterator( $dir );
	foreach ( $iterator as $leaf ) {
		/**
		 * Current iterator file object.
		 *
		 * @var \SplFileInfo $leaf
		 */
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
function get_slug_from_path( string $root, string $path ) : string {
	if ( substr( $path, 0, strlen( $root ) ) !== $root ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( sprintf( 'Relative path %s is not within root %s', $path, $root ), E_USER_WARNING );

		return $path;
	}

	$out_path = substr( $path, strlen( $root ) );

	return trim( preg_replace( '/README\.md/i', '', $out_path ), '/' );
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
		// This library seems to have problems with its autoload configuration
		// sometimes so we need to ensure it's available.
		if ( ! class_exists( 'Spyc' ) ) {
			require_once Altis\ROOT_DIR . '/vendor/mustangostang/spyc/Spyc.php';
		}
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
 * @param string $set_id Set ID.
 *
 * @return string Absolute URL to the page.
 */
function get_url_for_page( string $group_id, string $page_id, string $set_id = '' ) : string {
	$base_url = admin_url( 'admin.php' );

	// Default to whatever is the default set.
	if ( empty( $set_id ) ) {
		$set_id = Altis\Documentation\UI\get_current_set_id();
	}

	// Set the admin url appropriately.
	if ( $set_id === Altis\Documentation\get_default_set_id() ) {
		$page_slug = UI\PAGE_SLUG;
	} else {
		$page_slug = sprintf( '%s-%s', UI\PAGE_SLUG, $set_id );
	}

	$args = [
		'page' => $page_slug,
		'group' => $group_id,
		'id' => $page_id,
		'set' => $set_id,
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
function convert_internal_link( string $url ) : string {
	$parts = wp_parse_url( $url );
	if ( empty( $parts['scheme'] ) ) {
		return $url;
	}

	$host = $parts['host'] ?? '';
	$path = $parts['path'] ?? '';

	switch ( $parts['scheme'] ) {
		case 'docs':
			// Override href.
			$group = $host; // This will be the group id unless it is a set id for a different set.
			$set_id = '';
			if ( is_set_id( $host ) ) {
				$set_id = $host;
				// Split path into group and path again.
				$parts = explode( '/', $path, 3 );
				[ $unused, $group, $path ] = $parts;
			}
			$slug = get_slug_from_path( '', $path );
			$new_url = get_url_for_page( $group, $slug, $set_id );
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

		case 'support':
			$map = [
				'new' => 'https://dashboard.altis-dxp.com/#/support/new',
			];
			$new_url = $map[ $host ] ?? null;
			if ( empty( $new_url ) ) {
				return $url;
			}

			$stack_name = Altis\get_environment_name();
			$new_url .= '?applications[]=' . urlencode( $stack_name );
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
