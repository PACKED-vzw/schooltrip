#SchoolTrip
Schooltrip is a tool that allows students to create their own school journey. Through an online interface the teacher can set a couple of parameters defining the skeleton of the trip. Students fill the template with information on practicalities, cultural heritage sites to visit, historical information, and so on. They learn to plan a travel from a to z, while incorporating our cultural heritage. At the end, a journal-like document is generated which can be used as itinerary guidebook.

Documentation may be found at the [AthenaPlus wiki](http://wiki.athenaplus.eu/index.php/SchoolTrip).
 
SchoolTrip was created by [PACKED vzw](http://packed.be/) as part of the [AthenaPlus project](http://www.athenaplus.eu/) funded by the European Commission.

## Installation instructions

Create a MySQL database and user (in MySQL) and update your parameters.yml (in app/config) with the database server, database name, user name and password.

Install all dependencies using the composer.json-file included in this Git repository.

Create the tables this application uses with the following command (executed in the root of your Symfony application):
```
php app/console doctrine:schema:update --force
```

Execute this command to allow [FOSjsRouting](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle/blob/master/Resources/doc/index.md) to work:
```
php app/console assets:install --symlink web
```

Create the ```img``` directory in the ```web```-directory:
```
mkdir -p web/img
```

Create the ```media``` directory in the ```web```-directory:
```
mkdir -p web/media
```

### Permissions
You need to make sure that the web server user (e.g. www-data on Ubuntu) has write rights to the application sub tree.

## User administration
Schooltrip requires at least one "teacher" account to function. You can create this account by executing the following command (executed in the root of your Symfony application):
```
php app/console fos:user:create Teacher teacher@school.edu some_password
```

To promote this user to a teacher account, use:
```
php app/console fos:user:promote Teacher ROLE_ADMIN
```

### Resetting user passwords
The interface does not provide a way to reset user passwords. This can be done using the CLI interface however:
```
php app/console fos:user:change-password Teacher some_other_password
```

For more commands relating to user administration, see the documentation of [FOSUserBundle](https://symfony.com/doc/master/bundles/FOSUserBundle/command_line_tools.html).
