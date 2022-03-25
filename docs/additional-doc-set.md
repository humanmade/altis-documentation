# Additional Documentation Sets

You can add additional documentation sets to the default documentation menu. For example, user level documentation that
doesn't fit into the developer documentation.

To add a set of documentation, you will need to hook into the `altis.documentation.sets` filter.
The hooked function needs to create the new `\Altis\Documentation\Set` and populate it with one or more Groups of Pages. For example:

```php
use Altis\Documentation\Group;
use Altis\Documentation\Set;
use function Altis\Documentation\add_docs_for_group;

add_filter( 'altis.documentation.sets', function ( array $sets ) {
	if ( empty( $sets['user-docs'] ) ) {
		$doc_set = new Set( 'User Documentation' );
		$group = new Group( 'Project Guides' );
		add_docs_for_group( $group, __DIR__ . '/our-guides' );
		$doc_set->add_group( 'project-guides', $group );
		$sets['user-docs'] = $oc_set;
	}

	return $sets;
} );
```

You will also need to add your submenu page to the Documentation page.

```php
add_action( 'admin_menu', function () {
	$doc_set = \Altis\Documentation\get_documentation_set( 'user-docs' );
	$hook = add_submenu_page(
		\Altis\Documentation\UI\PAGE_SLUG,
		'',
		$doc_set->get_title(),
		'edit_posts',
		'user-docs',
		function () {
			\Altis\Documentation\UI\render_page( 'user-docs' ); 
		}
	);

	add_action( sprintf( 'load-%s', $hook ), '\Altis\Documentation\UI\load_page_assets' );
} );
```
