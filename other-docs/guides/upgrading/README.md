# Upgrading

_If you are migrating an existing install to Altis check out the [migrating guide here](../migrating-from-wordpress.md) first._

When new versions of Altis are released you will need to manually upgrade your project to the new version. New versions can bring anything from breaking changes to new features. It's important you read the changelog / upgrade notes for the specific version you are upgrading to. When upgrading multiple versions at once, be sure to follow the release notes on all intermediate versions.

To switch the version of Altis for your project, modify the version constraint for the `altis/altis` dependency in your `composer.json`. For example, to upgrade to Altis version 2.

```json
{
	"name": "company-name/my-site",
	"require": {
		"altis/altis": "^2.0.0"
	},
	"require-dev": {
		"altis/local-chassis": "^2.0.0",
		"altis/local-server": "^2.0.0"
	}
}
```

Next run `rm -rf vendor` on MacOS or Linux, or on Windows `rmdir vendor`.

*Note:* this is due to an issue in how Composer handles installation of `composer-plugin` type packages, and ensures the latest version of the package is used during the upgrade process. We are working to improve this process in future releases.

Next, run `composer update` to pull in the latest version of the packages.

This will generate a new `composer.lock` file. Both the `composer.json` and `composer.lock` should be committed to version control, and deployed.

Any upgrade will usually require some modification to your project (for example, deprecated APIs, new features you may want to implement). Altis is conservative about breaking backwards compatibility. Any known issues will be prefixed with "BREAKING: " in the version release notes.

## Upgrade Guides

- [Version 4](./v4.md)
- [Version 3](./v3.md)
- [Version 2](./v2.md)
