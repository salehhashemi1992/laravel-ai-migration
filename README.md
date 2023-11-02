# Laravel IntelliDB

[![Latest Version on Packagist](https://img.shields.io/packagist/v/salehhashemi/laravel-intelli-db.svg?style=flat-square)](https://packagist.org/packages/salehhashemi/laravel-intelli-db)
[![Total Downloads](https://img.shields.io/packagist/dt/salehhashemi/laravel-intelli-db.svg?style=flat-square)](https://packagist.org/packages/salehhashemi/laravel-intelli-db)
[![GitHub Actions](https://img.shields.io/github/actions/workflow/status/salehhashemi1992/laravel-intelli-db/tests.yml?branch=main&label=tests)](https://github.com/salehhashemi1992/laravel-intelli-db/actions/workflows/tests.yml)
[![GitHub Actions](https://img.shields.io/github/actions/workflow/status/salehhashemi1992/laravel-intelli-db/static-analysis.yml?branch=main&label=static-analysis)](https://github.com/salehhashemi1992/laravel-intelli-db/actions/workflows/static-analysis.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)

A Laravel package that provides an intelligent way to generate database-related components using OpenAI.

It extends the default `artisan make` commands in Laravel to automatically generate the content of each component using AI, based on the provided description.

![Header Image](./assets/header.png)

## Features

The following commands are implemented in this package:

- `ai:rule` - Generate custom validation rules using AI
- `ai:migration` - Generate migration files using AI
- `ai:factory` - Generate factory files using AI
- `ai:model` - Generate model files using AI

Stay tuned for future updates as we continue to expand the capabilities of the Laravel Intelli DB package.

## Installation

1. Install the package via composer:
    ```
    composer require salehhashemi/laravel-intelli-db
    ```

2. Publish the configuration file:
    ```
    php artisan vendor:publish --provider="Salehhashemi\LaravelIntelliDb\LaravelIntelliDbServiceProvider"
    ```

3. Add your OpenAI API key to the `.env` file:
    ```
    OPEN_AI_KEY=your_openai_key
    ```

4. Optionally, you can change the default model used by OpenAI in the `.env` file:
    ```
   OPEN_AI_MODEL=gpt-4
    ```

To adjust the behavior of the AI text generation, you can set the following parameters in your configuration file:
```
'temperature' => 0.7
'frequency_penalty' => 0.2
```


## Usage

### ai:rule

To create a new validation rule using AI, run the following command:

```
php artisan ai:rule YourRuleName
```

You can provide the description of the rule using the `--description` option:

```
php artisan ai:rule YourRuleName --description="Your rule description"
```

If you don't provide a description, it will ask for it interactively.

The generated rule class will be placed in the `app/Rules` directory.

### ai:migration

To create a new migration file using AI, run the following command:

```
php artisan ai:migration your_migration_name
```

You can provide the description of the migration using the `--description` option:

```
php artisan ai:migration your_migration_name --description="Your migration description"
```

You can also specify the table name for the migration with the `--table` option:

```
php artisan ai:migration your_migration_name --table=your_table_name
```

This will append the schema of the desired table to provide a better result.

And you can set the location where the migration file should be created using the `--path` option:

```
php artisan ai:migration your_migration_name --path=path/to/migrations
```

If you don't provide a description, it will ask for it interactively.

The generated migration file will be placed in the `database/migrations` directory or the specified path.

### ai:factory

To create a new factory file using AI, run the following command:

```
php artisan ai:factory YourFactoryName
```

You can provide the name of the model for which the factory will be created using the `--model` option:

```
php artisan ai:factory YourFactoryName --model="YourModelName"
```

If you don't provide the model, it will ask for it interactively.

The generated factory file will be placed in the `database/factories` directory.

### ai:model

To create a new model file using AI, run the following command:

```
php artisan ai:model YourModelName
```

The generated model file will be placed in the `app/Models` directory.

The generated model will include PHP DocBlock comments for properties, relationships, and methods with appropriate type hints.

## Examples

### ai:rule

To create a rule that validates a unique email address, run:

```
php artisan ai:rule UniqueEmail --description="Validate unique email address"
```

### ai:migration

To create a migration that adds an email column to the users table, run:

```
php artisan ai:migration AddEmailToUsersTable --description="Add email column to users table" --table=users
```

### ai:factory

To create a factory for the User model, run:

```
php artisan ai:factory UserFactory --model="User"
```

### ai:model

To create a model named Address, run:

```
php artisan ai:model Address
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Saleh Hashemi](https://github.com/salehhashemi1992)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.