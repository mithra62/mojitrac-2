# Installing Base MojiTrac 2 #

Below are the instructions to get MojiTrac 2 installed from the Github repository. 

## Requirements ##

MojiTrac 2 requires the below server setup:

1. Apache 2.X
	1. Requires its own vhost (will not work as subdirectory of existing vhost)
2. PHP >= 5.3.10
	1. Intl Extension is required
	2. MySQL PDO Extension is required
	3. Composer
3. MySQL >= 5.2

## Installation Instructions ##

For the most part, MojiTrac 2 is just like any other installed software:

1. Setup vhost
2. Create MySQL database, username, and password
	1. Import `dev_moji.sql` to your database
3. Configure code to connect to the database
	1. Rename `config/local.php.dist` to `config/local.php` and modify settings within
4. Run `composer update`
5. Ensure the permissions are writable on the `data\*` directory
6. Crack a beer. 
 
## Credentials ##

To log into the newly installed MojiTrac, use the below credentials:

### Administrator (No projects) ###
Email: test@mithra62.com<br />
Pass: test1234

### Administrator (LOTS of projects) ###
Email: eric@ericlamb.net<br />
Pass: dimens35