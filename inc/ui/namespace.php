<?php

namespace HM\Platform\Documentation\UI;

use HM\Platform\Documentation;
use HM\Platform\Documentation\Page;
use WP_Admin_Bar;

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
	$hook = add_submenu_page(
		null,
		'',
		'',
		'edit_posts',
		'hm-platform-documentation',
		__NAMESPACE__ . '\\render_page'
	);

	add_action( sprintf( 'load-%s', $hook ), __NAMESPACE__ . '\\load_page' );
}

/**
 * Add the Documentation link to the admin bar
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$wp_admin_bar->add_menu( [
		'parent' => 'hm-platform',
		'id'     => 'documentation',
		'title'  => __( 'Documentation', 'hm-platform' ),
		'href'   => add_query_arg( 'page', 'hm-platform-documentation', admin_url( 'admin.php' ) ),
	] );
}

function load_page() {
	wp_enqueue_style( 'highlightjs', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/vs2015.min.css' );
	wp_enqueue_script( 'highlightjs', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js' );
	wp_enqueue_script( 'highlightjs-line-numbers', 'https://cdn.jsdelivr.net/npm/highlightjs-line-numbers.js@2.7.0/dist/highlightjs-line-numbers.min.js' );

	wp_enqueue_style( __NAMESPACE__, plugins_url( '/assets/style.css', Documentation\DIRECTORY . '/wp-is-dumb' ), [], '2019-04-19' );
	wp_enqueue_script( __NAMESPACE__, plugins_url( '/assets/script.js', Documentation\DIRECTORY . '/wp-is-dumb' ), [ 'highlightjs' ], '2019-04-19' );
}

function render_page() {
	$documentation = Documentation\get_documentation();
	$current_group = $_GET['group'] ?? 'guides';
	$id = $_GET['id'] ?? '';
	$current_page_id = $id;
	$current_page = $documentation[ $current_group ]->get_page( $id );
	?>

	<div class="hm-platform-ui wrap">
		<header>
			<?php echo esc_html_e( 'Documentation', 'hm-platform' ) ?>
		</header>

		<div class="hm-platform-ui__main">
			<nav>
				<ul>
					<?php foreach ( $documentation as $group => $gobj ) : ?>
						<li
							class="<?php echo $group === $current_group ? 'current' : '' ?> <?php echo $group === $current_group && ! $current_page_id ? 'active' : '' ?>"
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
									<li class="<?php echo $current_page === $page ? 'active' : '' ?>">
										<a href="<?php echo add_query_arg( compact( 'group', 'id' ) ) ?>">
											<?php echo esc_html( $page->get_meta( 'title' ) ) ?>
										</a>
									</li>
									<?php render_page_subpages( $page, $group, $current_page ) ?>
								<?php endforeach ?>
							</ul>
						</li>
					<?php endforeach ?>
				</ul>
			</nav>

			<article>
				<?php echo render_content( $current_page ) ?>
			</article>
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
function render_page_subpages( Page $page, string $group, ?Page $current_page ) {
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
			<li class="<?php echo $current_page === $subpage ? 'active' : '' ?>">
				<a href="<?php echo esc_url( $permalink ) ?>">
					<?php echo esc_html( $subpage->get_meta( 'title' ) ) ?>
				</a>
				<?php render_page_subpages( $subpage, $group, $current_page ) ?>
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
