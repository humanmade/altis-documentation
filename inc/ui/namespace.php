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
use function Altis\Documentation\get_documentation_set;
use function Altis\Telemetry\track;

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
 *
 * We use a bit of a hack to not actually have the page added to the
 * admin-menu by setting the parent to `null`. This is then added
 * to the admin bar.
 */
function register_menu() {
	// Add top level page
	$hook = add_menu_page(
		null,
		__( 'Documentation', 'altis' ),
		'edit_posts',
		PAGE_SLUG
	);

	// Add our default dev docs.
	$dev_set = get_documentation_set( 'dev-docs' );
	add_submenu_page(
		PAGE_SLUG,
		'',
		$dev_set->get_title(),
		'edit_posts',
		PAGE_SLUG,
		__NAMESPACE__ . '\\render_dev_docs_page'
	);

	add_action( sprintf( 'load-%s', $hook ), __NAMESPACE__ . '\\load_page_assets' );
}

/**
 * Add the Documentation link to the admin bar
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar manager class.
 */
function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$wp_admin_bar->add_menu( [
		'parent' => 'altis',
		'id'     => 'documentation',
		'title'  => __( 'Documentation', 'altis' ),
		'href'   => add_query_arg( 'page', PAGE_SLUG, admin_url( 'admin.php' ) ),
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
	$page = Documentation\get_page_by_id( get_current_group_id(), get_current_page_id(), get_current_set_id() );
	if ( $page ) {
		$GLOBALS['title'] = $page->get_meta( 'title' );
	} else {
		$GLOBALS['title'] = __( 'Page Not Found', 'altis' );
	}
}

/**
 * Get the current Set ID.
 *
 * @return string Doc set ID if set, otherwise the default set.
 */
function get_current_set_id() : string {
	// @codingStandardsIgnoreLine
	return $_GET['set'] ?? apply_filters( 'altis.documentation.default.set', 'dev-docs' );
}

/**
 * Get the current group ID.
 *
 * @return string Group ID if set, otherwise the default group.
 */
function get_current_group_id( string $set_id = '' ) {
	$set_id = $set_id ?? get_current_set_id();

	// @codingStandardsIgnoreLine
	return $_GET['group'] ?? apply_filters( 'altis.documentation.default.group', 'welcome', $set_id );
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
 * Wrapper function to call render_page with the correct set id. Called from Documentation menu.
 */
function render_dev_docs_page() {
	render_page( 'dev-docs' );
}

/**
 * Documentation page render callback.
 */
function render_page( string $set_id ) {

	$set_id          = $set_id ?? get_current_set_id();
	$documentation   = Documentation\get_documentation( $set_id );
	$current_group   = get_current_group_id( $set_id );
	$current_page_id = get_current_page_id();
	$current_page    = Documentation\get_page_by_id( $current_group, $current_page_id, $set_id );
	?>

	<div class="altis-ui wrap">
		<div class="altis-ui__main">
			<nav>
				<p class="altis-ui__doc-title"><?php echo esc_html_e( 'Documentation', 'altis' ) ?></p>
				<ul>
					<?php foreach ( $documentation as $group => $gobj ) : ?>
						<li
							class="<?php echo $group === $current_group ? 'current' : '' ?> <?php echo $group === $current_group && ! $current_page_id ? 'active' : '' ?>"
						>
							<a
								href="<?php echo esc_attr( add_query_arg( [ 'set' => $set_id, 'group' => $group, 'id' => '' ] ) ); // phpcs:ignore ?>"
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
									<li class="<?php echo ( $current_group === $group && $current_page_id === $id ) ? 'active' : '' ?>">
										<a href="<?php echo esc_attr( add_query_arg( [ 'set' => $set_id, 'group' => $group, 'id' => $id ] ) ); ?>">
											<?php echo esc_html( $page->get_meta( 'title' ) ); ?>
										</a>
									</li>
									<?php render_page_subpages( $page, $group, $current_page, $set_id ) ?>
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
		'event'      => 'Documentation',
		'properties' => [
			'content_type'   => 'Documentation page view',
			'content_action' => "$set_id-$current_group-$current_page_id",
		],
	] );

}

/**
 * Output the menu for a page's subpages.
 *
 * This recurses all subpages.
 *
 * @param Page      $page         Documentation page object.
 * @param string    $group        The documentation page group.
 * @param Page|null $current_page The current page object if set.
 * @param string    $set          The current documentation set.
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
				'set'   => $set,
				'group' => $group,
				'id'    => $subpage_id,
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
