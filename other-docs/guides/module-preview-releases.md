# Module Preview Releases

On occasion when there is a new feature ready for the next major version of Altis that is compatible with the previous version we may create a preview release.

Early adopters and those wishing to test or implement the new feature ahead of time can do so by opting in to the new module version.

## Opting in

To opt in to a preview release of a module you need to require it directly in your root `composer.json`. The command to run is:

```
composer require altis/<module> <version>@RC --update-with-dependencies
```

This will override the version constraint in the `altis/altis` meta package for the target module only. The `@RC` modifier allows you to use a different minimum stability setting for the target package only. All other packages will be resolved using the default minimum stability setting.

Module pre-releases are "Release Candidates" and are not covered by the long term support policy. For instance if there is a `2.1.0-rc` version as soon as `2.2.0-rc` is released then `2.1.0-rc` will no longer be maintained. The features provided in these preview releases will always be part of the following major Altis release.

## Example

The following is an example of updating to Altis CMS 2.1 (which contains WordPress 5.3) in Altis v2. From your project root folder in an Altis v2 install you would run the following command.

```
composer require altis/cms ^2.1.0@RC --update-with-dependencies
```
