Passe-Plat Bundle
=============

The Passe-Plat Bundle is an order management system for Symfony 3. It is based on status oriented management 
rules and is coupled with Google Drive.

Features :
  * creation and edition of statuses
  * real-time status changes update your orders
  * automatic import and creation of orders from Google Drive
  * self-creating and organizing orders on Google Drive 
  
### Status oriented management

#### Principle
The Passe-Plat bundle is built on the principle that an order's status (i.e. on hold, ready etc...)
depends on the statuses of the products its made of. This is achieved through different weights given to 
statuses, the most important being the heaviest.

#### Use
All you have to do is create some statuses and order them on the statuses main page. There you have 
a table which rows you can drag'n'drop in the order you want.
Know that through this actions all concerned orders will be updated on your platform as well as moved to the right
folders on Google Drive.

### Google Drive 
#### Principle
Google Drive sheets are used to create orders and as a way to keep track of your orders.

#### Use
Create orders directly on the drive or directly from your platform. There is a button to scan new orders 
from the drive in the bundle. Whenever you update your statuses or your products, corresponding sheets
are moved to the right folders (if the folder doesn't exist it is created).

### Configuration
#### Google Drive
[Reference](https://developers.google.com/api-client-library/php/auth/web-app)

First step is to create a Google Account if you dont have one yet.
Then you [activate the Drive API](https://console.developers.google.com/apis/library) for your application.
After the you need to [create credentials](https://console.developers.google.com/projectselector/apis/credentials) and
and configure the redirect URI. By defaults it is the "/auth/checked" route in the bundle (don't forget 
to put your domain ).

After getting your client secret, you need to put the path to it in both methods of the DriveConnection 
class (in $client->setAuthConfig).
You also need to determine a path to refresh token in both methods (something like $pathTorefreshToken = 
'vendor\refresh_token.json).
