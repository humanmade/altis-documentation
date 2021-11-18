# Automating Updates

The recommended approach to automating updates to Altis is to use GitHub's Dependabot feature. This feature is provided for free by GitHub.

The service monitors the dependencies in your project and creates automatic pull requests whenever there are updates. This will help you to keep your projects healthy and always running the latest patch release of all Altis modules.

## Setting Up Dependabot

To get started create a `dependabot.yml` file in your project's `.github` directory, or if you already have an existing `dependabot.yml`, add the following config to it.

The minimum recommended configuration for Altis updates is as follows:

```yaml
version: 2
updates:
# Enable version updates for Altis modules
- package-ecosystem: composer
  # Look for `composer.json` and `composer.lock` files in the `root` directory
  directory: /
  # Create pull requests for updates (if any) once a day:
  schedule:
    interval: daily
  versioning-strategy: lockfile-only
  # Ensure all Altis modules recieve update PRs
  allow:
  - dependency-name: altis/*
  - dependency-type: all
  # Increase limit to number of Altis modules
  open-pull-requests-limit: 15
```

Finally commit this file to your repo, and you're done.

## Automatically Merging Dependabot PRs

You can also optionally use the [Dependabot Auto Merge GitHub Action](https://github.com/marketplace/actions/dependabot-auto-merge) to automatically merge Dependabot Pull Requests for Altis modules.

To get started add the following to a new workflow called `.github/workflows/auto-merge.yml`:

```yaml
name: auto-merge

on:
  pull_request_target:

jobs:
  auto-merge:
    runs-on: ubuntu-latest
    steps:
      - uses: ahmadnassri/action-dependabot-auto-merge@v2
        with:
          github-token: ${{ secrets.mytoken }}
```

Secondly to target Altis modules only add a config file called `.github/auto-merge.yml` with the following configuration:

```yaml
- match:
    dependency_name: altis/*
    dependency_type: all
    update_type: semver:patch
```

## Further Configuration

There are many more configuration options available and you can add additional update rules for different package ecosystems like npm to this file as well.

[The full Dependabot configuration documentation can be found here](https://help.github.com/en/github/administering-a-repository/configuration-options-for-dependency-updates).
