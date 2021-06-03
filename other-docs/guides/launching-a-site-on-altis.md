# Launching a Site on Altis

The process for launching a site on Altis varies depending on the type of project. Nonetheless, there are common steps no matter the context and this guide will walk you through what needs to be done in preparation for your site launch.

## Planning

We recommend creating a launch issue in the project repository. This issue should include:

* Launch date
* Feature freeze date
* Code freeze date
* Content freeze date (if migrating an existing site)
* Team members needed for the deploy
* Domains hosted on Altis
* Domain(s) used for email
* FROM address for each site in the network
* Deploy branch name per environment
* A launch checklist containing pre-launch, launch day and post-launch tasks (examples can be found below)
* Any additional notes or complexities for the project launch

An [example checklist](#Example-launch-checklist) for your GitHub issue can be found below. This issue is primarily for _internal project use_ and compiling all the necessary information.

Once you've collected all the information into your GitHub issue, this information can be copied into [a support ticket in the Altis Dashboard](https://docs.altis-dxp.com/guides/getting-help-with-altis/) to alert the Altis Support Team. Set the ticket Type to a "Task", the Priority to "Normal" and the Subject can be "Site Launch Preparation".

### Example Launch Checklist

```markdown
# Goal

<!-- A brief overview of the launch objective. Is it a migration? Is it a new site? -->

## Pre-launch Checklist
<!-- To be filled out by the project team/lead. Not needed for Altis Support ticket. -->
- [ ] Are we deploying the correct branch?
- [ ] Has AWS SES been setup?
- [ ] Is a content freeze active?
- [ ] Is the site ready for the DNS switch?
- [ ] Are any redirections needed?

## Domains Hosted on Altis
<!-- List of domains that will be on the stack. Dev and staging environments do not need to be included in the list. -->

- domain.com
- domain2.com
- etc.

**What domain(s) will email need to be sent from?**
<!-- Enter as a list -->
- noreply@domain.com
- etc.

**For each site in the network, what will be the FROM email?
<!-- Enter as a list -->
- **Network Admin:** noreply@domain.com
- **domain.com:** some-email@domain.com
- etc.

## Environment
* **Database import link:**
* **Uploads import link:**
* **Who is providing the SSL certificate(s) (Altis or client)?:**
* **Who controls the DNS for the client (Altis or client)?:**
* **Projected traffic:**
* **Any other specifications or challenges?**
_Refer to the Altis [Limitations](https://docs.altis-dxp.com/cloud/limitations/) and [Page Caching](https://docs.altis-dxp.com/cloud/page-caching/) guides for specifics around what requests can be supported._


```

## Preparing Altis Cloud Environments
After you have submitted your support request in the Altis Dashboard, the Altis Support team will work on setting up your environments and be in touch with your team. When your new cloud environments are set up, you will be contacted and can begin deploying your code to those new environments.

Once you have environments set up, we recommend limiting access to your sites. By default, login will be required on your development and staging environments. Before your site launches, we recommend requiring some form of login to view your production environment as well.

Logins can be enforced by [requiring user logins](https://docs.altis-dxp.com/security/require-login/), forcing [HTTP basic authentication](https://docs.altis-dxp.com/security/php-basic-auth/), or both.

In your Altis configuration file (`composer.json`), add the following to require login. You may additionally enable HTTP basic authentication if that is desirable for your environment. We recommend having at least one of these security options enabled for all non-production and pre-launch sites. You can override the require login setting to not require login on local environments. Basic authentication is disabled by default on local environments. Refer to the documentation pages for more information about Require Login and Basic Authentication.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"require-login": true
				}
			},
			"environments": {
				"local": {
					"modules": {
						"security": {
							"require-login": false
						}
					}
				}
			}
		}
	}
}
```

## Preparation for Launch

After the site launch is completed, there may be some items left to clean up or check.

- Remove the `require-login` and/or the `php-basic-auth` requirements on the production site. They should remain active on any staging or development environments.
- Check the Search Console, Analytics or other similar services or logs for any errors after the deploy is complete.
- If the development or staging environments need to be synced, you can import that data in the Altis Dashboard.

## Site Launch

### Content Migration

Before the domain(s) are pointed to the Altis servers, a content migration should be performed to ensure the right content is loaded on the site when the domain is made active. We recommend scheduling a migration sometime during the week when there is more support coverage rather than on weekends. Exceptions may be made on an ad-hoc basis. We ask for a two week lead time on support around migrations so we can ensure the team is on hand and prepared to support the process.

When the most updated data has been imported into the database, a search-and-replace action should be performed so all the database entries that contain the URL of the site have been updated to the _correct_ URL. This can be done in the Altis Dashboard by either opening an SSH connection to the Sandbox Server or running a command on the WP CLI tab.

The following command would change all entries in the database containing `domain-production.altis.cloud` to `domain.com`. We recommend testing the change first using the `--dry-run` flag to make sure no unexpected tables are affected.

```bash
wp search-replace domain-production.altis.cloud domain.com --all-tables --network --url=domain-production.altis.cloud
```

This step will need to be repeated for any subdomains you have. Be sure to flush the cache when you are done, otherwise the old URLs will still be saved in the object cache.

```bash
wp cache flush
```

### DNS Switch

After the database is updated and the site is ready to go, contact Altis Support to let them know that you are ready for the DNS switch to your new site.

During this period, your temporary production site (e.g. `domain-production.altis.cloud`) will be inaccessible. While the DNS updates are going through, you will want to remove the `require-login` setting in the Altis configuration file unless you have already done so.