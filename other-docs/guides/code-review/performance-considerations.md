# Performance Considerations

Altis is designed to scale up to any workload, both in the codebase and in the Cloud infrastructure. However, custom code can easily cause issues with scaling and performance if not carefully considered throughout the process of building a site.

## Unbounded queries

Unbounded queries (i.e. unlimited queries) should be avoided, as they quickly turn into scaling issues.

See [the coding standards guidance on unbounded queries](./standards.md#bounded-queries) for more information.


## Avoid writes on page views

Avoid writing to the database on page views (i.e. SQL `UPDATE` or `INSERT` queries). Database writes case a high level of load on the database server, which can cause large performance and scaling problems.

Writes to the database can cause row or table locking, in addition to invalidating the various caches throughout WordPress and the database. Additionally, it means that pages cannot be cached properly, as they are no longer properly idempotent.

This applies to any page view, including regular frontend pages and dashboard pages. Generally speaking, only write to the database after a form submission (or PUT or POST request to REST APIs or Ajax handlers). Larger updates should be offloaded to background tasks, ensuring they don't affect the web-serving infrastructure.

A typical use case for writing to the database is counting page views, such as functionality to display the top pages on a site. The [Altis Native Analytics tools](docs://analytics/native/README.md) can be used for this purpose instead, which takes advantage of the dedicated analytics infrastructure. This infrastructure is specifically designed for a write-heavy workload, unlike the web infrastructure.


## Cache any remote data

Avoid making remote requests on any page render, or other idempotent GET request. Parts of the page render that require data from a remote resource should use background tasks to periodicially update remote data into a long-lived object cache item. This includes both data pulled from remote APIs and external services, as well as most database queries.

This has two main effects: it is almost always faster to load data from the cache, and it also reduces variability. Reducing variability is useful as it ensures the site has predictable performance. For example, if an external API experiences performance issues, hitting the API on every page load would then cause performance issues on the Altis site as well.

Many of Altis' high-level functions will cache for you, including `get_post()`, `get_post_meta()`, `get_option()`, and many more. Additionally, many queries use Elasticsearch under the hood, which retains a high level of performance even with more complex queries.

When you can predict that a query will happen repeatedly, you can cache this at a higher level, using the [object cache API](https://codex.wordpress.org/Class_Reference/WP_Object_Cache#wp_cache_functions). Zack Tollman has an [excellent article about some of the caching concepts that exist within WordPress and Altis](https://www.tollmanz.com/core-caching-concepts-in-wordpress/).
