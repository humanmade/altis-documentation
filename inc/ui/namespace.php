<?php

namespace HM\Platform\Documentation\UI;

use HM\Platform\Documentation;
use HM\Platform\Documentation\Page;
use WP_Admin_Bar;

function bootstrap() {
	add_action( 'admin_menu', __NAMESPACE__ . '\\register_menu' );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_menu', 11 );
}

function register_menu() {
	$hook = add_submenu_page(
		null,
		'Documentation',
		'Documentation',
		'edit_posts',
		'hm-platform-documentation',
		__NAMESPACE__ . '\\render_page'
	);

	add_action( sprintf( 'load-%s', $hook ), __NAMESPACE__ . '\\load_page' );
}

function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	// Add WordPress.org link
	$wp_admin_bar->add_menu( [
		'parent'    => 'hm-platform-logo',
		'id'        => 'documentation',
		'title'     => 'Documentation',
		'href'      => add_query_arg( 'page', 'hm-platform-documentation', admin_url( 'admin.php' ) ),
	] );
}

function load_page() {
	wp_enqueue_style( __NAMESPACE__, plugins_url( '/assets/style.css', Documentation\DIRECTORY . '/wp-is-dumb' ) );
	wp_enqueue_style( 'highlightjs', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/default.min.css' );
	wp_enqueue_script( 'highlightjs', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js' );
	wp_enqueue_script( __NAMESPACE__, plugins_url( '/assets/script.js', Documentation\DIRECTORY . '/wp-is-dumb' ), [ 'highlightjs' ] );
}

function render_page() {
	$documentation = Documentation\get_documentation();
	$current_group = $_GET['group'] ?? 'guides';
	$id = $_GET['id'] ?? '';
	$current_page = $documentation[ $current_group ]->get_page( $id );
	?>

	<div class="hm-platform-ui wrap">
		<header>
			Documentation
		</header>

		<div class="hm-platform-ui__main">
			<nav>
				<ul>
					<?php foreach ( $documentation as $group => $gobj ) : ?>
						<li
							class="<?php echo $group === $current_group ? 'current' : '' ?>"
						>
							<a
								href="<?php echo add_query_arg( [ 'group' => $group, 'id' => '' ] ) ?>"
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
									<li>
										<a href="<?php echo add_query_arg( compact( 'group', 'id' ) ) ?>">
											<?php echo esc_html( $page->get_meta( 'title' ) ) ?>
										</a>
									</li>
									<?php render_page_subpages( $page, $group ) ?>
								<?php endforeach ?>
							</ul>
						</li>
					<?php endforeach ?>
				</ul>
			</nav>

			<article>
				<?php echo render_content( $current_page ) ?>
			</article>

			<aside>
				<input
					placeholder="Future search field…"
					type="search"
				/>
			</aside>
		</div>
	</div>

	<?php
}

/**
 * Output the menu for a page's subp ages.
 *
 * This recurses all subpages.
 *
 * @param Page $page
 * @param string $group
 */
function render_page_subpages( Page $page, string $group ) {
	if ( ! $page->get_subpages() ) {
		return;
	}
	?>
	<ul>
		<?php
		foreach ( $page->get_subpages() as $subpage_id => $subpage ) :
			$permalink = add_query_arg( [
				'group' => $group,
				'id' => $subpage_id,
			] );
			?>
			<li>
				<a href="<?php echo esc_url( $permalink ) ?>">
					<?php echo esc_html( $subpage->get_meta( 'title' ) ) ?>
				</a>
				<?php render_page_subpages( $subpage, $group ) ?>
			</li>
		<?php endforeach ?>
	</ul>
	<?php
}

function render_content( ?Page $page ) {
	if ( empty( $page ) ) {
		return '404: Unable to find page.';
	}

	return Documentation\render_page( $page );
}
