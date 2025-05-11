# Contributing to PHPapp

Thank you for considering contributing to PHPapp! This document provides guidelines and best practices to help you make your contributions effectively.

---

## Table of Contents

1. [How to Contribute](#how-to-contribute)
2. [Code of Conduct](#code-of-conduct)
3. [Development Workflow](#development-workflow)
4. [Pull Request Guidelines](#pull-request-guidelines)
5. [Reporting Issues](#reporting-issues)
6. [Community and Support](#community-and-support)

---

## How to Contribute

We welcome contributions of all kinds including bug fixes, new features, documentation improvements, and more. Here's how you can get started:

1. **Fork the Repository**: Create a personal copy of the repository by forking it to your GitHub account.
2. **Clone the Repository**: Clone your fork to your local machine.

   ```bash
   git clone https://github.com/<your-username>/phpapp.git
   ```

3. **Create a Feature Branch**: Create a new branch for your change.

   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Write Clean Code**: Follow the project’s coding standards and structure. Familiarize yourself with the MVC (Model-View-Controller) architecture used in PHPapp.

5. **Test Your Changes**: Ensure that your contribution doesn't break existing functionality. Add tests if applicable.

6. **Commit and Push**: Write clear and descriptive commit messages.

   ```bash
   git commit -m "feat: add feature description"
   git push origin feature/your-feature-name
   ```

7. **Open a Pull Request**: Head to the original repository and submit a pull request.

---

## Code of Conduct

Please adhere to the [Code of Conduct](CODE_OF_CONDUCT.md) to maintain a welcoming and inclusive community. Be respectful and constructive in your interactions.

---

## Development Workflow

### 1. Setting Up the Environment

- Ensure you have PHP 8+ installed.
- Install dependencies using Composer:

  ```bash
  composer install
  ```

- Use the interactive installer to set up the project by visiting `http://yourdomain.com/installer.php`.

### 2. Directory Structure

The framework follows the MVC architecture. Key directories include:

- `app/`: Contains models, views, and controllers.
- `core/`: Houses essential libraries such as `db.php`, `request.php`, and `response.php`.
- `static/`: Static files like CSS, JavaScript, and images.
- `config/`: Configuration files.

Review the README for more details on the project's structure.

---

## Pull Request Guidelines

To ensure a smooth review process:

- **Branch Naming Convention**: Use descriptive names such as `fix/issue-name` or `feature/feature-name`.
- **Write Descriptive Commit Messages**: Clearly explain the purpose of your changes.
- **Keep Changes Focused**: Avoid bundling unrelated changes in a single pull request.
- **Add Tests**: Include tests for new features or bug fixes.
- **Link Issues**: If your pull request addresses an issue, reference it in the description (e.g., `Fixes #123`).

---

## Reporting Issues

Found a bug or have a feature request? Open an [issue](https://github.com/and-ri/phpapp/issues) and provide the following:

1. A clear and descriptive title.
2. Steps to reproduce the issue (if applicable).
3. Expected and actual behavior.
4. Screenshots, logs, or error messages (if available).

---

## Community and Support

For questions, discussions, or collaboration, feel free to:

- Start a [GitHub Discussion](https://github.com/and-ri/phpapp/discussions).
- Join issues or pull request threads for ongoing topics.
- Reach out to the repository owner directly for critical matters.

We appreciate your time and effort in contributing to PHPapp. Together, we can make it an even better framework for developers worldwide!