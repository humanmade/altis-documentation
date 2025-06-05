# 3rd Party Compatibility

The [WordPress plugin ecosystem](https://wordpress.org/plugins/) offers a huge variety of features and tools that can supercharge
your application even further than those provided out of the box by Altis.

This guide outlines known compatibility issues and remediation for the most popular third party plugins used on the Altis platform.

## MultilingualPress Pro

[MultilingualPress](https://multilingualpress.org/) by Syde provides advanced capabilities for connecting content across
sites on a network and detecting user language preferences.

**Known issues:**

- MultilingualPress prior to version 5.0 uses built-in caches that are not compatible or required with Altis' object caching.
  We recommended you switch off the built-in caches via the MultilingualPress settings screen.
- From version 5.0 MultilingualPress turns off its internal caches by default, so this is no longer an issue.
