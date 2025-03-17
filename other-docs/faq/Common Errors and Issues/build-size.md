# Can't deploy due to build size 

If your deployment is throwing an error and failing with the message:

```
Error: The build image size (... MB) exceeds the maximum allowed size for your environment (640 MB)

```

Then your build image has exceeded the build size limit, and must be reduced to below 640MB.

You can look at the following areas to reduce build size:

- Add `--prefer-dist` to the composer install command
- If you are building JS / CSS assets with tools installed via npm or yarn ensure you're removing `node_modules` afterwards.
- Once the above is done, ensure you clear the build cache in the Altis Dashboard before re-trying the deployment (contact support to clear the build cache)


In some cases, a large file may have been committed to your repository and is being deploy; so check your code repository for large files.

It can also be the case that your Node modules directory has inflated the build size. These help with build speed, but are not required to be deployed onto your environments and can be safely trimmed as part of your build-script.

To remove your Node modules directory via your build-script, you can add something like:

# Clean up node_modules to reduce the size of the build
```
find . -name 'node_modules' -type d -prune -exec rm -rf '{}' +
```

You can read more about build script limitations in this article: https://docs.altis-dxp.com/cloud/build-scripts/#limits
