Passe-Plat Bundle
=============

The Passe-Plat Bundle is an order management system for Symfony 3 based on status oriented management 
rules and coupled with Google Drive.

Features :
  * creation and edition of statuses
  * real-time status changes update your orders
  * automatic import and creation of orders from/to Google Drive
  * self-creating and organizing orders on Google Drive 
  
### Status oriented management

#### Principle
The Passe-Plat bundle is built on the principle that an order's status (i.e. on hold, ready etc...)
depends on the statuses of the products it's made of. This is achieved through different weights given to 
statuses, the most important being the heaviest.

#### Use
All you have to do is create some statuses and order them on the statuses main page. There you have 
a table which rows you can drag'n'drop in the order you want.
Know that through this actions all concerned orders will be updated on your platform as well as moved to the right
folders on Google Drive.

### Google Drive 
#### Principle
Google Drive sheets are used to create orders and as a way to keep track of it.

#### Use
Create orders directly on the drive or directly from your platform. There is a button to scan new orders 
from the drive in the bundle. Whenever you update your statuses or your products, corresponding sheets
are moved to the right folders (if the folder doesn't exist it is created).

### Configuration
#### Bundle

#####Step 1 : download the bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:
```console
$ composer require <package-name> "~1"
```
This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

##### Step 2 : enable the bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Jasdero\PassePlatBundle\JasderoPassePlatBundle(),
        );
        // ...
    }
    // ...
}
```

##### Step 3 : configure the bundle

Open the `config.yml` file of your project and put the following lines with your values :
```yml
# app/config/config.yml

parameters:
##
    jasdero_passe_plat.folder_to_scan: yourValue
    jasdero_passe_plat.new_orders_folder: yourValue
    jasdero_passe_plat.errors_folder: yourValue
    

jasdero_passe_plat:
    drive_connection:
        path_to_refresh_token: "%path_to_refresh_token%"
        auth_config: "%auth_config%"
    drive_folder_as_status:
        root_folder: "%root_folder%"
```

Update your `parameters.yml` accordingly :
```yml
# app/config/parameters.yml

...
    path_to_refresh_token: yourPath
    auth_config: yourPath
    root_folder: yourValue
```
#### Google Drive
[Reference](https://developers.google.com/api-client-library/php/auth/web-app)

First step is to create a Google Account if you don't have one yet.
Then you [activate the Drive API](https://console.developers.google.com/apis/library) for your application.
After that you need to [create credentials](https://console.developers.google.com/projectselector/apis/credentials)
and configure the redirect URI. By defaults it is the "/auth/checked" route in the bundle (don't forget 
to put your domain ).

