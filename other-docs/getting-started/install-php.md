---
order: 90
---

# Prerequisite: install PHP

## Install PHP on macOS

Ref: https://www.php.net/manual/en/install.macosx.php

One easy way to install PHP on macOS is using [Homebrew](https://brew.sh/). Open a Terminal and paste the following command:

```shell
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

Once Homebrew is installed, proceed to installing PHP:

```shell
brew install php
```

When installation is complete, verify PHP was installed successfully by executing

```shell
php -v
```

You should see an output similar to 

```shell
PHP 8.4.10 (cli) (built: Jul  2 2025 02:22:42) (NTS)
Copyright (c) The PHP Group
Built by Homebrew
Zend Engine v4.4.10, Copyright (c) Zend Technologies
    with Zend OPcache v8.4.10, Copyright (c), by Zend Technologies
```

([Alternative ways to install PHP on macOS](https://www.php.net/manual/en/install.macosx.packages.php) are available on PHP project
website.)

## Install PHP on Windows

The Windows install is outlined in detail in [Running Local Server on Windows](docs://local-server/windows/).

## Install PHP on Linux

Ref: https://www.php.net/manual/en/install.unix.php

Install PHP with:

**Debian / Ubuntu / Windows Subsystem for Linux**

```shell
apt install php-common php-cli
```

**Red Hat Enterprise Linux, OpenSUSE, Fedora, CentOS, Rocky Linux, Oracle Enterprise Linux**

```shell
dnf install php php-common
```

For all distributions, check success with

```shell
php -v
```

You should see an output similar to 

```shell
PHP 8.2.28 (cli) (built: Mar 13 2025 18:21:38) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.28, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.28, Copyright (c), by Zend Technologies
```

**Note:**  Different Linux distributions will have different PHP versions in their package repositories (above output is from
`debian`). If you want to install the latest PHP version, you may consider [building from source](https://www.php.net/manual/en/install.unix.source.php). 

**Note:** Your locally installed PHP version is only a prerequisite to install Altis. The actual PHP version to be used when
running your project(s) is provided as a docker container, specific to each Altis version we maintain. Refer to our
[PHP Version Guide](docs://nightly/guides/updating-php/) for up-to-date compatibility, testing and upgrading information.
