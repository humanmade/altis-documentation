# Configuration

Configuration of HM Platform is done the `composer.json` file in the root of your project. To configure specifics modules, feature and settings for HM Platform, add an `extra.platform` section to your `composer.json`.

```
{
	"name": "example/my-site",
	"require": {
		"humanmade/platform": "*",
	},
	...
	"extra": {
		"platform": {

		}
	}
}

```

When documentation refers to the configuration file, its referring to this section of the `composer.json` file.

By convention, most module's settings reside in the path `platform.modules.$module.$setting`. For example, to require all users be logged in to view the website, you'd set the `modules.security.require-login` setting to `true`.

```json
{
	"name": "example/my-site",
	"require": {
		"humanmade/platform": "*",
	},
	...
	"extra": {
		"platform": {
			"modules": {
				"security": {
					"require-login": true
				}
			}
		}
	}
}
```

## Environment Specific Configuration

It's not unusual to want different configuration options for difference environments. For example, you may want to have the `require-login` feature enabled for all environments, except the `local` environment. Environment specific configuration is provided in the form `platform.environments.$environment`.

```json
{
	"name": "example/my-site",
	"require": {
		"humanmade/platform": "*",
	},
	...
	"extra": {
		"platform": {
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

Environment configuration is merged with the global `platform` configuration, with the matching environment options overriding anything specific in the global configuration. The environment type is matched against the value return by the function `HM\Platform\get_environment_type()`. The environment type will typically be any of `local`, `development`, `staging` or `production`.

## Custom Configuration

When developing custom code and features, it's recommended to make use of the configuration file and APIs so all configuration can be centrally located and machine readable. By convention it's also recommended to use a namespace in the `extra.platform` object to ensure your custom configuration settings don't collide with HM Platform current or future settings.

Suppose you have built a feature that published stories to Twitter on publish. You can make use of the configuration file to provide the Twitter OAuth 2 credentials that should be used. It's quite likely you'd want to make use of environment specific overrides so the `staging` site publishes to a different Twitter account.

```json
{
	"name": "example/my-site",
	"require": {
		"humanmade/platform": "*",
	},
	...
	"extra": {
		"platform": {
			"environments": {
				"staging": {
					"my-project": {
						"twitter-oauth2-token": "xxxxxxxxx"
					}
				},
				"production": {
					"my-project": {
						"twitter-oauth2-token": "yyyyyyyyy"
					}
				}
			}
		}
	}
}
```

To get the value of your custom configuration at runtime, use the `HM\Platform\get_config()` function. This function will automatically handle the environment specific overrides.

```php
$token = HM\Platform\get_config()['my-project']['twitter-oauth2-token'];

new TwitterClient( $token );
```
