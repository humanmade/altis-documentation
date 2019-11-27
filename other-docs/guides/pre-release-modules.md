# Updating Modules to Pre-Release Versions

On occasion when there is a new feature ready for the next major version of Altis that is compatible with the previous version we may create a pre-release version.

Early adopters and those wishing to test or implement the new feature ahead of time can do so by opting in to the new module version.

## Opting in

To opt in to a pre-release of a module you need to require it directly in your root `composer.json`. The command to run is:

```
composer require altis/<module> <version>@RC --update-with-dependencies
```

This will override the version constraint in the `altis/altis` meta package. The `@RC` modifier also overrides your project's minimum stability setting.

The reason these are released as "Release Candidates" is because we do not provide long term support for them. For instance if there is a `2.1.0-rc` version as soon `2.2.0-rc` is released then `2.1.0-rc` will no longer be patched. The features provided in these release candidates will always be part of the following major Altis release.

## Example

The following is an example of updating to Altis CMS 2.1 (which contains WordPress 5.3) in Altis v2. From your project root folder in an Altis v2 install you would run the following command.

```
composer require altis/cms ^2.1.0@RC --update-with-dependencies
```
