<?php

namespace HM\Platform\Documentation;

use FilesystemIterator;
use HM\Platform\Module;
use Parsedown;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Spyc;

const CACHE_GROUP = 'platform_documentation';
const ITERATOR_FLAGS = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;

/**
 * Register module.
 */
function register() {
	Module::register(
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
 * Load module data for documentation.
 *
 * @return bool True if docs were successfully generated, false otherwise.
 */
function generate_docs() : bool {
	$modules = Module::get_all();

	$docs = [
		'guides' => new Group( 'Guides' ),
	];
	foreach ( $modules as $id => $module ) {
		$module_docs = generate_docs_for_module( $id, $module );
		if ( empty( $module_docs ) ) {
			continue;
		}

		$docs[ $id ] = $module_docs;
	}

	$result = wp_cache_set( 'docs', $docs, CACHE_GROUP );
	if ( ! $result ) {
		trigger_error( 'Could not store documentation in cache', E_USER_NOTICE );
	}

	return $result;
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

	// Generate objects for each file.
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(
			$doc_dir,
			ITERATOR_FLAGS
		)
	);
	foreach ( $iterator as $leaf ) {
		/** @var \SplFileInfo $leaf */
		if ( $leaf->getExtension() !== 'md' ) {
			continue;
		}

		$file = $leaf->getRealPath();
		$doc = parse_file( $file );
		$doc->set_meta( 'root', $doc_dir );
		$out_path = get_slug_from_path( $doc_dir, $file );
		$group->add_page( $out_path, $doc );
	}

	return $group;
}

/**
 * Convert a (relative) Markdown path to a slug.
 *
 * @param string $path Path for Markdown file (relative to documentation root)
 * @return string Hierarchical slug for the document
 */
function get_slug_from_path( $root, $path ) {
	$out_path = substr( $path, strlen( $root ) );
	return trim( preg_replace( '/README\.md/i', '', $out_path ), '/' );
}

/**
 * Get all documentation group.
 *
 * @return Group[]|null Sorted list of groups if available, null otherwise
 */
function get_documentation() : ?array {
	// $cache = wp_cache_get( 'docs', CACHE_GROUP );
	if ( empty( $cache ) ) {
		generate_docs();
		$cache = wp_cache_get( 'docs', CACHE_GROUP );
	}
	return $cache ?: null;
}

/**
 * Get a documentation group.
 *
 * @param string $id Group ID
 * @return Group|null Group if available, null otherwise
 */
function get_documentation_group( $id ) : ?Group {
	$docs = get_documentation();
	return $docs[ $id ] ?: null;
}

/**
 * Parse Markdown file into a Page.
 *
 * @param string $file Path to the file to parse
 * @return Page Parsed data for the file
 */
function parse_file( $file ) : Page {
	$raw = file_get_contents( $file );

	// Find YAML frontmatter.
	$meta = [];
	preg_match( '#^---(.+)---\n+#Us', $raw, $yaml_matches );
	if ( $yaml_matches ) {
		$meta = Spyc::YAMLLoadString( $yaml_matches[1] );
		// Strip YAML doc from the header
		$raw = substr( $raw, strlen( $yaml_matches[0] ) );
	}

	// Default title if not set in YAML
	if ( empty( $meta['title'] ) && preg_match( '/^\n*#\s([^\n]+)/', $raw, $matches ) ) {
		$meta['title'] = $matches[1];
	}

	$data = new Page( $raw );
	if ( ! empty( $meta ) ) {
		$data->set_all_meta( $meta );
	}
	$data->set_meta( 'path', $file );

	return $data;
}

/**
 * Render a page from Markdown.
 *
 * @param Page $page Page to render
 * @return string HTML content for the page
 */
function render_page( Page $page ) : string {
	$parsedown = new MarkdownParser( $page );
	return $parsedown->text( $page->get_content() );
}
