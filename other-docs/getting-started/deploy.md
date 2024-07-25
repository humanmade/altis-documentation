---
order: 50
---

# Deploying to Altis Cloud

Once you've got your project working locally, you'll want to deploy it to a live, web-accessible site.

As part of your Altis subscription, you'll have up to three cloud environments available to you:

- **Development** (where available): A cloud environment to test code in a live cloud environment.
- **Staging**: Staging is the place for QA and testing to be performed before your site goes live.
- **Production**: Your production environment is where your live site is served by.

Cloud environments are considered either Production level or Non-Production level. Non-Production level environments are provisioned
with less intensive infrastructure, as they don't need to serve live traffic, and only views on your Production environment count
towards billing.

(Please do not run load testing against Non-Production environments, as it is not indicative of live performance.)

## Creating your repository

Altis projects are deployed from a GitHub repository that you control. You'll need to create this repository, and provide Altis
Cloud with access to deploy from it.

Altis requires the use of GitHub as well as branches to deploy to cloud environments. Each environment will match up to a given
branch, and you need to use a pull request-based workflow to make changes. Code will be automatically reviewed for security and
performance.

Once you’ve created your repository, you’ll need to give Altis access. Add
the [@humanmade-cloud user](https://github.com/humanmade-cloud) to your repository with admin access (we need this in order to
manage webhooks and other deployment settings).

We recommend setting up this repository and access ahead of your contract beginning, as this is required to allow us to set up your
first cloud environment.

### Branches

Each environment is linked to a specific branch within your GitHub repository.

Your GitHub repository is controlled by you, and you can set up your branching strategy however you would like.

A typical setup we see is:

- Development deploys from a `development` branch
- Staging deploys from a `staging` branch
- Production deploys from a `main` branch
- New features are pull requests made against the `development` branch
- Deploying changes from Development to Staging is done by creating a pull request from `development` to `staging`
- Deploying changes from Staging to Production is done by creating a pull request from `staging` to `main`
- Hot fixes, if necessary, are pull requests made directly to `main`

### Altis review bot

Code being deployed to Altis Cloud environments must pass through a series of [automated checks](docs://guides/code-review/).

These checks are performed by a bot called `altis-review`, and test for known performance and security flaws.

This bot needs to be installed in your repository per [the installation instructions](docs://guides/code-review/) for any applicable
Service Level Agreements (SLA) to apply to your instance.

## Your first cloud environment

Once you have your repository, codebase, and access ready, let your Altis contact know, and they’ll kick off the process of getting
your first environment provisioned. Depending on your tier, this will either be your dedicated development environment or your
staging environment.

They'll need to know your GitHub repository and the branch you want to use for this environment.

Provisioning your environment may take up to a week.

Once your environment is provisioned, you'll now have a URL to access your environment, which will look something
like `yourname-dev.altis.cloud` - You'll also receive an email with your user account activation link for your site. This is your
user account only for your development environment, and may not apply to other environments.

You'll also receive a user account activation link for the [Altis Dashboard](docs://cloud/dashboard/).

When the Altis team sets up your environment, they'll also kick off an initial deployment of your application to get you started.

## How deploys work

Altis Cloud includes an integrated build and deployment pipeline. This occurs in two discrete steps: builds and deploys.

The build process occurs automatically when you push a commit to the branch attached to an environment. The Altis build pipeline
handles installation of dependencies (including the Altis packages), as well as running
a [custom build script](docs://cloud/build-scripts) that you specify. (A default build script was automatically created for you when
you ran `composer create-project`.)

Built assets (such as compiled JS, CSS, etc) must not be committed to your project's Git repository, and must be built using the
build script.

The deploy process occurs when you click the "Deploy" button in the Altis Dashboard. This takes the build you select and deploys it
to the application servers while minimising downtime. These are separate processes to allow you to quickly roll back to a previous
build if you need.

## Deploying a new version of your application

Making any change starts with writing and testing your code locally. Your [local environment](docs://local-server/) that you set up
earlier acts as an almost-exact copy of the cloud environments, so most testing can take place locally. (The exception is machine
learning behaviour.)

When you're ready, push it up to your repository and file a pull request following your team's development workflow. Once you've met
your team's checks and the `altis-review` bot is passing, merge the pull request.

In the Altis Dashboard, you'll see a new build start (you might need to hit Refresh to see it). You can follow along with this build
as it runs to monitor its progress, including full logs for your build tools.

Once it's complete, you'll see the Deploy button appear. When you're ready for the code to go live, hit the Deploy button. The
deploy process will take a couple of minutes as we run health checks and swap live traffic from the old code to the new. (We'll also
make sure any running [background tasks](docs://cloud/scheduled-tasks/) finish gracefully.)

Once that's done, your changes are now live!

## Going live

Site development tends to be intense during the initial site build phases. [Altis Support](docs://guides/getting-help-with-altis/)
is available should you need any help throughout the process.

When you start getting towards launch, contact Altis Support to get your production environment spun up. We recommend following
our [launch guide](docs://guides/launching-a-site-on-altis/), which runs through considerations including content migration and DNS
switchover.

We’re here to support a successful launch day for you. Contact us at least a month in advance of your launch day with the details of
your launch, and we can provide services including additional on-call staff and pre-warming of your servers.

*Available launch services depend on your Altis subscription. Some services are only available during business hours. Contact your
account manager for further details.*

## What's next?

If you've reached here, congratulations, you know how to successfully build an Altis project from start through launch! We wish you
the best with your launches, and we're here to help if you need us.

For modern sites built with tools like Webpack, you'll probably want to check out
our [build scripts documentation](docs://cloud/build-scripts/) for more info about how to integrate these tools into your build
process. Take it even further with [our guide to headless frontends](https://docs.altis-dxp.com/guides/headless/react-app/).

When you're ready to launch a second site on your application,
our [guide to multisite](https://docs.altis-dxp.com/guides/multiple-sites/) outlines how multiple sites work with Altis.

Still want more? Check [our blog](https://www.altis-dxp.com/blog/) regularly for everything new in the world of Altis.
