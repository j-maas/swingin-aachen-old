# Swingin&rsquo; Aachen
## Gettings started
### Requirements
1. Install PHP and MySQL. (On Windows, we recommend [XAMPP].)
2. Install [Composer].
3. Install [Laravel].
4. Install [Node.js]. (For compiling the assets, like CSS.)

[XAMPP]: https://www.apachefriends.org/index.html
[Composer]: https://getcomposer.org/
[Laravel]: https://laravel.com/
[Node.js]: https://nodejs.org/

### Setup
1. Configure the `.env` file.
    1. Copy the example file: `cp .env.example .env`
    2. Create a database table in MySQL.
    3. Adapt the `DB_*` settings in `.env` to allow a connection to that database table.
2. Migrate the database: `php artisan migrate`
3. Seed the database with initial data: `php artisan db:seed`
4. Add an admin user: `php artisan orchid:admin`
5. Install the dependencies for the asset pipeline: `npm install`

### Running
```bash
php artisan serve
```
This command will run a development web server. You can then view your website in the browser by connecting to the URL indicated in the console.

To also recompile the assets (like the CSS files), you can instruct the asset pipeline to compile all files whenever you save them:
```bash
npm run watch
```

## Documentation
- [Laravel](https://laravel.com/docs/6.x)
- [Orchid](https://orchid.software/en/docs) (for the admin interface)
