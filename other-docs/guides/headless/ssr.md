# Server-Side Rendering

Altis provides the ability to render JavaScript-based frontends on the server.

Server-side rendering allows you to provide faster load times to users, as well as a better experience for users without JavaScript or search engines.

This server-side rendering is done through [v8js](https://github.com/phpv8/v8js), a JavaScript engine integrated into PHP. v8js is installed and configured on Altis, as well as the local development environment provided by Altis.

Altis maintains the [react-wp-ssr library](https://github.com/humanmade/react-wp-ssr) which can be plugged into an existing React application to enable server-side rendering.


## Prerequisites

This guide will assume you're adding server-side rendering to an existing React-based theme using react-wp-scripts. Follow the [Building a React app guide](react-app.md) to set up a new theme, or consult the [react-wp-ssr documentation](https://github.com/humanmade/react-wp-ssr) for information about setting up react-wp-ssr with other projects.


## Adding the JavaScript library

To get started, add the JS library to your application:

```sh
npm install --save react-wp-ssr
```

In your application, replace the `ReactDOM.render` call with a call to `react-wp-ssr`'s `render` function:

```js
import React from 'react';
import render from 'react-wp-ssr';

import App from './App';

render( () => <App /> );
```

You do not need to provide a root container ID, as this will provided by the PHP framework.


## Enabling server-side rendering

To enable server-side rendering, first add react-wp-ssr to your project's Composer packages:

```sh
composer require humanmade/react-wp-ssr
```

Next, call the PHP API's `render` function in your theme. This should replace the root container for your application. We recommend using a minimal `index.php`:

```php
<?php

get_header();

ReactWPSSR\render( get_stylesheet_directory() );

get_footer();
```

Your site should now render your application as before. In your browser's console, you should see a message from react-wp-ssr:

```txt
Skipping server-side render in development.

Rendering in development may cause hydration errors, as the server renders
from your built bundle, not from your development script.

Add `define( 'SSR_DEBUG_ENABLE', true )` to your wp-config to enable
in development
```

To test out server-side rendering, first ensure the built version of your application is up-to-date by rebuilding:

```sh
npm run build
```

Then add the constant to your configuration:

```php
define( 'SSR_DEBUG_ENABLE', true );

// Optionally, set the following constant to `true` to disable loading the
// script on the client. This will render a static site on the server only,
// allowing you to verify server-side rendering is working correctly:
define( 'SSR_DEBUG_SERVER_ONLY', false );
```

Refresh the page and view the page's source. You should see your application rendered statically into the HTML by the server.


## Limitations

react-wp-ssr comes with [several limitations](https://github.com/humanmade/react-wp-ssr/blob/master/docs/limitations.md) that you should be aware of. Most well-built applications will work without any modifications, but some applications may require modifications to adapt to these modifications.

For questions about adapting your application to server-side rendering, consult the Altis team.


## Using v8js Directly

v8js's [PHP API](https://github.com/phpv8/v8js#php-api) can be used directly to execute JavaScript as part of your backend request.

To run JavaScript code on the server, instantiate V8Js and use the `executeString` method:

```php
$v8 = new V8Js();
$result = $v8->executeString( '42 * 3.14;' );
```

Generally, we recommend avoiding using v8js directly, as it can be complex to configure, secure, and make performant. As v8js uses a full JavaScript engine, it can have considerably different performance characteristics. Additionally, data that is passed between PHP and JavaScript needs to be correctly sanitized and escaped for both contexts.

Ensure that any custom code is heavily tested on development and staging environments before deploying to production.
