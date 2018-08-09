<p align="center">
  <img src="https://raw.githubusercontent.com/antony382/roles-and-permission/master/public/images/logo.png" style="width: 15% !important;max-width: 20% !important;">
</p>

![Latest Stable Version](https://poser.pugx.org/laravel/laravel/v/stable) [![Latest Unstable Version](https://poser.pugx.org/laravel/laravel/v/unstable)](https://packagist.org/packages/laravel/laravel) [![License](https://poser.pugx.org/laravel/laravel/license)](https://packagist.org/packages/laravel/laravel)

**Onedrive Integration**

This library helps you to integrate your php Application with Onedrive

**Installation**
````
composer require "processdrive/onedrive-integration":"dev-master"
````

**Configuration**

To Integrate your app with onedrive you need follow below steps:

step 1: Create application in [Onedrive Application Portal](https://apps.dev.microsoft.com/#/appList)

step 2: Generate new password

step 3: Add redirect URL (Here auth_code will be sent to this url)

step 3: Add Delegated permission (Files.ReadWrite.All and Directory.ReadWrite.All)

step 4: Edit /vendor/onedrive/src/config/onedrive.php and add your creds. 


**How to Use ?**

use onedrive\models\onedrive_business; // for onedrive business
````
$onedrive = new onedrive_business;

$onedrive->get_code_url() // It will give redirect uri
````
After authenticate:

Onedrive will redirect(this from onedrive application configuration) to your php application


For example:
````
http://localhost/index.php  // your redirect page

http://localhost/index.php?code=** // response from onedrive

$code // from onedrive

$onedrive->get_access_token_from_code($code);
````
it will generate access_token and refresh_token
````
$onedrive->access_token // will give accesstoken

$onedrive->refresh_token // will give refreshtoken

$onedrive->get_access_token_from_refresh_token($refresh_token);
````
it will generate access_token and refresh_token
````
$onedrive->access_token // will give accesstoken

$onedrive->refresh_token // will give refreshtoken
````

**Get Items**
````
$onedrive->get_items($parent_folder_id = null); 
````
**Create Folder**
````
$onedrive->create_folder($folder_name, $parent_folder_id = null); 
````
**Delete Folder or File**
````
$onedrive->delete_item_from_drive($item_id);
````
**Create File**
````
$onedrive->create_file($file_path, $parent_id = null, $file_name = null);
````
**Download File**
````
$onedrive->download_file($file_id);
````
**Get Share link of Folder or File**
````
$onedrive->get_share_link_of_an_item($file_id);
````
