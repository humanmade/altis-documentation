# Multiple Sites

Altis includes support for multiple independent sites out-of-the-box. This functionality can be used to provide different sites for regional or multilingual content, or for independent microsites.

This functionality is referred to as **multisite**, and is enabled out-of-the-box on all Altis projects.


## Terminology

With Altis, you work on a **project**. This encompasses the codebase and the hosting provided by Altis.

Each project can have multiple **sites**, which can represent multiple domains, subdomains, or other distinct websites. These are all powered by the same codebase and hosting.

For advanced use cases, sites can be grouped into **networks**; most projects will only have a single network encompassing all sites.

**Note:** Due to backwards compatibility concerns in WordPress, some concepts and internal APIs use outdated terminology which may be confusing. In legacy versions of WordPress, sites were called "blogs", and networks were called "sites". When using new APIs, consult the documentation to ensure you are using the correct APIs.


## Technical Architecture

In a multisite project, your codebase is used to power multiple sites. Some functionality is applied to all sites, while other functionality is applied on a site-by-site-basis.

A single database is used by all sites, and individual sites use separate tables for site-specific content. Site-specific tables are prefixed with the site's ID. Some tables are shared across sites

Altis modules and [custom modules](docs://getting-started/custom-modules.md) are enabled across every site on your project. For per-site functionality, you can develop plugins, which can be activated for individual sites. They can also optionally be activated for all sites (called "Network-Wide Activation").

[Themes](docs://getting-started/first-theme.md) are activated on a site-by-site basis as sites can only have a single theme active at any one time. Child themes can be used to build site-specific themes which inherit common templates.

Users and profile information are shared across all sites in the project, but user permissions (roles and capabilites) are managed on a per-site basis. Users on your network must be added to each site they should have access to. Additionally, some users may be given network-wide administrator capabilities, called "super-admin".



## Setting up new sites

On all new Altis sites, you will start with a single site. This is called the "main" site. To take advantage of multisite, you'll need to create multiple sites.

In the backend user interface, a separate "Network Admin" dashboard is provided for management of your network. The Network Admin contains site management tools, as well as settings for the network.

To create a site, start by going to the Network Admin dashboard. This is available under the My Sites item in the toolbar. Head to My Sites > Network Admin > Sites to manage sites. The "Add New" button can be used to create new sites on the network.

**Note:** You must be authenticated as a super-admin to access the Network Admin.

By default, sites will be created underneath the main site's domain; for example, if your main site is at `altis.local`, new sites will be created at `altis.local/{site}/`. Once a site has been created, you can edit the site's address to use a subdomain or custom domain instead.


## Building cross-site functionality

Functionality can be built in custom modules and plugins to operate across multiple sites. This may include fetching data from other sites, or storing settings or other data on the main site.

Internally, WordPress has a concept of the "current" site, which acts as context to many low-level functions. This is determined during the bootstrap process using the URL. This is used to determine (amongst other things) the database table prefix.

To build cross-site functionality, you can use the multisite APIs to access and change the current site. To access data on another site, you "switch" to it, and "restore" when you are finished using the site. WordPress maintains a stack of sites, allowing you to switch multiple times.

**Important:** Ensure you always restore after switching, to avoid breaking other functionality which uses the current site.

The primary functions for this functionality are [`switch_to_blog()`](https://developer.wordpress.org/reference/functions/switch_to_blog/), [`restore_current_blog()`](https://developer.wordpress.org/reference/functions/restore_current_blog/), and [`get_current_blog_id()`](https://developer.wordpress.org/reference/functions/get_current_blog_id/). Note that these use outdated terminology, and "blog" refers to the site.


## Limitations

### Shared Content

Each site stores and manages content independently. This allows unrelated sites to be managed as part of the same network without affecting each other.

In some cases, you may wish to share content between sites. We recommend the [Distributor plugin by 10up](https://distributorplugin.com/) to syndicate content between sites as necessary; keep in mind that duplicate content across sites may cause a SEO penalty.

For sharing media between sites, install the [Network Media Library](https://github.com/humanmade/network-media-library).


## Common use-cases

### Multi-region or multilingual

Multi-region or multilingual sites can be handled through multisite. With these sites, you typically want to share as much of the codebase across sites, while keeping content managed separately for each site.

This can be handled by building functionality in [custom modules](docs://getting-started/custom-modules.md), along with a [common theme](docs://getting-started/first-theme.md) used across all sites. Adjustments to the style and behavior of the site can be done through child themes, which can extend the common theme.

Consult the [multilingual module guide](docs://multilingual/) for information on how to configure a multilingual setup.
