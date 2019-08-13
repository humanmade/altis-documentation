---
order: 10
---
# Your First Theme

Themes are used to control the visual style of your sites. Themes can be shared and reused across many sites in a project, and you can also create "child" themes which inherit parts of another theme.

Themes are a core concept of WordPress, the underlying implementation of the CMS module. For further documentation, consult the [WordPress theme documentation](https://developer.wordpress.org/themes/).

Altis includes a minimal starter theme out-of-the-box which simply renders a placeholder. Due to the custom nature of Altis projects, this only includes the bare basics of a theme, allowing you to build your themes from scratch with minimal boilerplate.


## Theme Structure

Themes include several specially-named files which are used to control the theme's behavior. These files must always exist.

* `style.css` - This CSS file must exist and contain a comment header which specifies metadata about the theme.
* `functions.php` - This PHP file acts as the entrypoint to PHP code in your theme. It is loaded like a plugin, but only when the theme is active.
* `index.php` - This PHP file acts the the "default" template file (see below).

When a page is loaded on your site, the CMS attempts to load a "template", which is a PHP file used to render the page. The specific file to be used depends on the type of content being used, and follows an order called the [template hierarchy][]. If no specific template is found, `index.php` is loaded as the "default" template.

[template hierarchy]: https://developer.wordpress.org/themes/basics/template-hierarchy/

Individual templates can be split up into "parts", and various helpers are provided to load in these parts:

* `get_template_part( $slug )` - Loads a template file called `{$slug}.php`. `$slug` may contain slashes to split your template parts into directories.
* `get_header()` - Loads a template file called `header.php`
* `get_footer()` - Loads a template file called `footer.php`
* `get_sidebar()` - Loads a template file called `sidebar.php`


## Comment Header

Metadata about your theme is stored in the `style.css` file in a comment at the start of the file. This is called the "comment header".

In the starter theme, this header looks like:

```css
/*
Theme Name: Base Theme
Author: You

This file is yours to edit and replace.
*/
```

The comment header contains several fields in the format `Key: Value`, and may also contain other unrelated data. [Many keys are available](https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/#basic-structure), but the following should always be set:

* `Theme Name` - This key specifies the name of the theme, and is used to distinguish between different themes in the Appearance menu.
* `Author` - This key specifies the authorship of the theme. If this is not supplied, "Anonymous" will be displayed in the UI.

**Note:** Even if you use different filenames for your CSS, the comment header **must** be set in `style.css`. You can use an empty CSS file apart from the comment header if necessary.


## Starting a Theme

To get started with your first theme, open up `content/themes/base` in your code editor or IDE. You'll notice this comes with the following files out-of-the-box:

* `header.php` and `footer.php` - These are specifically-named template parts (see above), loaded via `get_header()` and `get_footer()` respectively.
* `index.php` - This is the "default" template, used as the fallback template if no other template is found in the [template hierarchy][]
* `style.css` - This is the main CSS file, which contains the comment header.
* `functions.php` - This is the entrypoint to your theme. As a best practice, we suggest using this file only to bootstrap actions and filters for your theme, and moving all function/class declarations to the `inc` directory.
* `inc/namespace.php` - This is the main file for functions in your theme's namespace. We suggest [splitting namespaces and classes out into separate files](https://engineering.hmn.md/standards/style/php/#file-layout).


### The Loop

If you edit the text in `index.php`, you'll see the changes appear on your site when you reload. This is purely static text as it's hardcoded into the theme. Much of the power of the CMS comes from the combination of themes with a concept called ["The Loop"](https://developer.wordpress.org/themes/basics/the-loop/), which is used to work with data queried by the CMS.

Before your template is loaded, the CMS processes the requested URL into a query (called "routing"), then executes this query against the database to fetch the requested content (a process called "querying"). This results in data being fetched and stored, ready for your template to translate into output (called "rendering").

Querying results in one or more items being fetched from the database, conventionally called "posts" (including for custom content types). The Loop is simply the use of built-in rendering functions (called "[template tags][]") to iterate over each queried post and output data.

[template tags]: https://developer.wordpress.org/themes/basics/template-tags/

The core of The Loop is two functions: `have_posts()` and `the_post()`. They are combined into a `while` loop in your template, resulting in a basic loop which looks like:

```php
while ( have_posts() ) :
	the_post();
endwhile;
```

`have_posts()` is the iterator function. This establishes the internal iterator variables, and returns `true` while items still remain in the query.

`the_post()` uses the current item in the query and establishes global variables, which are used by various template tags. In particular, it establishes the global `$post` variable, which contains the current item.

Within the loop, you can use [template tags][] to render data for the current post. These use the global variables established by `the_post()`, applying various transformations to turn them into human-readable data.


### A Basic Loop

One of the most basic loops is to render the post's title, link, and content. In the `index.php` file in your theme, replace the welcome message with the following basic loop:

```php
<?php
while ( have_posts() ):
	the_post();
	?>
	<div id="post-<?php the_ID() ?>">
		<h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
		<div>
			<?php the_content() ?>
		</div>
	</div>
<?php
endwhile;
?>
```

Reload your site, and you'll see the example post created on installation rendered onto the page.

This basic loop uses template tags, recognizable by the `the_` prefix. These functions output their data directly to the page, and are escaped automatically for their typical contexts. To access this data instead, most template tags also have a `get_the_` variant, which returns the data instead.


### More Complex Loops and Templates

For more complex loops, we recommend using a more complex starter theme. [Underscores by Automattic](https://github.com/Automattic/_s) includes a full set of templates, providing much more functionality out of the box.

Consult the [WordPress theme documentation](https://developer.wordpress.org/themes/) for more information about loops, the template hierarchy, and other parts of themes.


## Next Steps

With a visual style established, the next step to getting started is to begin enabling custom functionality for your site. This is controlled through the [project's configuration](configuration.md).
