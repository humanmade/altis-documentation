---
order: 0
---

# Upgrading

*If you are migrating an existing install to Altis check out the [migrating guide](../migrating/) first.*

When new versions of Altis are released you will need to manually upgrade your project to the new version. New versions
can bring anything from breaking changes to new features. It's important you read the changelog / upgrade notes for the
specific version you are upgrading to. When upgrading multiple versions at once, be sure to follow the release notes on
all intermediate versions.

To switch the version of Altis for your project, modify the version constraint for the `altis/altis` dependency in
your `composer.json`. For example, to upgrade to Altis version 21.

```json
{
    "name": "company-name/my-site",
    "require": {
        "altis/altis": "^23.0.0"
    },
    "require-dev": {
        "altis/local-server": "^23.0.0"
    }
}
```

Next run `rm -rf vendor` on MacOS or Linux, or on Windows `rmdir vendor`.

*Note:* this is due to an issue in how Composer handles installation of `composer-plugin` type packages, and ensures the
latest version of the package is used during the upgrade process. We are working to improve this process in future
releases.

Next, run `composer update -W   ` to pull in the latest version of the packages.

This will generate a new `composer.lock` file. Both the `composer.json` and `composer.lock` should be committed to
version control, and deployed.

Any upgrade will usually require some modification to your project (for example, deprecated APIs, new features you may
want to implement). Altis is conservative about breaking backwards compatibility. Any known issues will be prefixed
with "BREAKING: " in the version release notes.

## Upgrade Guides

- [Version 23](./v23.md)
- [Version 22](./v22.md)
- [Version 21](./v21.md)
- [Version 20](./v20.md)
- [Version 19](./v19.md)
- [Version 18](./v18.md)
- [Version 17](./v17.md)
- [Version 16](./v16.md)
- [Version 15](./v15.md)
- [Version 14](./v14.md)
- [Version 13](./v13.md)
- [Version 12](./v12.md)
- [Version 11](./v11.md)
- [Version 10](./v10.md)
- [Version 9](./v9.md)
- [Version 8](./v8.md)
- [Version 7](./v7.md)
- [Version 6](./v6.md)
- [Version 5](./v5.md)
- [Version 4](./v4.md)
- [Version 3](./v3.md)
- [Version 2](./v2.md)
