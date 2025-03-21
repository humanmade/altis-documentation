# Importing assets to your Altis Environment

Assets uploaded to Altis are stored in dedicated asset storage, powered by Amazon S3. This storage is flexible and able to scale to any size, while remaining resilient and available at all times.

This guide will help you in migrating smaller sets of assets to an Altis Environment. If you're performing a large data import or are migrating onto our platform, [contact Altis support](https://docs.altis-dxp.com/guides/getting-help-with-altis/). For example, if your assets already exist on S3, support can arrange an S3 to S3 transfer. 

To import new or delta assets into your Altis Environment, the process will depend on the total size of the assets you wish to import. For large sets of assets (10GB or more), [contact Altis support](https://docs.altis-dxp.com/guides/getting-help-with-altis/), and we will import them on your behalf. For small assets imports, you can use the [CLI via Altis Dashboard](https://docs.altis-dxp.com/cloud/dashboard/cli/).

Note: The Sandbox has a maximum 10GB of disk space available mounted to `/usr/src/app/`.

## Transfer assets to the Sandbox

For those comfortable installing and using CLI tools locally, you can copy files to the Sandbox, via the [local Altis CLI tool](https://github.com/humanmade/altis-cli). This will allow you to `scp` directly to the Sandbox. The Altis CLI is currently experimental with minimal documentation, so debugging issues with this method will have to be self-guided.

Alternatively, you can use `wget` to download files to the sandbox. To download files with `wget`, you will need a web accessible link to the files.

## Sync the assets to S3

Once you have your assets in place, you can run `wp s3-uploads` to copy the assets to S3. The target destination needs to use an S3 location. To get your environment S3 bucket info, run `env | grep S3_UPLOADS_BUCKET`, and this will return the S3 Bucket for this environment, which you will use as part of the target destination. 
Transferring the assets to S3 would be done with a command that looks like this; 

```shell
wp s3-uploads cp ./myassets s3://S3_UPLOADS_BUCKET_VALUE/uploads/sites/02
```


