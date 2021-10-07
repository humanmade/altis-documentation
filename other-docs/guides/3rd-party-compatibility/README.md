# 3rd Party Compatibility

The [WordPress plugin ecosystem](https://wordpress.org/plugins/) offers a huge variety of features and tools that can supercharge your application even further than those provided out of the box by Altis.

This guide outlines known compatibility issues and remediations for the most popular third party plugins used on the Altis platform.

## MultilingualPress Pro

[MultilingualPress](https://multilingualpress.org/) by Inpsyde provides advanced capabilities for connecting content across sites on a network and detecting user language preferences.

**Known issues:**

- MultilingualPress uses built in caches that are not compatible or required with Altis' object caching. It is recommended to switch off the built in caches via the MultilingualPress settings screen.
