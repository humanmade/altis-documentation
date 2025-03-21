# How do I import an external database into an Altis environment?

To import an external database dump into an Altis environment, the process 
will depend on how large the dump is you wish to import. For large database dumps (10GB or more), [contact Altis support](https://docs.altis-dxp.com/guides/getting-help-with-altis/), and we will import the database on your behalf. To import smaller databases, you'll predominately use the [CLI via Altis Dashboard](https://docs.altis-dxp.com/cloud/dashboard/cli/). 

Note: The Sandbox has a maximum 10GB of disk space available mounted to `/usr/src/app/`.

## Copying the dump to the Sandbox

The first step is to copy the database dump to your Altis environment sandbox so you can run operations on it. 

For those comfortable installing and using CLI tools locally, you can copy files to the Sandbox, via the [local Altis CLI tool](https://github.com/humanmade/altis-cli). This will allow you to `scp` directly to the Sandbox. 

Another method would be to run `wget` from the sandbox. You must have a pre-signed download link to your database dump. `wget` is preinstalled on the Sandbox.

The target location for your database dump on the Sandbox should be `/usr/src/app/`, the home directory of your application. This volume's storage capacity is 10gb.

Note: the sandbox storage is non-permanent and may be cleared if the underlying machine is replaced (such as due to hardware failure), so we'd recommend running the import straight after you've copied a DB dump to the sandbox.

## Importing a database

Now the dump exists in the Sandbox, you can run operations. The most [direct method of importing](https://developer.wordpress.org/cli/commands/db/import/) Databases for your WordPress website, is to use `wp db import mysqldump.sql`. 

Note: Depending on the size of your database, this might take a while, so it's best to run this in a [`screen` session](https://techoverflow.net/2021/12/24/how-to-use-screen-sessions-in-linux/). Screen is preinstalled on the Sandbox. 

## Search replace the database

When importing databases into non-production environments, a search-replace may be needed.

Use [`wp search-replace`](https://developer.wordpress.org/cli/commands/search-replace/) to update URLs from their original to your Altis domain. 

E.G `wp search-replace example.com example-dev.altis.cloud --dry-run` - note 
the `--dry-run` flag, this is good practice prior to any `search replace` operation to review the replacements to be made. Also note the HTTP protocol is omitted from the URLs - if you run a search-replace that include the protocol, this may cause irreversible issues to the database, so avoid using them.

Finally, flush the object cache! Run `wp cache flush`. 

