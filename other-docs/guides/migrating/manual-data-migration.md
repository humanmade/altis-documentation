---
order: 0
---
# Manual data migration

For most sites, we recommend using regular WordPress exports and imports. However, for larger or more complex sites, you may want to do a manual migration instead.

With a manual migration, you'll use a database export along with a copy of your uploads.


## Setting up locally

If you haven't already, now is a good time to get a copy of your data, and import it locally. This will allow you to conduct any necessary migration steps on a local copy of the data using [Local Server](docs://local-server/).

If your data is too large to work with locally, you can use a non-production environment on Altis Cloud instead. We recommend avoiding this for your first migration on Altis, as working with a server over SSH can be slower than working locally.


### Migrating the database

Altis works with existing WordPress databases and most necessary changes will be made automatically for you.

However, if your site is not already on multisite, you will need to convert it. To do so, you will need to import your database locally, perform the migration, and then re-export the database.

**Note:** If you are continuing to publish content to an existing site, we recommend performing these steps using your existing provider. This will minimize the migration process when you need to move.


#### Switching single site to multisite

You can import an existing SQL file into Local Server using the wp-cli import command:

```sh
$ composer server cli -- db import your-database.sql
```

To convert an existing single site to a multisite install, run the following command:

```sh
$ composer server cli core multisite-convert
```


### Importing your database into a cloud environment

Once you have your database backup, you can import it into a cloud environment.

Use the [Altis Dashboard's shell access](docs://cloud/dashboard/cli/) to import the database. Note that Altis does not currently provide the ability to upload files directly to the sandbox server that the shell access uses, so you will need to download files on the server instead.

If you have your SQL file in a web-accessible location, you can use cURL to download it:

```sh
$ curl -sLO https://example.com/your-database.sql
```

You can then import this file into the database using wp-cli:

```sh
$ wp db import your-database.sql
```

For larger exports, or if your SQL files are not web-accessible, the Altis team can upload these files to the server for you. Contact support for more information.


### Transferring uploads

On Altis, uploaded assets are stored in [a separate data store](docs://cloud/s3-storage/), rather than within your content directory.

Just like with the database backup, you can use shell access to import uploads.

If you have a copy of your uploads in a web-accessible location, download them with cURL.

```sh
$ curl -sLO https://example.com/your-uploads.zip
```

Then, unzip them:

```sh
$ unzip your-uploads.zip
```

Finally, you can use the [S3 Uploads import command](https://github.com/humanmade/S3-Uploads#uploading-files-to-s3) to push these files into the S3 storage:

```sh
$ wp s3-uploads upload-directory /path/to/uploads/ uploads
```

Just like with database files, the Altis team can import these for you. Additionally, the Altis team can perform direct copies from an S3 bucket you may already have to your Altis bucket, which can significantly speed up the import process. Contact support for more information.

