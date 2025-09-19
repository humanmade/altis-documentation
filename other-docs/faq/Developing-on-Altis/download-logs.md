# Can I download logs from the Altis Dashboard?

Yes. It's possible to download CDN Access logs from the Altis Dashboard, but as yet no other log types are available for download.

If you need to download logs other than the Access Logs, or can't find what you're looking for, [contact us](https://docs.altis-dxp.com/guides/getting-help-with-altis/) and we can assist you.

See our article on [logs and their retention periods](docs://cloud/dashboard/logs/) before requesting logs.

## Downloading Access Logs

To download Access Logs, find the environment you want in Altis Dashboard and select it.

Then, click on the "Logs" sub menu item, followed by the "Access Logs" tab.

From here you can then select a date range and further filter the data, as well as choosing either CSV or JSON format.

### Host name

To get logs for a specific host name choose the **"Host Header (x-host-header)"** field from the dropdown, and then enter the bare domain name as the value.

### URL Path

To filter by URL path choose the **"URI Path (cs-uri-stem)"** field from the dropdown, and then enter the full path including the leading and any trailing slashes as the value.
