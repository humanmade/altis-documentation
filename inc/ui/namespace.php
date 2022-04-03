<?php
/**
 * Altis Documentation.
 *
 * @package altis/documentation
 */

namespace Altis\Documentation\UI;

use Altis\Documentation;
use Altis\Documentation\Page;
use WP_Admin_Bar;

const PAGE_SLUG = 'altis-documentation';

/**
 * Set up hooks.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'admin_menu', __NAMESPACE__ . '\\register_menu' );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_menu', 11 );
}

/**
 * Register the Documentation admin page.
 * We loop through all documentation sets adding a submenu for each.
 * If there are none, we don't add the top level menu.
 */
function register_menu() {
	// Add top level page.
	$doc_sets = Documentation\get_documentation_sets();
	if ( empty( $doc_sets ) ) {
		return;
	}

	add_menu_page(
		__( 'Documentation', 'altis' ),
		__( 'Documentation', 'altis' ),
		'edit_posts',
		PAGE_SLUG
	);

	$first_child = true; // The first in the list is the default menu item.

	foreach ( $doc_sets as $set_id => $doc_set ) {
		// Add a sub menu page with custom render callback.
		$page_hook = add_submenu_page(
			PAGE_SLUG,
			$doc_set->get_title(),
			$doc_set->get_title(),
			'edit_posts',
			$first_child ? PAGE_SLUG : sprintf( '%s-%s', PAGE_SLUG, $set_id ),
			static function () use ( $set_id ) {
				// Render this set of docs.
				render_page( $set_id );
			}
		);

		// Add custom call back to load styles and scripts and to set page title tag.
		add_action( "load-$page_hook", static function () use ( $set_id ) {
			// Filter default set_id for this page. Add this hook here, so it is set up before the page renders.
			add_filter( 'altis.documentation.default.set', static function () use ( $set_id ) : string {
				return $set_id;
			}, 10 );

			load_page_assets();
		} );

		$first_child = false;
	}
}

/**
 * Add the Documentation link to the admin bar
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar manager class.
 */
function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$wp_admin_bar->add_menu( [
		'parent' => 'altis',
		'id' => 'documentation',
		'title' => __( 'Documentation', 'altis' ),
		'href' => add_query_arg( 'page', PAGE_SLUG, admin_url( 'admin.php' ) ),
	] );
}

/**
 * Callback for the load-$page admin action.
 *
 * We enqueue all the scripts and styles for the documentation page here.
 */
function load_page_assets() {
	wp_enqueue_style( 'highlightjs', plugins_url( '/assets/vs2015.min.css', Documentation\DIRECTORY . '/wp-is-dumb' ) );
	wp_enqueue_script( 'highlightjs', plugins_url( '/assets/highlight.min.js', Documentation\DIRECTORY . '/wp-is-dumb' ) );
	wp_enqueue_script( 'highlightjs-line-numbers', plugins_url( '/assets/highlightjs-line-numbers.min.js', Documentation\DIRECTORY . '/wp-is-dumb' ) );

	wp_enqueue_style( __NAMESPACE__, plugins_url( '/assets/style.css', Documentation\DIRECTORY . '/wp-is-dumb' ), [], '2019-04-29' );
	wp_enqueue_script( __NAMESPACE__, plugins_url( '/assets/script.js', Documentation\DIRECTORY . '/wp-is-dumb' ), [ 'highlightjs' ], '2019-04-19' );

	// Determine the current page title.
	$page = '';
	$set_id = get_current_set_id();
	$group_id = get_current_group_id( $set_id );
	$group = Documentation\get_documentation_set( $set_id )->get_group( $group_id );
	$page_id = get_current_page_id();

	if ( ! is_null( $group ) ) {
		if ( empty( $page_id ) ) {
			$page_id = $group->get_default_page_id();
		}
		$page = $group->get_page( $page_id );
	}

	if ( $page ) {
		$GLOBALS['title'] = $page->get_meta( 'title' );
	} else {
		$GLOBALS['title'] = get_admin_page_title(); // Get it from the menu.
	}
}

/**
 * Get the current Set ID.
 *
 * @return string Doc set ID if set, otherwise the default set.
 */
function get_current_set_id() : string {
	/**
	 * Filter the default set ID. If no query string parameter is found, the filter is applied.
	 *
	 * @param string $set_id The default set id.
	 *
	 * @return string The default set id.
	 */
	// @codingStandardsIgnoreLine
	return $_GET['set'] ?? apply_filters( 'altis.documentation.default.set', '' );
}

