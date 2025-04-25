# PHPapp Framework

**PHPapp** is a lightweight PHP framework designed for quick and efficient web application development. It follows the MVC (Model-View-Controller) architecture and provides core components for data management, routing, and user interaction.

> [@and-ri](https://github.com/and-ri):
> Hello everyone! I am very excited to share with you a version of my PHP framework. Maybe it is far from such giants as Laravel or Yii, but it has become very convenient for me. Especially for quick prototyping and small applications.
> 
> I will be happy for any contribution :)

## Features

- **MVC Architecture:** Clean separation of concerns with models, views, and controllers to organize code logically.
- **Core Components:** Includes essential libraries for handling sessions, database connections, request processing, and URL routing.
- **Migration System:** Built-in system for managing database schema changes and versioning.
- **Installer:** Interactive installer for quick setup, including `.env` generation, Composer installation, and database migration execution.
- **Easy to Install and Configure:** Minimal setup required for developers to get started quickly.

## Requirements

- PHP 8+
- A web server (e.g., Apache, Nginx)
- MySQL

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/yourusername/PHPapp.git
    ```

2. Navigate to the project directory:

    ```bash
    cd PHPapp
    ```

3. Install dependencies:

    ```bash
    composer install
    ```

4. Run the installer:
    Open `http://yourdomain.com/installer.php` in your browser and follow the on-screen instructions to set up the database and configuration.

5. Done!

## Core Components

### Classes

- **controller.php**: Handles application logic, loading views and models.
- **model.php**: Manages database interactions and data-related operations.
- **view.php**: Renders views and passes data to the user interface.

### Libraries

- **app.php**: Manages the application lifecycle, including initialization and configuration.
- **db.php**: Provides methods for database queries and connection handling.
- **env.php**: Handles environment variables and configuration settings.
- **google_auth.php**: Handles the Google authentication process for the application.
- **language.php**: Loads and manages language files for multi-language support.
- **load.php**: Loads models and controllers dynamically.
- **pagination.php**: Provides simple pagination functionality.
- **request.php**: Handles incoming HTTP requests.
- **response.php**: Manages HTTP responses and headers.
- **session.php**: Facilitates session management (start, get, set, remove, etc.).
- **staticfile.php**: Serves static files (CSS, JS, images).
- **url.php**: Generates URLs and manages routing.
- **log.php**: Provides centralized logging functionality using Monolog.

### New Features

- **Migration System:** Manage database schema changes with ease. Use `php migrate.php migrate` to apply migrations, `php migrate.php rollback` to undo the last migration, and `php migrate.php status` to check migration status.
- **Interactive Installer:** Quickly set up your application by providing database and web configuration details in a user-friendly web installer.
- **Centralized Logging:** Monitor application events, errors, and debugging information using the integrated Monolog-based logging system.

## License

This project is licensed under the MIT License.