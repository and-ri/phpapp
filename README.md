# PHPapp Framework

**PHPapp** is a lightweight PHP framework designed for quick and efficient web application development. It follows the MVC (Model-View-Controller) architecture and provides core components for data management, routing, and user interaction.

> [@and-ri](https://github.com/and-ri):
> Hello everyone! I am very excited to share with you a version of my PHP framework. Maybe it is far from such giants as Laravel or Yii, but it has become very convenient for me. Especially for quick prototyping and small applications.
> 
> I will be happy for any contribution :)

## Features

- **MVC Architecture:** Clean separation of concerns with models, views, and controllers to organize code logically.
- **Core Components:** Includes essential libraries for handling sessions, database connections, request processing, and URL routing.
- **Secure by Default:** Twig auto-escaping, CSRF tokens, hardened sessions, and strict route validation out of the box.
- **Modern Frontend Build:** Tailwind CSS 4 + daisyUI bundled with Vite, with automatic cache busting.
- **Migration System:** Built-in system for managing database schema changes and versioning.
- **Installer:** Interactive installer for quick setup, including `.env` generation, Composer installation, and database migration execution.
- **Easy to Install and Configure:** Minimal setup required for developers to get started quickly.

## Requirements

- PHP 8+
- A web server (e.g., Apache, Nginx)
- MySQL
- Node.js 20+ and npm (only for building frontend assets)

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/and-ri/phpapp.git
    ```

2. Navigate to the project directory:

    ```bash
    cd phpapp
    ```

3. Install dependencies and build frontend assets:

    ```bash
    composer install
    npm install
    npm run build
    ```

4. Run the installer:
    Open `http://yourdomain.com/installer.php` in your browser and follow the on-screen instructions to set up the database and configuration.

5. Done! For security, delete `installer.php` from the `www` directory after installation (it refuses to run again while `.env` exists, but removing it entirely is safer).

## Frontend Assets

Frontend sources live in `static/css/style.css` and `static/js/app.js` and are bundled by Vite (Tailwind CSS 4 + daisyUI) into `www/assets/`:

```bash
npm run dev    # rebuild automatically on changes (vite build --watch)
npm run build  # one-off production build
```

Templates reference the built assets through `$this->staticfile->getAssetUri('css/app.css')`, which appends a cache-busting `?v=<timestamp>` parameter. The built files in `www/assets/` are committed to the repository, so remember to run `npm run build` before committing frontend changes.

## Templating and Security

- Twig **auto-escaping is enabled**: any variable printed with `{{ ... }}` is HTML-escaped. Pass trusted HTML (e.g. output of another controller) through `$this->view->raw($html)` — `Load::controller()` already does this for you.
- Every form should include the CSRF field with `{{ csrf }}` and validate it with `$this->session->validateToken($this->request->post['csrf'])` (see `app/controller/catalog/csrf_protection.php` for a complete example).
- Request data (`$this->request->get/post/cookie`) is raw. Escape at the output layer: Twig handles HTML, and use `$this->db->escape()` or `$this->db->execute($sql, $params)` (prepared statements) for SQL.

## Core Components

### Classes

- **controller.php**: Handles application logic, loading views and models.
- **model.php**: Manages database interactions and data-related operations.
- **view.php**: Renders views and passes data to the user interface.

### Libraries

- **app.php**: Manages the application lifecycle, including initialization and configuration.
- **cache.php**: Simple file-based cache with optional TTL (`get`, `set`, `delete`, `deleteAll`).
- **db.php**: Provides methods for database queries and connection handling, including prepared statements via `execute()`.
- **env.php**: Handles environment variables and configuration settings.
- **google_auth.php**: Handles the Google authentication process for the application.
- **language.php**: Loads and manages language files for multi-language support.
- **load.php**: Loads models and controllers dynamically.
- **pagination.php**: Provides simple pagination functionality.
- **request.php**: Handles incoming HTTP requests.
- **response.php**: Manages HTTP responses and headers.
- **session.php**: Facilitates session management (start, get, set, remove, etc.) and CSRF tokens.
- **staticfile.php**: Serves static files (CSS, JS, images) and generates cache-busted URIs for built assets.
- **url.php**: Generates URLs and manages routing.
- **log.php**: Provides centralized logging functionality using Monolog.
- **meta.php**: Manages SEO metadata including page titles, descriptions, Open Graph tags, and robots directives using the Melbahja/Seo package.

## Migrations

Create a migration by copying `migrations/template.php` to a new file (e.g. `migrations/2026_07_04_create_users.php`). A migration returns an anonymous class with `up()` and `down()` methods:

```php
return new class {
    public function up($db) {
        $db->query("CREATE TABLE ...");
    }

    public function down($db) {
        $db->query("DROP TABLE ...");
    }
};
```

Then run:

```bash
php migrate.php migrate   # apply pending migrations
php migrate.php rollback  # undo the last migration
php migrate.php status    # show applied and pending migrations
```

## License

This project is licensed under the MIT License.
