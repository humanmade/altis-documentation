# Isolating and Debugging Issues

Before reporting problems in Altis, it's important to isolate issues to ensure they are occurring in Altis modules, rather than
within your custom codebase.

Due to the complex nature of WordPress plugins, even when an error may appear to be coming from Altis modules, it may be caused by
an errant action or filter in a different plugin.

Please note that Altis does not take responsibility for your custom codebase, and cannot assist in errors which come from
third-party plugins or your custom codebase.

## Check for updates

Altis releases minor updates constantly, so you should ensure you are running the latest minor versions before contacting support.

To check for new versions, run `composer outdated`.

You can update Altis and its dependencies by running `composer update -W altis/altis`.

## Replicating issues with a fresh installation

The most straightforward step to replicating issues in Altis is to start with a fresh installation with no custom code.

You should run this using the provided local environments, as these are **replicate the Altis infrastructure**; there is generally
no need to test in Cloud environments.

Follow the [Getting Started guide's installation process](docs://getting-started/#creating-a-new-altis-project) to create a new
Altis project from scratch. As a shortcut, here's the command:

```sh
composer create-project altis/skeleton my-test-project
```

(Replace `my-test-project` with any name you like.)

Once created, use [Local Server](docs://local-server) to run locally, and attempt to replicate the issue.

We recommend leaving configuration at its default settings initially, and turning on any configuration progressively as necessary.

If you are able to replicate a problem in a fresh installation or with only configuration changes, this is likely to be a bug in
Altis. Report the bug to Altis Support; if you made configuration changes, please ensure you include this with your report.

## Finding where an error is occurring

For errors which appear to come from Altis, ensure you check the full trace for the error.

A common problem we see is when issues occur due to plugins or custom code incorrectly calling internal APIs. This can trigger an
error within Altis modules, but a trace may reveal the source of the issue.

The [Altis Dev Tools](docs://dev-tools/) can help you to identify the source of issues, as it shows traces for errors which occur.
Additionally, [X-Ray](docs://cloud/dashboard/x-ray/) can help identify issues occurring in Cloud environments.
