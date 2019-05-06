# Upgrading

When new versions of HM Platform are released you will need to manually upgrade your project to the new version. New versions can bring anything from breaking changes to new features. It's important you read the changelog / upgrade notes for the specific version you are upgrading to. When upgrading multiple versions at once, be sure to follow the release notes on all intermediate versions.

To switch the version of HM Platform for your project, modify the version constraint for the `humanmade/platform` dependency in your `composer.json`. For example, to upgrade to HM Platform version 2.

```json
{
	"name": "company-name/my-site",
	"require": {
		"humanmade/platform": "2.0.0"
	}
}
```

Next, run `composer update` to pull in the latest version of the package. This will generate a new `composer.lock` file. Both the `composer.json` and `composer.lock` should be committed to version control, and deployed.

Any upgrade will usually require some modification to your project (for example, deprecated APIs, new features you may want to implement). HM Platform is conservative about breaking backwards compatibility. Any known issues will be prefixed with "BREAKING: " in the version release notes.
