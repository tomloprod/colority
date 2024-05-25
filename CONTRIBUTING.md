# 🧑‍🤝‍🧑 Contributing

Contributions are welcome, and are accepted via pull requests.
Please review these guidelines before submitting any pull requests.

## Process

1. Fork the project
1. Create a new branch
1. Code, test, commit and push
1. Open a pull request detailing your changes.

## Guidelines

Colority uses a few tools to ensure the code quality and consistency. [Pest](https://pestphp.com) is the testing framework of choice, and we also use [PHPStan](https://phpstan.org) for static analysis. Pest's type coverage is at 100%, and the test suite is also at 100% coverage.

In terms of code style, we use [Laravel Pint](https://laravel.com/docs/11.x/pint) to ensure the code is consistent and follows the Laravel conventions. We also use [Rector](https://getrector.org) to ensure the code is up to date with the latest PHP version.

You run these tools individually using the following commands:

```bash
# Lint the code using Pint
composer lint
composer test:lint

# Refactor the code using Rector
composer refactor
composer test:refactor

# Run PHPStan
composer test:types

# Run the test suite
composer test:unit

# Run all the tools
composer test
```
