this is shell for magento. 
install this module from composer:
edit you `composer.json` and adding this module to require section 
[code]
    "require": {
      ......
      "lnroma/adminuser":"dev-master"
    }
[/code]
and adding to section vcs repository
[code]
    "repositories": [
         ..............
         {
           "type": "vcs", 
           "url": "https://github.com/lnroma/adminuser"
         },  
         ..............
    ]
[/code]

and type `php composer.phar update` or `composer update` if you `composer` install to system.

install this module manualy:
`cd /your/root/magento/shell/`
`wget https://raw.githubusercontent.com/lnroma/adminuser/master/shell/adminuser.php`

usage this shell script:
`cd /your/root/magento/shell/`
`php adminuser.php list` - list all admin users in system
`php adminuser.php reset` - run script for reset user...
