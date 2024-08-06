# Updating Elasticsearch Version

The Elasticsearch service is core to the advanced search and native analytics features of Altis. Newer versions unlock newer
features, more up to date stopword dictionaries and stemming support as well as more advanced data processing tools.

## Supported Versions

Altis v5+ is compatible with the following Elasticsearch versions:

- 7.10
- 6.8
- 6.3

For all Altis environments created before 2021-09-01, the default Elasticsearch version is 6.3. For all environments created after
this date, the default version is 7.10.

## Prerequisites

There several things you must ensure have been done before updating your Elasticsearch version locally _or_ when requesting an
update for a cloud environment.

These requirements will help to avoid any errors and delays.

1. Ensure all Altis modules are fully up to date by running `composer update "altis/*" --with-all-dependencies`
1. If the upgrade is for a cloud environment, ensure the updated `composer.lock` has been committed and deployed
1. If any patch updates were downloaded, in particular for the Cloud or Search modules you should reindex your content using one of 
   the following commands:
   - Local Server: `composer server cli -- elasticpress index --setup --network-wide`
   - Cloud: `wp elasticpress index --setup --network-wide`

After completing the above steps to ensure your environment is ready for the Elasticsearch upgrade, you can then proceed to the
steps below.

## Updating Local Environments

It is recommended to update your local environment's Elasticsearch version before requesting an upgrade to your Cloud environments
to confirm that any custom code you have that interacts with Elasticsearch still works.

1. Take a database back up of your existing local environment using WP CLI:
    - Local Server: `composer server exec -- wp db export database.sql`
1. Destroy your existing local environment:
    - Local Server: `composer server destroy`
1. Require Local Server version 9.0.0 or higher:

  ```sh
  composer require --dev --update-with-dependencies altis/local-server:^9.0.0
  ```

1. Start your environment and re-import your data:
    - Local Server: `composer server start && composer server exec -- wp db import database.sql`

**Note:** These local environment versions should be backwards compatible with older Altis versions but it is not guaranteed.
Contact support if you experience issues.

This will change the default Elasticsearch version to 7.10.

The Elasticsearch version can also be configured for each local environment should you wish to use an older version. Consult the
documentation for each to find out how to do this.

## Requesting An Update To A Cloud Environment

To update Elasticsearch on your Cloud environments you must raise a support request with the type "Request a change".

[See the "Getting Help With Altis" guide to learn how to raise support requests](../getting-help-with-altis.md).

Select the application(s) you want to upgrade and the version you would like to upgrade to in the subject line, for example:

> Upgrade to Elasticsearch 7.10

A support team member will respond to acknowledge the request and inform you with the time when the upgrade will be started.

For production environments we will aim to perform the upgrade outside of your primary operating hours.

Upgrades can take a long time depending on the amount of data currently indexed.

If you are upgrading from Elasticsearch 6.2 (or any version lower than 6.8) to 7.10 then the upgrade will happen in 2 stages, first
to 6.8 and then to 7.10. This will take longer and may happen over 2-3 days.

You will be notified via the support ticket of each stage of the upgrade process.
