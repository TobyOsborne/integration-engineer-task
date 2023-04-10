# Overview

Hi! Thanks for the opportunity to complete the test project. It was an interesting few days that provided a great overview of the MailerLite API, and Laravel Framework.

Completely honest, I'd not worked with Laravel before, so, I'd love some feedback on where I did stupid things and what I should have done instead.

With that said, I purposely made a few aspects more complicated so I could play around with the various features in Laravel. (Noted that it's not the best time to do that though. 😂)

# Environment Setup

I utilised Laravel Sail, Docker and Composer to create the environment so setup should be fairly straight forward.

The brief mentioned setup with `PHP 7.4 and MySQL 5.*` as a result, I back dated the version of Laravel to v8, which was the last one that supported `PHP 7.*`.

**Note this set up guide is mostly based on OSX**

**Note I kept the .env in the repository to make it easier to set up, I know it's not smart to do.**

## Docker Desktop

First you'll need to make sure you have [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed. This will act as the virtual environment.

**Note** that if you're on Windows you'll need to ensure that Windows Subsystem for Linux 2 (WSL2) is installed and enabled. As documented [here](https://laravel.com/docs/10.x/installation#getting-started-on-windows).

## Composer

Laravel uses [Composer](https://getcomposer.org/doc/00-intro.md) for PHP dependancy management, So, you'll also need to ensure that your computer has it installed.

If you're on OSX you can check if it's already installed by running the following in the terminal.

```bash
composer --version
```

## Setup

Once you've installed Docker Desktop, and Composer download or clone the repository and follow these steps.

1. Open the terminal and `cd` into the cloned repository folder.
2. Run `composer install` Which will download the dependancies; it will take a while.
3. Run `./vendor/bin/sail up`

## Database

Before you can start using the project you need to import the database.

To connect to your application's MySQL database from your local machine, you may use a graphical database management application such as [TablePlus](https://tableplus.com/). By default, the MySQL database is accessible at `localhost` port 3306.

| Name     | Value                     |
| -------- | ------------------------- |
| Host     | localhost                 |
| Port     | 3306                      |
| Database | integration_engineer_task |
| Username | sail                      |
| Password | password                  |

From there import the `database.mysql` file.

#### Migrations

I know the brief said not to but, I did create a migrations file as well (Chalk it up to getting to grips with Laravel).

It can be run using:

`./vendor/bin/sail artisan migrate` form the repository root.

## All Set

You should now be able to access the site from:
`0.0.0.0`

# Code Notes & Reasonings

There's a few points I'd like to make about why I went about things the way I did, though pretty much all of them are _"To get a better understanding of Laravel"._

## Settings Model

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Models/Setting.php

I know the the task required simply saving the key, which means the model itself is a little overkill.

I decided to make it anyway, and to make it a little more complicated for myself, chose to make it non-static so I could store other settings. (Almost certainly breaking the way models work).

In the end it has two settings `api_key` and `per_page`.

## Settings Controller

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Http/Controllers/SettingsController.php

Similarly, if I'd kept the settings simple this would have been overkill particularly because there's only two routes.

### Update Settings Request

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Http/Requests/UpdateSettingsRequest.php

I created a separate Form Request file, to manage the validation rules and to validate the `api_key` against the `/me` MailerLite endpoint, in one motion.

## Subscriber Controller

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Http/Controllers/SubscriberController.php

### Update Subscriber Request

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Http/Requests/UpdateSubscriberRequest.php

## Ensure Key Is Set Middleware

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Http/Middleware/EnsureKeyIsSet.php

I created a middleware to redirect users to the settings page if they don't have an `api_key` set. I just thought it would look nicer, that having an error message.

## Settings Provider

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Providers/SettingsServiceProvider.php

Another one that's overkill, it just injects the `per_page` and `has_key` variables into the templates that need them. It could have gone into the routes themselves.

## MailerLite

https://github.com/TobyOsborne/integration-engineer-task/blob/main/app/Connectors/MailerLite.php

This is my helper class to send requests to the MailerLite API, I could have used the PHP SDK, but thought that would defeat the point of the task. I will note that I used the classic api endpoint because:

1. The new api doesn't have the `/me` endpoint, I needed to _"Validate an account's API key against the MailerLite API"_
2. The new api uses cursors for pagination while DataTables uses offsets.

Some things I noticed while working with the API.

> **/api/v2/subscriber/search** - The docs say it supports the limit parameter, but when I used it; it returned all matching results. (maybe because I have so few subscribers).
> **/api/v2/batch** - Doesn't seem to support the `/api/subscribers/count` endpoint. I so I ended up making two requests instead of my preferred one.

# Running Test

To run the PHP tests you can use the sail command in the terminal from within the repository root folder.

`./vendor/bin/sail test`
