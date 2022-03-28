# Additional Documentation Sets

You can add additional documentation sets to the default documentation menu. For example, user level documentation that
doesn't fit into the developer documentation.

To add a set of documentation, you will need to hook into the `altis.documentation.sets` filter. The hooked function
needs to create a new `\Altis\Documentation\Set` instance and populate it with one or more Groups of Pages. Then add it
to the returned array of Sets. For example:

```php
use Altis\Documentation\Group;
use Altis\Documentation\Set;
use function Altis\Documentation\add_docs_for_group;

add_filter( 'altis.documentation.sets', static function ( array $sets ) {
	if ( empty( $sets['user-docs'] ) ) {
		// Create our docuemtnaiton set
		$doc_set = new Set( 'User Documentation' );
		
		// Add all documentation pages in a group.
		$user_docs = dirname( __DIR__ ) . '/user-docs';
		$group = new Group( 'Project Guides' );
		add_docs_for_group( $group, $user_docs );
		
		// Add the group to the set
		$doc_set->add_group( 'guides-group', $group );
		
		// Add our set to the others.
		$sets['user-docs'] = $doc_set;
	}

	return $sets;
} );
```

You will need to hook the `altis.documentation.default.group` filter to return your default group when your
documentation set is active.

```php

add_filter( 'altis.documentation.default.group', static function ( $group_id, $set_id ) {
	if ( $set_id === 'user-docs'  ) {
		return 'guides-group';
	}

	return $group_id;
}, 10, 2 );
```

You will also need to add your submenu page to the Documentation menu page. Your page render callback function needs to
call `\Altis\Documentation\UI\render_page()` passing your set id. Within that callback function you need to
hook `altis.documentation.default.set` so that your set is always the default one when your submenu page is active.
Finally, you will need to load the Documentation styles and scripts for your submenu page.

```php
add_action( 'admin_menu', static function () {
	$doc_set = \Altis\Documentation\get_documentation_set( 'user-docs' );
	$hook = add_submenu_page(
		\Altis\Documentation\UI\PAGE_SLUG,
		$doc_set->get_title(),
		$doc_set->get_title(),
		'edit_posts',
		\Altis\Documentation\UI\PAGE_SLUG . '-user-docs',
		function () {
			add_filter( 'altis.documentation.default.set', static function ( $set_id ) {
				return 'user-docs';
			}, 10 );

			\Altis\Documentation\UI\render_page( 'user-docs' );
		}
	);

	// Load the styles and scripts for our page too.
	add_action( "load-$hook", '\Altis\Documentation\UI\load_page_assets' );
} );
```
