# Updating Elasticsearch Version

The Elasticsearch service is core to the advanced search and native analytics features of Altis. Newer versions unlock newer features, more up to date stopword dictionaries and stemming support as well as more advanced data processing tools.

## Supported Versions

Altis v5+ is compatible with the following Elasticsearch versions:

- 7.10
- 6.8
- 6.3

## Updating Local Environments

It is recommended to update your local environment's Elasticsearch version before requesting an upgrade to your Cloud environments to confirm that any custom code you have that interacts with Elasticsearch still works.

1. Take a database back up of your existing local environment using WP CLI:
   - Local Server: `composer server exec -- wp db export database.sql`
   - Local Chassis: `composer chassis exec -- wp db export database.sql`
1. Destroy your existing local environment:
   - Local Server: `composer server destroy`
   - Local Chassis: `composer chassis destroy`

1. Require Local Server or Local Chassis version 8.1 or higher:
   ```sh
   composer require altis/local-server:^8.1.0-rc
   composer require altis/local-chassis:^8.1.0-rc
   ```
1. Upgrade if using Local Chassis by running `composer chassis upgrade`
1. Start your environment and re-import your data:
   - Local Server: `composer server start && composer server exec -- wp db import database.sql`
   - Local Chassis: `composer chassis start && composer chassis exec -- wp db import database.sql`

**Note:** These local environment versions should be backwards compatible with older Altis versions but it is not guaranteed. Contact support if you experience issues.

This will change the default Elasticsearch version to 7.10.

The Elasticsearch version can also be configured for each local environment should you wish to use an older version. Consult the documentation for each to find out how to do this.

## Requesting An Update To A Cloud Environment

To update Elasticsearch on your Cloud environments you must raise a support request with the type "Request and infrastructure change".

[See the "Getting Help With Altis" guide to learn how to raise support requests](../getting-help-with-altis.md).

Select the application(s) you want to upgrade and the version you would like to upgrade to in the subject line, for example:

> Upgrade to Elasticsearch 7.10

A support team member will respond to acknowledge the request and inform you with the time when the upgrade will be started.

For production environments we will aim to perform the upgrade outside of your primary operating hours.

Upgrades can take a long time depending on the amount of data currently indexed.

If you are upgrading from Elasticsearch 6.2 (or any version lower than 6.8) to 7.10 then the upgrade will happen in 2 stages, first to 6.8 and then to 7.10. This will take longer and may happen over 2-3 days.

You will be notified via the support ticket of each stage of the upgrade process.
