# Automating Updates

The recommended approach to automating updates to Altis is to use GitHub's Dependabot feature. This feature is provided for free by GitHub.

The service monitors the dependencies in your project and creates automatic pull requests whenever there are updates. This will help you to keep your projects healthy and always running the latest patch release of all Altis modules.

## Setting Up Dependabot

To get started create a `dependabot.yml` file in your project root.

The minimum recommended configuration for Altis is as follows:

```yaml
version: 2
updates:
  # Enable version updates for Composer
  - package-ecosystem: "composer"
    # Look for `composer.json` and `composer.lock` files in the `root` directory
    directory: "/"
    # Create pull requests as soon as updates are made available
    schedule:
      interval: "live"
    # Increase the version requirements for Composer
    # only when required
    versioning-strategy: increase-if-necessary
```

Finally commit this file to your repo, and you're done.

## Further Configuration

There are many more configuration options available and you can add additional update rules for different package ecosystems like npm to this file as well.

[The full Dependabot configuration documentation can be found here](https://help.github.com/en/github/administering-a-repository/configuration-options-for-dependency-updates).
