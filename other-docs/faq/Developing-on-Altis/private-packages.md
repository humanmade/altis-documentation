#  How do I use privately hosted composer packages 

To install private packages, our recommendation is to use Composer's `auth.json` file to store credentials and commit this file to the repository.
 
To create the auth.json file, you can run the following in your project directory:
 
```
composer config --auth http-basic.my.yoast.com token {token}
```
 
You should see a `auth.json` file in the project's directory - you can commit this, and it should authenticate correctly in the build. 

`auth.json` is prevented from being accessed directly via the servers, so this won't be exposed publicly.
