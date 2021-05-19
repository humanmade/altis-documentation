# Launching a Site on Altis

The process for launching a site on Altis varies depending on the type of project. Nonetheless, there are common steps that need to be done no matter the context and this guide will walk you through what needs to be done in preparation for your site launch.

## Planning

Create a launch issue in the project repository. This issue should include:

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

Once the GitHub issue is created, this information can then be copied into [a support ticket in the Altis Dashboard](https://docs.altis-dxp.com/guides/getting-help-with-altis/) to alert the Altis Support Team.

### Example launch checklist

```markdown
# Goal

<!-- A brief overview of the launch objective. Is it a migration? Is it a new site? -->

## Pre-launch checklist
<!-- To be filled out by the project team/lead. Not needed for Altis Support ticket. -->
- [ ] Are we deploying the correct branch?
- [ ] Has AWS SES been setup?
- [ ] Is a content freeze active?
- [ ] Is the site ready for the DNS switch?
- [ ] Are any redirections needed?

## Domains hosted on Altis
<!-- List of domains that will be on the stack -->

### Production
- domain.com
- domain2.com
- etc.

### Staging
- domain-staging.altis.cloud
- domain2-staging.altis.cloud
- etc.

### Development
<!-- If applicable -->
- domain-dev.altis.cloud
- domain2-dev.altis.cloud
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
* **GitHub repository:** https://github.com/{owner}/{repo}
* **GitHub repository deploy branches:**
  * **Production:** `main`
  * **Staging:** `staging`
  * **Development:** `dev`
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

Once you have environments set up, you should restrict access to them by [requiring login](https://docs.altis-dxp.com/security/require-login/), forcing [PHP basic authentication](https://docs.altis-dxp.com/security/php-basic-auth/), or both. In your Altis configuration file (`composer.json`), add the following to require login both and PHP authentication. You may omit PHP authentication, but we recommend having at least one of these enabled for all non-production and pre-launch sites. You can override the require login setting to not require login on local environments. PHP authentication is disabled by default on local environments. Refer to the documentation pages for more information about Require Login and PHP Basic Authentication.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"require-login": true,
					"php-basic-auth": {
						"username": "username",
						"password": "password"
					}
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

## Post-launch

After the site launch is completed, there may be some items left to clean up or check.

- Remove the `require-login` and/or the `php-basic-auth` requirements on the production site. They should remain active on any staging or development environments.
- Check the Search Console, Analytics or other similar services or logs for any errors after the deploy is complete.
- Do the development or staging environments need to be synced?
