# Getting Started

![Getting started banner](./assets/banner-getting-started.png)

This guide covers getting started with a brand new Altis project.

After reading this guide, you'll know:

- How to start a new Altis project
- How to run your Altis project locally
- The layout of an Altis project

## Core concepts in Altis

Before you get started, it's important to be aware of a few core concepts which affect how you think about and build sites for
Altis.

With Altis, you will work on a **project**. This encompasses the codebase and the hosting provided by Altis Cloud. Each project can
have multiple **sites** ("multisite"), which can represent multiple domains, subdomains, or other distinct websites. Sites within a
single project share a common codebase, but can have different customizations applied to each site.

Altis is built on top of a WordPress foundation. WordPress and other dependencies of Altis are managed for you as part of the Altis
version.

Custom functionality on top of the Altis platform is implemented via WordPress **plugins**, and Altis is generally compatible with
most WordPress plugins. (Subject to our [cloud environment limitations](docs://cloud/limitations.md).)

Design and styling of sites is implemented via WordPress **themes**. Projects can contain many themes, and each site has a single
theme active. Themes can be shared across sites, or used on only a single site.

Development work on your project will take place on your **local environment**, which is a full copy of the cloud environment you
run locally with Docker. For testing and quality assurance, you'll use your **non-production cloud environment(s)** including
development and staging. And when you're ready to go live, your **production environment** will serve live traffic to users. (
Collectively, your cloud environments belong to a single **Altis instance**.)

## Creating a new Altis project

The quickest way to get started with Altis is to use Composer. If you don't already have
Composer, [follow the Composer installation guide](https://getcomposer.org/download/).

To get started, run:

```shell
composer create-project altis/skeleton my-project
```

**Note:** We recommend Composer v2. Run `composer self-update` to ensure you're using the latest version.

Follow the interactive prompts to get started.

This will create a new directory called `my-project`, set up the project, and install the Composer dependencies. This directory is
now ready for you to start working on your project.

Composer is used to manage the version of Altis and its dependencies, and you'll need to be familiar with its commands
including `install` and `upgrade`.

## Your project's layout

Your project directory will contain a number of auto-generated files and directories.

- `composer.json` and `composer.lock` - These files allow you to specify the dependencies for your project. These files are used by
  Composer.
- `content/` - Contains all the functional code that makes up your project.
  - `mu-plugins/` - Contains any custom modules for your project.
  - `plugins/` - Contains WordPress plugins which can be activated on a per-site basis.
  - `themes/` - Contains the available themes for your project.
- `index.php` - The main entrypoint used by Altis. Don't edit this file.
- `vendor/` - Contains the third-party dependencies for your project, including Altis.
- `wordpress/` - Contains WordPress, the core CMS used by Altis.
- `wp-config.php` - The main configuration used by Altis. Don't edit this file.

Generally, you'll be mostly working on files inside the `content` directory. This directory contains most of your project-specific
code, including new functionality (in the form of "plugins") and visual styling (in the form of "themes").

To start using your project, you'll need to set up a development environment.

## Running your project locally

For local development, you'll also need to add a local server to your development dependencies. Altis includes a Docker based
development environment out-of-the-box. See the [Local Server documentation](docs://local-server/) for more details.

Local Server requires [Docker Desktop for Mac or Windows](https://www.docker.com/products/docker-desktop), or Docker Engine on
Linux.

To set up Local Server for Altis, run the following inside your project's directory:

```shell
composer server start
```

This will download and start all the services needed for Altis development.

The first time you run Local Server, it may take a little longer than usual, as it downloads the containers. Once this is complete,
you will now have a working local site at <https://my-project.altis.dev/>. Visit `/wp-admin/` and login with `admin` / `password` to
get started!

If you used a directory name other than `my-project` the default URL will be `https://<directory name>.altis.dev`.

To stop Local Server, run `composer server stop`.

To start the virtual machine again run `composer server start`.

### Docker alternative

If you are unable to use Docker on your computer, consider trying
a [GitHub Codespaces environment](docs://dev-tools/cloud-dev-env/), which makes it possible to spin up a complete Altis development
environment within your browser, without having to install any additional software on your computer.

<!-- markdownlint-disable MD026 -->
## Ready for development!
<!-- markdownlint-enable MD026 -->

When you first view your local site, you'll see the Altis splash page. This indicates that the site has been set up, and is awaiting
your custom code.

Not familiar with WordPress already? Check out our guide to working on [your first theme](first-theme.md).

When you're ready to go live with your project, you can [deploy to your cloud environments](deploy.md).
