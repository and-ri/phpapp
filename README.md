# PHPapp Framework

**PHPapp** is a lightweight PHP framework designed for quick and efficient web application development. It follows the MVC (Model-View-Controller) architecture and provides core components for database interaction, session management, request handling, and more.

## Features

- **MVC Architecture:** Clean separation of concerns with models, views, and controllers to organize code logically.
- **Core Components:** Includes essential libraries for handling sessions, database connections, request processing, and URL routing.
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

4. Create `.env` file
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
- **language.php**: Loads and manages language files for multi-language support.
- **load.php**: Loads models and controllers dynamically.
- **pagination.php**: Provides simple pagination functionality.
- **request.php**: Handles incoming HTTP requests.
- **response.php**: Manages HTTP responses and headers.
- **session.php**: Facilitates session management (start, get, set, remove, etc.).
- **staticfile.php**: Serves static files (CSS, JS, images).
- **url.php**: Generates URLs and manages routing.

## License

This project is licensed under the MIT License.