#### Lazy loading via GaussHolder functionality

As WordPress provides lazy loading of images by default Altis no longer provides
that functionality. See
the [Lazy Loading announcement](https://make.wordpress.org/core/2020/07/14/lazy-loading-images-in-5-5/)
for more details.

If you still require the Gaussholder image placeholder functionality, you can
add [humanmade/Gaussholder](https://github.com/humanmade/Gaussholder) to your project.

```bash
## Add Gaussholder library
composer require "humanmade/gaussholder"
```

Then hook into the `gaussholder.image_sizes` filter.

```php
add_filter( 'gaussholder.image_sizes', function ( $sizes ) {
	$sizes['medium'] = 16;
	$sizes['large'] = 32;
	$sizes['full'] = 84;
	return $sizes;
} );
```

Note: You can experiment to determine the best sizes (blur radius) to use. See
the [documentation on Github](https://github.com/humanmade/Gaussholder#readme).
