# Launching a Site on Altis

The process for launching a site on Altis varies depending on the type of project. Nonetheless, there are common steps that need to be done no matter the context and this guide will walk you through what needs to be done in preparation for your site launch.

## Planning

Create a launch issue in the project repository. This issue should include:

* Client name
* Client business region
* Client contact
* Launch date
* Feature freeze date
* Code freeze date
* Content freeze date (if migrating an existing site)
* Team members needed for the deploy
* Domains hosted on Altis
* Domain(s) used for email
* FROM address for each site in the network
* GitHub repository
* Deploy branch name per environment
* A launch checklist containing pre-launch, launch day and post-launch tasks (examples can be found below)
* Any additional notes or complexities for the project launch

An [example checklist](#Example-launch-checklist) for your GitHub issue can be found below. This issue is primarily for _internal project use_ and compiling all the necessary information. 

Once the GitHub issue is created, this information can then be copied into [a support ticket in the Altis Dashboard](https://docs.altis-dxp.com/guides/getting-help-with-altis/) to alert the Altis Support Team.

## Example launch checklist

```markdown
# Goal

<!-- A brief overview of the launch objective. Is it a migration? Is it a new site? -->

## Pre-launch checklist
<!-- To be filled out by the project team/lead. Not needed for Altis Support ticket. -->
- [ ] Are we deploying the correct branch?
- [ ] Has AWS SES been setup?
- [ ] Is a content freeze active?
- [ ] Is the client ready for DNS switches?
- [ ] Site is ready for HTTPS?
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
- domain-dev.altis.cloud
- domain2-dev.altis.cloud
- etc.

**What domain(s) will email need to be sent from?**
<!-- Enter as a list -->
- [ ] noreply@domain.com
- [ ] etc.

**For each site in the network, what will be the FROM email?
<!-- Enter as a list -->
- [ ] **Network Admin:** noreply@domain.com
- [ ] **domain.com:** some-email@domain.com
- [ ] etc.

## Environment
* **GitHub repository:** https://github.com/humanmade/{projectname}
* **GitHub repository deploy branches:**
  * **Production:** `main`
  * **Staging:** `staging`
  * **Development:** `dev`
  * **Altis integration branch:** `altis` <!-- If applicable -->
* [ ] **Database exists in Altis Dashboard?**
  * [ ] If not, provide a link to the database to import:
* [ ] **Uploads exist in Altis Dashboard?**
  * [ ] If not, provide a link to the uploads to import:  
* **Who is providing the SSL certificate(s) (Altis or client)?:** 
* **Who controls the DNS for the client (Altis or client)?:**
* **Special requirements or considerations for ElasticSearch:**
* **Projected traffic:** 
* **Specific cookies that need to be allow-listed outside these patterns `comment_`, `hm_`, `wordpress_`, `wp-`, `wp_`, `altis_`:**
* **Any other specifications or challenges?**

## Altis Cloud
- [ ] Nameserver records have been provided to the client (if we're managing their DNS).
- [ ] AWS SES domain verification DNS records have been provided to the client or entered into Route 53.
- [ ] AWS SES Sending domains have been verified in SES.
- [ ] AWS SES Sending domains have been configured in the application code.
- [ ] ACM Certificate DNS records have been provided to the client or entered into Route 53
- [ ] ACM Certificate has been issued.
- [ ] ACM Certificate has been attached to the environment.
- [ ] Existing database, if any, has been imported into the environment.
- [ ] Existing uploads, if any, have been imported into the environment.
- [ ] Database has been search/replaced with the environment-specific domain as the replacement.

<!-- For each environment that needs to be created or migrated, include a checklist like the following: -->

### domain-dev
- [ ] Create terraform config in humanmade/infrastructure-config/terraform/apps
- [ ] Retrieve the latest AMI for the ECS EC2 Cluster, add to TF config
- [ ] Gather all the domains the stack will use to serve content from, add to TF config
- [ ] Create a valid SSL Certificate in ACM, add to TF Config
- [ ] Update GitHub repository details in TF config
- [ ] Retrieve migration parameters using script, add to TF config
- [ ] Spin up environment
- [ ] Deploy to environment
- [ ] Dump original database and import new environment
- [ ] Perform search/replace where necessary
- [ ] Confirm site is operating normally

### domain-staging

- [ ] Create terraform config in humanmade/infrastructure-config/terraform/apps
- [ ] Retrieve the latest AMI for the ECS EC2 Cluster, add to TF config
- [ ] Gather all the domains the stack will use to serve content from, add to TF config
- [ ] Create a valid SSL Certificate in ACM, add to TF Config
- [ ] Update GitHub repository details in TF config
- [ ] Retrieve migration parameters using script, add to TF config
- [ ] Add DNS Terraform config
- [ ] Spin up environment
- [ ] Deploy to environment
- [ ] Dump original database and import new environment
- [ ] Perform search/replace where necessary
- [ ] Confirm site is operating normally

### domain-production

- [ ] Create terraform config in humanmade/infrastructure-config/terraform/apps
- [ ] Retrieve the latest AMI for the ECS EC2 Cluster, add to TF config
- [ ] Gather all the domains the stack will use to serve content from, add to TF config
- [ ] Create a valid SSL Certificate in ACM, add to TF Config
- [ ] Update GitHub repository details in TF config
- [ ] Retrieve migration parameters using script, add to TF config
- [ ] Add DNS Terraform config
- [ ] Spin up environment in read-only migration mode
- [ ] Deploy to environment
- [ ] Disconnect from source environment
- [ ] Update MySQL Password Need to establish a better process around this
- [ ] Perform search/replace where necessary
- [ ] Confirm site is operating normally

### Production Data Migration
Reference : https://github.com/humanmade/terraform-app-stack/blob/master/MIGRATING.md#migration-checklist
- [ ] Communicate an edit lock with the client.
- [ ] Spin up the Terraform stack.
- [ ] Verify the stack is functioning correctly
- [ ] Point Terraform to the original CloudFront distribution
- [ ] Remove the existing distribution from Terraform's state
- [ ] Import the original distribution
- [ ] Update CloudFront domains with the values from the original distribution
- [ ] Plan the changes:
- [ ] Verify the changes looks
- [ ] Inform client edit lock has begun.
- [ ] Prepare to flip the switch
- [ ] Flip the switch:
- [ ] Monitor the RequestCount metric on the original Load Balancer
- [ ] Disable Cavalcade on the CloudFormation stack
- [ ] Verify the new RDS Cluster is in sync with the Original RDS Instance
- [ ] Disable Replication
- [ ] Enable writes on the new infrastructure
- [ ] Delete the dangling CloudFront distribution.
- [ ] Update the CloudFormation stack template to retain CloudFront distribution.
- [ ] Delete Cloudformation stack once after the client QA.

```

## Post-launch

After the site launch is completed, there may be some items left to clean up or check.

- Do you need to set the default branch back to `main` in your repository?
- Check the Search Console, Analytics or other similar services or logs for any errors after the deploy is complete.
- Do the development or staging environments need to be synced?