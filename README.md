
<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## User Interface for Spatie Laravel Permissions

The **Permissions UI** project is shows an approach to creating a User Interface (UI) for the Spatie Laravel Permissions package.  You can find this here:

https://spatie.be/docs/laravel-permission/v3/introduction

**Permissions UI** was created with Laravel Version 8.x and includes the following packages:
- Laravel Breeze (https://github.com/laravel/breeze)
- Laravel Passport (https://laravel.com/docs/8.x/passport)
- Laravel Collective Forms & HTML (https://laravelcollective.com/docs/6.x/html)

### Installation
1. Begin by cloning this project to your system:

    `git clone https://github.com/mpemburn/permissions_ui.git`

2. Change to the `permissions_ui` directory. 
3. Copy the file `.env.example` to `.env`.
4. Create a local database. By default, this will be called `permissions_ui`.  You can name it whatever you like, but you'll need to change the `DB_DATABASE` value in `.env` to match.
5. Create the data tables by running `php artisan migrate`.
6. Run `composer install` to pull in the PHP packages and dependencies.
7. Create the application key by running `php artisan key:generate`.
8. Install Passport by running `php artisan passport:install`.
9. Install `npm` in the project by running `npm install`. NOTE: If `npm` is not installed, you can get it here: https://www.npmjs.com/get-npm
10. Compile the Javascript and CSS assets by running `npm run dev`.

### Running
There are a number of ways to run this project on your local system this simplest is to use Laravel's `artisan` server:
1. Go to your project's root (the `permissions_ui` directory) and run `php artisan serve`.  It should say:

    **Starting Laravel development server:** `http://127.0.0.1:8000`
2. Browse to `http://127.0.0.1:8000`

Another Laravel-friendly way to run the project with a few more sophisticated options is Larvel Valet.  You can get this here:

https://laravel.com/docs/8.x/valet

There are several other options as well.  You might try **Vagrant** (https://www.vagrantup.com) or **Docker** (https://www.docker.com).  Each has its advantages.

### Using the Permissions UI
Once you have the project up and running, you should be able to create a new user account by going to the **Register** link at the upper right corner of the screen (or add `/register` to the URL).  Enter your name, email, and password, and you'll be logged in automatically.

On the **Dashboard**, you'll see the **Admin** menu.  It contains three items: **Roles**, **Permissions**, and **User Roles**. You can create each of these entities manually or, for a quick start, run:

`php artisan quickstart --email=myemail@sample.com`

...replacing the "myemail@sample.com" with the email address you used to create a login account.

Now, if you go to **Admin**->**Roles** on the menu, you should see something like this:

![](wiki/roles_screenshot.png)

Going to the **Admin**->**Permissions** menu will show you this:

![](wiki/permissions_screenshot.png)

And finally  **Admin**->**User Roles** menu will look like this:


![](wiki/user_roles_screenshot.png)


















### License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
