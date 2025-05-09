# Updating WordPress

We recommend that you automate updates to your Altis project using the techniques described
in [Automating Updates](./automatic-updates.md).

However, if you want to update to the latest WordPress security release without updating all other Altis modules, you can do so by
running the following command:

```bash
composer update altis/cms --with-dependencies
```

This will update the `altis/cms` module to the latest version and update any dependencies that are required for that
version including WordPress itself.

Note this will only update bug fix and security releases of WordPress. If there is a new major version of WordPress, that will be
included in a later version of Altis. If you want to update to a new major version you will need to update to the latest version of
Altis.
