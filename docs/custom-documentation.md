# Custom Documentation

The documentation module can be used for your own [custom modules](docs://getting-started/custom-modules.md), allowing you to build
an internal knowledge base for your development team.

The built-in Documentation Set is intended for developer documentation. To add your documentation to the Developer Docs set, see the
information below. To add additional sets of Documentation, perhaps for the users of your system,
see [Additional Documentation Sets](./additional-doc-set.md).

## Documentation Structure

Each module provides its own documentation. The Documentation module also includes project-level documentation, such as
the [Guides](docs://guides/) and [Getting Started](docs://getting-started/) documentation.

The Documentation module automatically takes Markdown files from a `docs/` directory within each module's root directory, and parses
them into pages. These are hierarchically-structured based on the directory structure, with `README.md` acting as the index file for
each directory.

## Formatting

Documentation files are standard Markdown, as supported by [Parsedown](https://parsedown.org/). They also
support [relative and in-project links](linking.md).

Markdown files can contain YAML front-matter, which specifies a list of "meta" information about the page itself. The following
fields are supported:

```yaml
---
# Title of the page for the menu.
# Default: first H1 on the page.
title: Page Title

# Order of the page within the menu.
# Pages with the same `order` value will be ordered by filename.
# Default: 0
order: 0
---
```

Headers have automatically generated fragment IDs attached for in-page linking. This automatic ID can be overridden if desired by
suffixing with `{#id-name}`:

```md
# Header Name {#override-id}
```

## Custom Behavior

Internally, the Documentation module stores an ordered list of "groups", which have an ID and associated top-level pages. Each page
has metadata, content, and potentially sub-pages.

The `altis.documentation.groups` filter is provided the ordered list of `Group` objects, and you can add or remove groups from here,
or manipulate existing groups. The `Documentation\add_docs_for_group()` function may be useful for this;
see `Documentation\filter_add_dev_docs_set()` to see how Altis generates developer documentation for modules.

For example, to add your own Guides-style section:

```php
use Altis\Documentation\Group;
use function Altis\Documentation\add_docs_for_group;

add_filter( 'altis.documentation.groups', function ( array $groups, string $set_id ) {
    if ( $set_id === 'dev-docs' ) {
        $groups['project-guides'] = new Group( 'Project Guides' );
        add_docs_for_group( $groups['project-guides'], __DIR__ . '/our-guides' );
    }
    return $groups;
} );
```
