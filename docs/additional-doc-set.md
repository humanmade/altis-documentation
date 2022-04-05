# Additional Documentation Sets

You can add additional documentation sets to the default documentation menu. For example, user level documentation for
your project that doesn't fit into the developer documentation.

To add a set of documentation, you will need to hook into the `altis.documentation.sets` filter. The hooked function
needs to create a new `Altis\Documentation\Set` instance and populate it with one or more Groups of Pages. Then add it
to the returned array of Sets. For example:

```php
use Altis\Documentation\Group;
use Altis\Documentation\Set;

const PROJECT_DOCS_SET_ID = 'project-docs';

	// Custom user documentation Set.
	add_filter( 'altis.documentation.sets', static function ( array $sets ) {
		if ( empty( $sets[ PROJECT_DOCS_SET_ID ] ) ) {
			// Create our documentation set
			$doc_set = new Set( PROJECT_DOCS_SET_ID, 'Project Documentation' );

			// Add all documentation pages in a group.
			$proj_docs = dirname( __DIR__ ) . '/project-docs';
			$group = new Group( 'Project Guides' );
			Altis\Documentation\add_docs_for_group( $group, $proj_docs );

			// Add the group to the set
			$doc_set->add_group( 'guides-group', $group );
			$doc_set->set_default_group_id( 'guides-group' );

			// Add our set to the others.
			$sets[ PROJECT_DOCS_SET_ID] = $doc_set;
		}

		return $sets;
	} );

```

If you wish to include a link to your additional documentation page under a different menu, for example a top level
functional menu you can. Add a submenu page under a different menu but with the same menu id as the system uses.

```php
const PROJECT_DOCS_SET_ID = 'project-docs';

add_action( 'admin_menu', static function () {
	$doc_set = \Altis\Documentation\get_documentation_set( PROJECT_DOCS_SET_ID );
	$page_hook = add_submenu_page(
		'my-custom-menu-id',
		$doc_set->get_title(),
		$doc_set->get_title(),
		'edit_posts',
		sprintf( '%s-%s', \Altis\Documentation\UI\PAGE_SLUG, PROJECT_DOCS_SET_ID ),
		static function () {
			\Altis\Documentation\UI\render_page( PROJECT_DOCS_SET_ID );
		} );

	// Add custom call back to load styles and scripts and to set page title tag.
	add_action( "load-$page_hook", static function () {
		// Filter default set_id for this page. Add this hook here, so it is set up before the page renders.
		add_filter( 'altis.documentation.default.set', static function () : string {
			return PROJECT_DOCS_SET_ID;
		}, 10 );

		\Altis\Documentation\UI\load_page_assets();
	} );
} );
```
