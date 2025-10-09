# Can I download logs from the Altis Dashboard?

You can download CDN access logs for local analysis of your traffic. You can do this by heading to your Altis environment,
in the menu for that environment, select 'Logs', then 'Access Logs'.

You can select the sample date you wish to download. Note that this will be a large amount of data, so we recommend
starting with a small sample of less than 24 hours to get a sense of the amount of data you'd be working with.

Note that we do not currently provide the ability to download other types of logs, as they are stored in a different,
internal format. In select cases, Altis Support can provide a copy of these logs, or perform queries upon the data for you.

If you need help downloading logs, or can't find what you're looking
for, [contact us](https://docs.altis-dxp.com/guides/getting-help-with-altis/) and we can assist you.

See our article on [logs and their retention periods](docs://cloud/dashboard/logs/) before requesting logs.

## Downloading Access Logs

To download Access Logs, find the environment you want in Altis Dashboard and select it.

Then, click on the "Logs" sub menu item, followed by the "Access Logs" tab.

From here you can then select a date range and further filter the data, as well as choosing either CSV or JSON format.

### Host name

To get logs for a specific host name choose the **"Host Header (x-host-header)"** field from the dropdown, and then
enter the bare domain name as the value.

### URL Path

To filter by URL path choose the **"URI Path (cs-uri-stem)"** field from the dropdown, and then enter the full path
including the leading and any trailing slashes as the value.
