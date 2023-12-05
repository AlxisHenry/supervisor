# Supervisor :mag_right:

This is one of my first project in PHP. This is a supervisor of the assets of a company.

## Table of contents
1. [What is it ?](#what-is-it-)
2. [Features](#features)
3. [How to use it ?](#how-to-use-it-)
4. [Technologies](#technologies)
5. [Authors](#authors)

## What is it ?

*Global view of the assets, where you can search using the filters.*

![Capture d'écran 2023-12-05 125454](https://github.com/AlxisHenry/supervisor/assets/91117127/4d5bbda8-c96a-4a8b-87ac-10074de5d25a)

*Asset page where you can find all informations and actions that you can perform on a specific asset.*

![Capture d'écran 2023-12-05 125554](https://github.com/AlxisHenry/supervisor/assets/91117127/93287805-b3a6-4af4-b08f-d746760b6d91)

## Features

- Auth system that allows only specified windows users
- Import assets from Active Directory
- Update one or all assets from Active Directory or WMIC
- View of the history of an asset (all changes)
- View installed software on an asset
- Generate a pdf report of an asset
- Manage repairs of assets
- Create, update and delete assets and users manually
- Search with a lot of filters on all interisting tables
- Export to CSV for all tables
- CRUD on all others needed tables
- Custom export of assets to CSV (using a range)
- Script page to execute some tests on a specific asset

## How to use it ?

Clone the project and go to the directory.

```bash
$ git clone https://github.com/AlxisHenry/supervisor.git
$ cd inventory-manager
```

Then you need to create the database by importing `supervisor.sql`.

```bash
$ mysql -u <user> -p
mysql> source supervisor.sql
```

Next you need to configure the database connection in `prog\start.php`.

```php
$host = "localhost";
$dblogin = "<username>";
$dbpassword = "<password>";
```

If you want to use the project for a demo, you only need to disable the auth system in `prog\start.php`.

```php
define("AUTH", false);
```

Else you will need to make some changes. Good luck :laughing:

Finally you need to start the project.

```bash
$ make run # or php -S localhost:8000
```

## Technologies

![](https://img.shields.io/badge/php-%23121011.svg?style=for-the-badge&logo=php&color=20232a)
![](https://img.shields.io/badge/mysql-%23121011.svg?style=for-the-badge&logo=mysql&color=20232a)
![](https://img.shields.io/badge/javascript-%2523121011.svg?style=for-the-badge&logo=javascript&color=20232a)

## Authors

- [@AlxisHenry](https://github.com/AlxisHenry)
