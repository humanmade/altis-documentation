#  How to move uploads in bulk from one directory to another. 

The AWS CLI is installed on the Sandbox instance for each Altis Environment. To access the Sandbox, simply head to the CLI Tab on the selected Altis Environment you wish to work on.

To copy a directory to another, first get the S3 Uploads Bucket for your environment by running the following:

```
$ env | grep S3_UPLOADS_BUCKET=
```

This should return your environment variable and the value. The value of the environment variable is your S3 Bucket for this environment.

We can ensure all the permissions are still functioning as expected by running:

```
$ aws s3 ls s3://copied-uploads-bucket-path/
```

This should return a list of directories you have access to.

## Operations

The most common operations are `$ aws s3 cp *src dest*` and `$ aws s3 sync *src dest*.`.

The `cp` command copies objects from the source directory to the destination.

The `sync` command copies any missing objects from the source to the destination.

For more advanced operations, see the AWS CLI documentation:

https://docs.aws.amazon.com/cli/latest/reference/s3/#single-local-file-and-s3-object-operations

## Example

A common use case is a change of sub-site ID, so you want to copy the uploads from `uploads/sites/10/` to `uploads/sites/11/`

Assuming the destination directory is empty the command would look like this:

```
$ aws s3 cp s3://copied-uploads-bucket-path/uploads/sites/10/ s3://copied-uploads-bucket-path/uploads/sites/11/ --recursive --acl=public-read
```
