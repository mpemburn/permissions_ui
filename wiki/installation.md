### Installation
1. Begin by cloning this project to your system:

    `git clone https://github.com/mpemburn/permissions_ui.git`
    
    If you wish to give the project a different name, do so like this:
    
    `git clone https://github.com/mpemburn/permissions_ui.git my_project`

2. Change to the `permissions_ui` directory. 
3. Copy the file `.env.example` to `.env`.
4. Create a local database. By default, this will be called `permissions_ui`.  You can name it whatever you like, but you'll need to change the `DB_DATABASE` value in `.env` to match.
5. Create the data tables by running `php artisan migrate`.
6. Run `composer install` to pull in the PHP packages and dependencies.
7. Create the application key by running `php artisan key:generate`.
8. Install Passport by running `php artisan passport:install`.
9. Install `npm` in the project by running `npm install`. 
    
    **NOTE**: If `npm` is not installed, you can get it here: https://www.npmjs.com/get-npm
10. Compile the Javascript and CSS assets by running `npm run dev`.