/**
 * Get the current group ID.
 *
 * @param string $set_id The current set id.
 *
 * @return string Group ID if set, otherwise the default group for the set.
 */
function get_current_group_id( string $set_id = '' ) : string {
	$set_id = $set_id ?? get_current_set_id();
	$set = Documentation\get_documentation_set( $set_id );

	// @codingStandardsIgnoreLine
	return $_GET['group'] ?? $set->get_default_group_id();
}

/**
 * Get the current page ID.
 *
 * @return string Page ID if set, otherwise the default page.
 */
function get_current_page_id() {
	// @codingStandardsIgnoreLine
	return $_GET['id'] ?? '';
}

/**
 * Documentation page render callback.
 *
 * @param string $set_id The current Set id.
 */
function render_page( string $set_id ) {

	$set_id = $set_id ?? get_current_set_id();
	$current_group_id = get_current_group_id( $set_id );
	$current_page_id = get_current_page_id();

	$doc_set = Documentation\get_documentation_set( $set_id );
	$current_group = $doc_set->get_group( $current_group_id );
	$current_page = null;
	if ( $current_group !== null ) {
		$current_page = $current_group->get_page( $current_page_id );
	}

	?>

	<div class="altis-ui wrap">
		<div class="altis-ui__main">
			<nav>
				<p class="altis-ui__doc-title"><?php esc_html_e( 'Documentation', 'altis' ) ?></p>
				<ul>
					<?php foreach ( $doc_set->get_groups() as $group_id => $gobj ) : ?>
						<li
							class="<?php echo $group_id === $current_group_id ? 'current' : '' ?> <?php echo $group_id === $current_group_id && ! $current_page_id ? 'active' : '' ?>"
						>
							<a
								href="<?php echo esc_attr( add_query_arg( [ 'set' => $set_id, 'group' => $group_id, 'id' => '' ] ) ); // phpcs:ignore ?>"
							>
								<?php echo esc_html( $gobj->get_title() ) ?>
							</a>

							<ul>
								<?php
								foreach ( $gobj->get_pages() as $id => $page ) :
									if ( $id === '' ) {
										continue;
									}
									?>
									<li class="<?php echo ( $current_group_id === $group_id && $current_page_id === $id ) ? 'active' : '' ?>">
										<a href="<?php echo esc_attr( add_query_arg( [ 'set' => $set_id, 'group' => $group_id, 'id' => $id ] ) ); // phpcs:ignore ?>">
											<?php echo esc_html( $page->get_meta( 'title' ) ); ?>
										</a>
									</li>
									<?php render_page_subpages( $page, $group_id, $current_page, $set_id ) ?>
								<?php endforeach ?>
							</ul>
						</li>
					<?php endforeach ?>
				</ul>
			</nav>

			<article>
				<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo render_content( $current_page );
				?>
			</article>
		</div>
	</div>

	<?php

	// Track this page view in Altis Telemetry.
	do_action( 'altis.telemetry.track', [
		'event' => 'documentation',
		'properties' => [
			'set' => $set_id,
			'group' => $current_group_id,
			'page' => $current_page_id,
		],
	] );

}

/**
 * Output the menu for a page's subpages.
 *
 * This recurses all subpages.
 *
 * @param Page $page Documentation page object.
 * @param string $group The documentation page group.
 * @param Page|null $current_page The current page object if set.
 * @param string $set The current documentation set.
 */
function render_page_subpages( Page $page, string $group, ?Page $current_page, string $set ) {
	if ( ! $page->get_subpages() ) {
		return;
	}
	?>
	<ul>
		<?php
		foreach ( $page->get_subpages() as $subpage_id => $subpage ) :
			$permalink = add_query_arg( [
				'set' => $set,
				'group' => $group,
				'id' => $subpage_id,
			] );
			?>
			<li class="<?php echo $current_page === $subpage ? 'active' : '' ?>">
				<a href="<?php echo esc_url( $permalink ) ?>">
					<?php echo esc_html( $subpage->get_meta( 'title' ) ) ?>
				</a>
				<?php render_page_subpages( $subpage, $group, $current_page, $set ) ?>
			</li>
		<?php endforeach ?>
	</ul>
	<?php
}

/**
 * Render the content for a page.
 *
 * @param Page|null $page The page object to render content for.
 *
 * @return string
 */
function render_content( ?Page $page ) : string {
	if ( empty( $page ) ) {
		return '404: Unable to find page.';
	}

	return Documentation\render_page( $page );
}
