# Updating PHP Version

On occasion Altis will make a PHP version update available and once a particular version of PHP is out of support updating will become mandatory to help protect the security of your application.

There are 2 key steps to getting ready for a new version of PHP:

- Checking your application is compatible with the new version and making any necessary updates
- Requesting the PHP upgrade for your cloud environments

## Altis Compatiblity Chart

|Altis|PHP Version|
|-|-|
|v7|7.2-7.4|
|v6|7.2-7.4|
|v5|7.2-7.4|
|v4|7.0-7.4|
|v3|7.0-7.2|
|v2|7.0-7.2|
|v1|7.0-7.2|

## Checking PHP Version Compatibiliity

**Note:** Before carrying out the following tasks locally ensure your application is fully installed so that all code that will be deployed to the cloud is present.

### Update All Altis Modules

The first task is to ensure your application has the latest patch versions of all Altis modules by running the following command in your project root:

```
composer update "altis/*" --with-all-dependencies
```

Afterwards commit the updated `composer.lock` file.

### Run The Compatibility Check

The next step is using the PHP Compatibility package for PHP Code Sniffer. The guide belows shows you how to install and use this tool globally, however you may wish to install them directly in your project and use them in CI as part of your code linting process.

First install the standard and dependencies using the following command:

```
composer global require dealerdirect/phpcodesniffer-composer-installer phpcompatibility/php-compatibility
```

Next run the standard against your codebase for the target PHP version, in this example PHP 7.4:

```
phpcs -p . --standard=PHPCompatibility
  --runtime-set testVersion 7.4 \
  --extensions=php \
  -d memory_limit=1G \
  --ignore=wordpress,vendor/altis,*/tests/*
```

**Note:** You _must_ fix anything reported as an "Error". Warnings can be ignored at your discretion.

Fix any reported errors (ignoring those listed in the section below) by upgrading affected third party plugins and amending first party custom code as needed following your standard code review and deployment processes.

#### Known Warnings and Errors

There are some known warnings in the following packages that you can safely ignore. Altis guarantees its core packages and their dependencies are verified as working with the versions outlined in the compatibility chart at the top of this page.

- `aws/aws-sdk-php`
- `guzzlehttp/promises`
- `humanmade/batcache`
- `humanmade/ludicrousdb`
- `humanmade/wp-redis`
- `phpunit/phpunit`
- `sebastian/global-state`
- `sebastian/object-enumerator`
- `symfony/yaml`
- `wp-phpunit/wp-phpunit`


## Requesting A Cloud Environment Update

Once you are confident that your application is compatible with the version of PHP to upgrade to you should do the following:

1. Deploy the updated application to the environment you wish to update, we recommend starting with your non production environments first
2. Create a support ticket for the target environment with the type "Task", titled "Upgrade to PHP 7.4", replacing "7.4" with the target version if necessary

See the [Getting Help With Altis guide](../getting-help-with-altis.md) for more information on creating support tickets.

The Altis team will follow up with you on the ticket once the environment has been updated.