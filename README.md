CloudBackupBundle
=================

This bundle helps you to backup your databases and upload it to the cloud with only one Symfony2 command.

You can :
* Dump one database
* Dump all databases
* Different types of databases can be dumped each time
* Upload to several Cloud services

Databases supported
* MongoDB
* MySQL

Cloud service supported
* __Dropbox__       (with the help of [DropboxUploader by hakre](https://github.com/hakre/DropboxUploader))
* __CloudApp__      (thanks to [CloudAPP-API-PHP-wrapper](https://github.com/matthiasplappert/CloudApp-API-PHP-wrapper))
* __Amazon S3__     (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __FTP__           (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __GridFS__        (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __Local__         (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __MogileFS__      (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* __sFTP__          (through [KnpGaufretteBundle](https://github.com/KnpLabs/KnpGaufretteBundle))
* Google Drive      (soon..)




Installation (Symfony 2.1)
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
jms_di_extra:
    locations:
        all_bundles: false
        bundles: [ DizdaCloudBackupBundle ]       # Add the bundle to JMSDiExtra conf to allow DI (if all_bundles is false)
        directories: ["%kernel.root_dir%/../src"]

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


End
---
This bundle was inspired from [KachkaevDropboxBackupBundle](https://github.com/kachkaev/KachkaevDropboxBackupBundle).

It is 2.1.x compatible, I'll make some tests as soon as time permits.

Enjoy, PR are welcome !
