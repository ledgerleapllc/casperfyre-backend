<p>
	<img src="https://api.casperfyre.com/logo.png" width="400">
</p>

# CasperFYRE API
Dispensory API interface for Casper mainnet

## Dependencies

The system will check for the existence of the following softwares that are required to run a full API server.

```
 1. Ubuntu 18+
 2. Apache2
 3. PHP 7+ {bcmath,bz2,intl,gd,mbstring,mysql,zip,common,curl,xml,gmp}
 4. Mysql 5+
 5. cURL
 6. Composer 2+
```

## Setup

### Database

You will first need to create the database itself manually.

e.g. 

```
mysql> create database casperfyre-db
mysql> exit
````

Then, you can simply run **setup.sh** to run interactive setup script.

```
./setup.sh
```

The first time pinging the server will build the tables inside the given named database and create an admin account.

Find admin credentials in apache error log. Example output:

````
[CasperFYRE 2022-04-05T12:28:46-04:00] - Created admin
[CasperFYRE 2022-04-05T12:28:46-04:00] - Email: "email"
[CasperFYRE 2022-04-05T12:28:46-04:00] - Password: "password"
````

### Finish Environment

The setup script does not contain interactive input pertaining to the webmaster email. You must setup an email server and provide an email username, password, port, host, etc; Or a relay API key like Sendgrid, in order to make the webmaster email scheduler function. This is required for receiving 2fa codes, getting registration confirmation codes, password reset links, and other similar functions.

```
cat .env
```

Adjust your .env configuration variables to fit your instance. 