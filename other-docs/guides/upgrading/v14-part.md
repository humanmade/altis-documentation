This is a temporary file to be added to the content of the
file [v14 upgrade guide](v14.md)  when it is created/merged.

### Modules removed ###

Altis v14 no longer includes the workflows functionality. If you want to use
that functionality, you need to add the framework to your project.
In your top level project folder, add the `humanmade/workflows` framework
package

```sh
## Add Frameworks package
composer require "humanmade/workflows"
```

This provides the framework to create your own workflow functionality as well as
the "Editorial Comments" functionality.

The Publication Checklist feature has been removed from Altis v14. If you wish
to use that functionality you can add the `humanmade/publication-checklist` to
your project.

```sh
## Add publication checklist example
composer require "humanmade/publication-checklist"
```

This will provide you with the framework to write your own publication
checklist. If you want to use the previous Altis demo functionality as a
starting point, take a look at
the [Demo Github repository](https://github.com/humanmade/demo-publication-checklist)
