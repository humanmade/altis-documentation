---
order: 100
---
# For WordPress Developers

If you're an experienced WordPress developer, you'll feel right at home with Altis.

We've designed Altis to be the best of WordPress, with extra functionality and tools to make it even easier to work on projects. Most of your existing processes, tools, plugins, and themes will work with no changes.


## Your project structure

Unlike traditional WordPress projects, your Altis project's codebase will look a little different. (Altis projects are based off a [WordPress Skeleton pattern](https://github.com/markjaquith/WordPress-Skeleton).)

Your project will contain a `content/` directory, which works just like `wp-content/ that you may be used to already. This can contain your plugins and themes; Altis is compatible with most existing WordPress plugins and themes.

But, unlike a typical WordPress project, you won't see `wp-admin/`, `wp-includes/`, or other WordPress files. Once you've set up your project, you'll see a `wordpress/` directory. This is created dynamically when you install your project's dependencies.

You'll also see a `vendor/` directory, as well as some extra files including `composer.json` and `.build-script`.


## Dependencies and versioning

With Altis projects, you use [Composer](https://getcomposer.org/) to manage dependencies including Altis and WordPress. You can also [manage plugins and themes via Composer](third-party-plugins.md) if you want.

Your `composer.json` contains your Composer manifest. This describes which libraries and tools to load in. If you've just created a new version of Altis, this will only contain Altis itself (`altis/altis`) as well as the local environments (`altis/local-chassis` and `altis/local-server`). These will be tied to a specific version of Altis (like `^10.0.0`).

The main Altis package (`altis/altis`) will load in a bunch of dependencies, including WordPress and bundled plugins. **The version of WordPress you use is tied to the Altis version**, as are any bundled plugins. This means we can ensure all the functionality works well together. We support each major version [for a full year](docs://guides/long-term-support/) including backporting bugfixes and security patches.

As the developer, you're responsible for keeping this version up to date. We recommend using [Dependabot](https://github.blog/2020-06-01-keep-all-your-packages-up-to-date-with-dependabot/) for automated alerts about new major and minor versions of Altis.


## Installing plugins and themes

Unlike many WordPress hosts, on Altis your codebase is managed through GitHub. This means any changes to your project's code need to go through version control.

In our cloud environments, we operate a read-only filesystem for security reasons. This means that **users cannot install or upgrade plugins or themes**, and changes must go via the codebase instead.

You can [manage plugins and themes via Composer](third-party-plugins.md), which can integrate with Dependabot. Alternatively, you can use a local environment to check the Upgrades tab inside the WordPress dashboard, then commit any changes to your repository.


## Ecosystem compatibility

Altis is broadly compatible with the WordPress ecosystem, including most WordPress plugins and themes. Our [automated code review](docs://guides/code-review/) checks for known security and performance issues.

We have a few changes from standard WordPress. The biggest one of those is that the filesystem is read-only. That means instead of having a `wp-content/uploads/` directory, we use [S3 for storage](docs://cloud/s3-storage/). We automatically set up filters internally which mean most plugins work, but some plugins which need filesystem access may break.

Notably, most backup plugins will not work on Altis. Altis operates [automated backup processes for you](docs://cloud/backups/), and the Altis Dashboard allows you to take a manual snapshot of your uploads or database if you need a copy (such as for testing locally).

Note that we also bundle some plugins as part of Altis; we manage the versions of those via the Altis module they're included in.


### Multisite

Unlike out-of-the-box WordPress, **Altis is multisite by default, and multisite only**. This makes it much easier to add additional sites when you eventually need them, but means you'll need to migrate from single site to multisite as part of any WordPress to Altis migrations.

We also create an private site especially for shared network content, called the [Global Content Repository](docs://core/global-content-repository/). This powers the [Global Media Library](docs://media/global-media-library/), and can also be used in your custom code for any content you want to store for usage across the network.

Unlike WordPress, Altis does not distinguish between subdomain and subdirectory multisite types. Users can create sites of either type, or use custom domains.

Altis also sets the [large network flags](https://developer.wordpress.org/reference/functions/wp_is_large_network/) by default. This ensures that sites scale much more predictably as sites expand, but can sometimes lead to compatibility issues in code not designed to properly paginate data.


## Deploying your changes

Our cloud environments use a multi-server architecture based on Docker containers, so you can't (S)FTP into them.

Instead, changes need to be committed to your codebase. We'll automatically build them for you, and you can then deploy them when you're ready. (Check out our [deploy guide for more information](deploy.md).)

Because of this build process, we also strongly recommend that you don't commit built files to your repository (like minified JS or CSS). Instead, follow [our guides](docs://cloud/build-scripts/) and run Webpack (or other tools) as part of the automated build process instead.


## Debugging your site and performing maintenance

Because of Altis' multi-server architecture, it's not possible to log into "the" web server. However, we provide [shell access](docs://cloud/dashboard/cli/), which connects to a "sandbox" container alongside the live containers (and connected to your live database).

You can use shell access to run [wp-cli](https://wp-cli.org/) commands, allowing you to run management commands. You can even use `wp db query` to connect to your database and run SQL commands.

For debugging, you can use [X-Ray](docs://cloud/dashboard/x-ray/) to dive into any request. This works like other application performance monitoring (APM) tools like New Relic, but contains *every* non-cached request to your backend to help you debug. On local environments, we bundle [Query Monitor](docs://dev-tools/) and extend it with additional Altis-specific functionality.


## Migrating an existing WordPress site to Altis

We have [a step-by-step guide to moving an existing WordPress site](docs://guides/migrating-from-wordpress/). This will walk you through converting an existing codebase across to Altis.

Our [launch guidance](docs://guides/launching-a-site-on-altis/) covers how to migrate your database and uploads across to Altis as well.
