[![Build Status](https://travis-ci.org/dizda/CloudBackupBundle.png?branch=master)](https://travis-ci.org/dizda/CloudBackupBundle)
CloudBackupBundle
=================

This bundle helps you to backup your databases and upload it to the cloud with only one Symfony2 command.

You can :
* Dump one database
* Dump all databases
* Different types of databases can be dumped each time
* Upload to several Cloud services

Databases supported :
* MongoDB
* MySQL

Cloud services supported :
* __Dropbox__       (with the help of [DropboxUploader by hakre](https://github.com/hakre/DropboxUploader))
* __CloudApp__      (thanks to [CloudAPP-API-PHP-wrapper](https://github.com/matthiasplappert/CloudApp-API-PHP-wrapper))
* __Amazon S3__     (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* Google Drive      (soon..)

But also :
* __Local__         (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __FTP__           (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __sFTP__          (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __GridFS__        (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __MogileFS__      (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))

are supported :-)



Installation (>=Symfony 2.1)
------------

### Composer

Download CloudBackupBundle and its dependencies to the vendor directory. You can use Composer for the automated process:

```bash
$ php composer.phar require dizda/cloud-backup-bundle dev-master
```

Composer will install the bundle to `vendor/dizda` directory.

### Adding bundle to your application kernel

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
        new Dizda\CloudBackupBundle\DizdaCloudBackupBundle(),
        // ...
    );
}
```

Configuration
-------------

Here is the default configuration for the bundle:

```yml
dizda_cloud_backup:
    cloud_storages:
        # Dropbox account credentials (use parameters in config.yml and store real values in prameters.yml)
        dropbox:
            user:     ~  # Required
            password: ~  # Required
            remote_path: ~ # Not required, default "/", but you can use path like "/Accounts/backups/"
        # CloudApp account. Can be optional, like dropbox.
        cloudapp:
            user:        ~ # Required
            password:    ~ # Required
        # or you can use Gaufrette as well (optional)
        gaufrette:
            service_name: ~  # Gaufrette filesystem service name

    databases:
        mongodb:
            all_databases: false # Only required when no database is set
            database:     ~ # Required if all_databases is false
            db_user:     ~ # Not required, leave empty if no auth is required
            db_password: ~ # Not required

        mysql:
            all_databases: false # Only required when no database is set
            database: ~          # Required if all_databases is false
            db_host: localhost   # This, and following is not required and if not specified, the bundle will take ORM configuration in parameters.yml
            db_port: ~           # Default 3306
            db_user: ~
            db_password: ~
```

It is recommended to keep real values for logins and passwords in your parameters.yml file, e.g.:

```yml
# app/config/config.yml
dizda_cloud_backup:
    cloud_storages:
        dropbox:
            user:        %dizda_cloud_dropbox_user%
            password:    %dizda_cloud_dropbox_password%
            remote_path: %dizda_cloud_dropbox_remote_path%


    databases:
        mongodb:
            all_databases: false
            database: %dizda_cloud_mongodb_user%
            db_user:  %dizda_cloud_mongodb_user%
            db_pass:  %dizda_cloud_mongodb_password%

        mysql:
            # When no parameters is specified under mysql, the bundle taking those from parameters.yml
```

```yml
# app/config/parameters.yml
	# ...
    database_driver: pdo_mysql
    database_host: localhost
    database_port: null
    database_name: myDatabase
    database_user: myLogin
    database_password: myDatabasePassword
    # ...
    dizda_cloud_dropbox_user:     myDropboxUser
    dizda_cloud_dropbox_password: MyDropboxPassword
    dizda_cloud_mongodb_user:     mongodbUser
    dizda_cloud_mongodb_password: mongodbPass
	# ...
```


Usage
-----

The bundle adds one command to symfony console: ``app/console dizda:backup:start`` which you execute periodically as a cron job.
For example the following cron command dumps your database every days at 6am on a server :
```
# m h  dom mon dow   command
0 6 * * * php /opt/www/symfony-project/app/console dizda:backup:start
```

Info : To edit crontab for the user www-data (to prevent permissions error) :
```bash
$ crontab -u www-data -e
```

or simply

```bash
$ php app/console dizda:backup:start
```

![](https://github.com/dizda/CloudBackupBundle/raw/master/Resources/doc/dizda-Cloud-Backup-Bundle-symfony2.png)

Capifony integration
--------------------

If you are using capifony for deployment you can grab the sample task for easier backups.

Add the following task in your deploy.rb file
```ruby
namespace :symfony do
    namespace :dizda do
        namespace :backup do
            desc "Upload a backup of your database to cloud service's"
            task :start do
                run "#{try_sudo} sh -c 'cd #{current_release} && #{php_bin} #{symfony_console} dizda:backup:start #{console_options}'"
            end
        end
    end
end
```

This adds symfony:dizda:backup:start command to capifony. To launch it automatically on deploy you might use:

```ruby
# 1) Launches backup right before deploy
before "deploy", "symfony:dizda:backup:start"

# 2) Launches backup after deploy
after "deploy", "symfony:dizda:backup:start"
```

End
---
This bundle was inspired from [KachkaevDropboxBackupBundle](https://github.com/kachkaev/KachkaevDropboxBackupBundle).

It is Symfony2.1, 2.2 and 2.3 compatible, I'll make some tests as soon as time permits.

Enjoy, PR are welcome !
