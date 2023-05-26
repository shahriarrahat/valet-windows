<p align="center"><img src="./laravel_valet_windows_3_logo.svg" width="50%"></p>

<p align="center">
<a href="https://github.com/cretueusebiu/valet-windows/actions?query=workflow%3Atests"><img src="https://github.com/cretueusebiu/valet-windows/workflows/Tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/cretueusebiu/valet-windows"><img src="https://poser.pugx.org/cretueusebiu/valet-windows/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/cretueusebiu/valet-windows"><img src="https://poser.pugx.org/cretueusebiu/valet-windows/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/cretueusebiu/valet-windows"><img src="https://poser.pugx.org/cretueusebiu/valet-windows/license.svg" alt="License"></a>
</p>

<p align="center">Windows port of the popular development environment <a href="https://github.com/laravel/valet">Laravel Valet</a>.</p>

## Introduction

Valet is a Laravel development environment for Windows. No Vagrant, no `/etc/hosts` file. You can even share your sites publicly using local tunnels. _Yeah, we like it too._

Laravel Valet configures your Windows to always run Nginx in the background when your machine starts. Then, using [Acrylic DNS](http://mayakron.altervista.org/wikibase/show.php?id=AcrylicHome), Valet proxies all requests on the `*.test` domain to point to sites installed on your local machine.

## Documentation

Before installation, make sure that no other programs such as Apache or Nginx are binding to your local machine's port 80. <br> Also make sure to open your preferred terminal (Windows Terminal, CMD, Git Bash, PowerShell, etc.) as Administrator. You can use VS Code integrated terminal, but if VS Code isn't opened as Administrator, then a bunch of administrator pop ups will appear in order to give access to Valet.

- If you don't have PHP installed, open PowerShell (3.0+) as Administrator and run:

```powershell
# PHP 8.1
Set-ExecutionPolicy RemoteSigned -Scope Process; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri "https://github.com/ycodetech/valet-windows/raw/master/bin/php.ps1" -OutFile $env:temp\php.ps1; .$env:temp\php.ps1 "8.1"

# PHP 8.0
Set-ExecutionPolicy RemoteSigned -Scope Process; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri "https://github.com/ycodetech/valet-windows/raw/master/bin/php.ps1" -OutFile $env:temp\php.ps1; .$env:temp\php.ps1 "8.0"

# PHP 7.4
Set-ExecutionPolicy RemoteSigned -Scope Process; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri "https://github.com/ycodetech/valet-windows/raw/master/bin/php.ps1" -OutFile $env:temp\php.ps1; .$env:temp\php.ps1 "7.4"
```

> This script will download and install PHP for you and add it to your environment path variable. PowerShell is only required for this step.

- If you don't have Composer installed, make sure to [install](https://getcomposer.org/Composer-Setup.exe) it.

- Install Valet with Composer via `composer global require ycodetech/valet-windows`.

- Run the `valet install` command. This will configure and install Valet and register Valet's daemon to launch when your system starts. Once installed, run `valet start` command to start the daemon.

- If you're installing on Windows 10/11, you may need to [manually configure](https://mayakron.altervista.org/support/acrylic/Windows10Configuration.htm) Windows to use the Acrylic DNS proxy.

Valet will automatically start its daemon each time your machine boots. There is no need to run `valet start` or `valet install` ever again once the initial Valet installation is complete.

## Commands

### PHP Services

##### php:add

```
php:add    [path]   Add PHP by specifying a path
```

```console
$ valet php:add "C:\php\7.4"
```

###### **Note:** When adding PHP, the full version number (eg. 7.4.33) will be extracted and an alias (eg. 7.4) will be generated. Either of these can be used in other commands.

###### Furthermore, the details of the versions will be written to the config in a natural decending order that adheres to decimals. This means that when two minor versions (like 8.1.8 and 8.1.18) of an alias (8.1) are added, and the default PHP is then set to use the alias, then Valet will use the most recent version of the alias, which in this case would be 8.1.18.

##### php:remove

```
php:remove [path]   Remove PHP by specifying a path
```

```console
$ valet php:remove "C:\php\7.4"
```

##### php:list

```
php:list            List all PHP versions and services
```

```console
$ valet php:list
Listing PHP services...
+---------+---------------+------------+------+-------------+---------+
| Version | Version Alias | Path       | Port | xDebug Port | Default |
+---------+---------------+------------+------+-------------+---------+
| 8.1.8   | 8.1           | C:\php\8.1 | 9006 | 9106        | X       |
| 7.4.33  | 7.4           | C:\php\7.4 | 9004 | 9104        |         |
+---------+---------------+------------+------+-------------+---------+
```

##### php:which

```
php:which  [site]   To determine which PHP version the current working directory or a specified site is using
```

```console
$ valet php:which
The current working directory site1 is using PHP 7.4.33 (isolated)

$ valet php:which site2
The specified site site2 is using PHP 8.1.8 (default)
```

##### php:install

```
php:install          Reinstall all PHP services from [valet php:list]
```

```console
$ valet php:install
```

##### php:uninstall

```
php:uninstall        Uninstall all PHP services from [valet php:list]
```

```console
$ valet php:uninstall
```

### Using PHP versions

##### use

```
use       [phpVersion]  Set or change the default PHP version used by Valet. Either specify the full version or the alias
```

```console
$ valet use 8.1
Setting the default PHP version to [8.1].
Valet is now using 8.1.18.

$ valet use 8.1.8
Setting the default PHP version to [8.1.8].
Valet is now using 8.1.8.
```

##### isolate

```
isolate   [phpVersion]  Isolates the current working directory to a specific PHP version
          [--site=]     Optionally specify the site instead of the current working directory
```

###### Note: You can isolate 1 or more sites at a time. Just pass the `--site` option for each of the sites you wish to isolate to the same PHP version.

```console
$ valet isolate 7.4
Isolating the current working directory...
The site [my_site] is now using 7.4.

$ valet isolate 7.4 --site=another_site
The site [another_site] is now using 7.4.

$ valet isolate 7.4 --site=site1 --site=site2 --site=site3
The site [site1] is now using 7.4.
The site [site2] is now using 7.4.
The site [site3] is now using 7.4.

```

##### unisolate

```
unisolate [--site=]     Removes [unisolates] an isolated site
          [--all]       Optionally removes all isolated sites
```

```console
$ valet unisolate
Unisolating the current working directory...
The site [my_site] is now using the default PHP version.

$ valet unisolate --site=my_site
The site [my_site] is now using the default PHP version.

$ valet unisolate --all
The site [my_site] is now using the default PHP version.
The site [site1] is now using the default PHP version.
```

##### isolated

```
isolated                List isolated sites
```

```console
$ valet isolated
+----------+--------+
| Site     | PHP    |
+----------+--------+
| site1    | 7.4.33 |
| my_site  | 7.4.33 |
+----------+--------+
```

### Parked and Linked

##### link

```
link   [name]        Register the current working directory as a symbolic link with a different name
       [--secure]    Optionally secure the site
       [--isolate=]  Optionally isolate the site to a specified PHP version
```

```console
$ valet link my_site_renamed
A [my_site_renamed] symbolic link has been created in [C:/Users/Stuart/.config/valet/Sites/my_site_renamed].

$ valet link cool_site --secure
A [cool_site] symbolic link has been created in [C:/Users/Stuart/.config/valet/Sites/cool_site].
The [cool_site.test] site has been secured with a fresh TLS certificate.

$ valet link cool_Site --isolate=7.4
A [cool_site] symbolic link has been created in [C:/Users/Stuart/.config/valet/Sites/cool_site].
The site [cool_site.test] is now using 7.4.

$ valet link cool_Site --secure --isolate=7.4
A [cool_site] symbolic link has been created in [C:/Users/Stuart/.config/valet/Sites/cool_site].
The [cool_site.test] site has been secured with a fresh TLS certificate.
The site [cool_site.test] is now using 7.4.
```

##### unlink

```
unlink [name]        Unlink a site
```

```console
$ valet unlink cool_site
Unsecuring cool_site...
The [cool_site] symbolic link has been removed.
```

##### links

```
links                Display all registered symbolic links
```

```console
$ valet links
+-----------------+-----+------------------+-----------------------------+---------------------------------------+
| Site            | SSL | PHP              | URL                         | Path                                  |
+-----------------+-----+------------------+-----------------------------+---------------------------------------+
| my_site_renamed |     | 8.1.18 (default) | http://my_site_renamed.code | D:\_Sites\a_completely_different_name |
| cool_site       | X   | 7.4.33 (isolated)| http://cool_site.code       | D:\_Sites\cool and awesome site       |
+-----------------+-----+------------------+-----------------------------+---------------------------------------+
```

##### parked

```
parked               Display all current sites within parked paths
```

###### Note: If there's a parked site that is also a symbolic linked site, then it will also output the linked site name (aka alias) and it's URL (aka alias URL).

```console
$ valet parked
+-----------------------------------------------+
|      Site: site1                              |
|     Alias:                                    |
|       SSL:                                    |
|       PHP: 7.4.33 (isolated)                  |
|       URL: http://site1.test                  |
| Alias URL:                                    |
|      Path: D:\_Sites\site1                    |
|-----------------------------------------------|
|      Site: another site                       |
|     Alias: another_site_renamed               |
|       SSL:                                    |
|       PHP: 8.1.18 (default)                   |
|       URL: http://another site.test           |
| Alias URL: http://another_site_renamed.test   |
|      Path: D:\_Sites\another site             |
+-----------------------------------------------+
```

### Other commands

##### services

```
services    List the installed Valet services
```

```console
$ valet services
Checking the Valet services...
+-------------------+--------------------------------+---------+
| Service           | Windows Name                   | Status  |
+-------------------+--------------------------------+---------+
| acrylic           | AcrylicDNSProxySvc             | running |
| nginx             | valet_nginx                    | running |
| php 8.1.8         | valet_php8.1.8cgi-9006         | running |
| php 7.4.33        | valet_php7.4.33cgi-9004        | running |
| php-xdebug 8.1.8  | valet_php8.1.8cgi_xdebug-9106  | running |
| php-xdebug 7.4.33 | valet_php7.4.33cgi_xdebug-9104 | running |
+-------------------+--------------------------------+---------+
```

### Commands not supported

`valet loopback`

`valet trust`

`valet status` - In favour of the `valet services` command

`valet php` (proxying commands to PHP CLI)

`valet composer` (proxying commands to Composer CLI)

`valet which-php` - In favour of the `valet php:which` command

For other commands that have not changed, please refer to the official documentation on the [Laravel website](https://laravel.com/docs/8.x/valet#serving-sites).

## Known Issues

- WSL2 distros fail because of Acrylic DNS Proxy ([microsoft/wsl#4929](https://github.com/microsoft/WSL/issues/4929)). Use `valet stop`, start WSL2 then `valet start`.
- The PHP-CGI process uses port 9001. If it's already used change it in `~/.config/valet/config.json` and run `valet install` again.
- When sharing sites the url will not be copied to the clipboard.
- ~~You must run the `valet` commands from the drive where Valet is installed, except for park and link. See [#12](https://github.com/cretueusebiu/valet-windows/issues/12#issuecomment-283111834).~~ All commands seem to work fine on all drives.
- If your machine is not connected to the internet you'll have to manually add the domains in your `hosts` file or you can install the [Microsoft Loopback Adapter](https://docs.microsoft.com/en-us/troubleshoot/windows-server/networking/install-microsoft-loopback-adapter) as this simulates an active local network interface that Valet can bind too.
- When trying to run valet on PHP 7.4 and you get this error:

  > Composer detected issues in your platform:
  >
  > Your Composer dependencies require a PHP version ">= 8.1.0". You are running 7.4.33.
  >
  > PHP Fatal error: Composer detected issues in your platform: Your Composer dependencies require a PHP version ">= 8.1.0". You are running 7.4.33. in C:\Users\YourName\AppData\Roaming\Composer\vendor\composer\platform_check.php on line 24

  It means that a dependency of Valet's dependencies requires 8.1. You can rectify this error by running `composer global update` while on 7.4, and composer will downgrade any global dependencies to versions that will work on 7.4. See this [Stack Overflow answer](https://stackoverflow.com/a/75080139/2358222).

  NOTE #1: This will of course downgrade all global packages. Depending on the packages, it may break some things. If you just want to downgrade valet dependencies, then you can specify the valet namespace. `composer global update ycodetech/valet-windows`.

  NOTE #2: It's recommended to use PHP 8.1 anyway, downgrading will mean some things may break or cause visual glitches in the terminal output. So downgrade at your own risk.

## Xdebug

To enable a debugging session you can use [Xdebug helper](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc) or set a cookie with the name `XDEBUG_SESSION`.

## Testing

Run the unit tests with:

```bash
composer test-unit
```

Before running the integration tests for the first time, you must build the Docker container with:

```bash
composer build-docker
```

Next, you can run the integration tests with:

```bash
composer test-integration
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

Laravel Valet is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
