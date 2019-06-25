---
order: 10
---
# Linking

The Documentation module supports relative links and cross-linking between pages in documentation.


## Relative Links

Within a given module's documentation, you can use relative links between files. These will be resolved during the rendering of the documentation.

For example, to link to a document in the same directory called `doc.md`, you can format your link as:

```md
Consult the [documentation](./doc.md)
```

To link to a document in a subdirectory or a parent directory, use similar relative links with the directory names:

```md
[Document in the parent directory](../doc.md)

[Document in a subdirectory](./subdir/doc.md)
```

Note that relative links can only be used between documents in a single module, and links to other modules should instead use cross-linking.


## Cross-Linking

Cross-linking refers to links from one module to another. This allows creating richer documentation, tying together various modules.

To link from one module to another, use the special URL scheme `docs://`. This should be followed by the name of the documentation group (typically the module ID), followed by a slash, followed by the page ID (typically a file path within the module's documentation directory). The full format is:

```
docs://{group}/{id}
```

For example, to link to the `branding.md` document in the CMS module (ID `cms`):

```md
Consult the [branding documentation](docs://cms/branding.md)
```

**Note:** while documentation group IDs generally align with module IDs, some special groups exist for meta documentation. This includes the Getting Started (ID `getting-started`) and Guides (ID `guides`) documentation, which are located in the `other-docs` directory of the Documentation module.


## Internal Linking

To enable rich documentation, you can link to internal resources on the site.

These links use the special `internal://` URL scheme. This is followed by the type of internal link, followed by a slash, followed by an optional additional path. The following types are available:

* `internal://home/{path}` - URL on the frontend, equivalent to `home_url( $path )`
* `internal://site/{path}` - URL on the backend, equivalent to `site_url( $path )`
* `internal://admin/{path}` - Page in the admin, equivalent to `admin_url( $path )`
* `internal://network-admin/{path}` - Page in the network admin, equivalent to `network_admin_url( $path )`

For example, to link to the current user's profile page:

```md
[View your profile](internal://admin/profile.php)
```
