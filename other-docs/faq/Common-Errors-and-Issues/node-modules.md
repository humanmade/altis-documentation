# Node modules not updated after deployment

Node modules are cached on the build server for faster deployments. It can be the case that your updates to Node modules, or Node
versions themselves are not updated after a deployment.

In this case, you must clear your build cache and redeploy.

To do this, goto the Altis Dashboard release tab, click "advanced" on the deploy menu, and select 'Clear Build Cache'; Then Select "
Force Rebuild". This will now rebuild your deployment package, and ensure the proper Node version and Node modules are pulled in as
per your build script.
