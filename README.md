# Snawbar DataTable

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

A Laravel package to build server-side DataTable with custom column builders and easy frontend integration.

## Installation

```
composer require mikailfaruqali/datatable
```

## Usage

Publish the config (if applicable) or just use the package classes in your Laravel app:

```
php artisan vendor:publish --provider="Snawbar\DataTable\DataTableServiceProvider" --tag="config"
```

## 🛠 Create a DataTable Class

This package provides an Artisan command to generate a new DataTable class quickly.

### 📦 Command

```
php artisan make:datatable UserDatatable
```

This will generate a new file at:

```
app/DataTables/ProductCategoryDatatable.php
```


### 📁 Stub Example

The generated class will extend the base `Snawbar\DataTable\DataTable` and include the required methods like:

- `query(Request $request): Builder`
- `columns(): array`
- `setupColumns(): void`
- `tableId(): string`
- `filterContainer(): string`
- `tableClass(): ?string`
- `isOrderable(): bool`
- `length(): int`
- `shouldJumpToLastPage(): int`


## Requirements

- PHP >= 7.4  
- Laravel (or illuminate/contracts) >= 5.0  
- illuminate/database (Laravel's database/query builder)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Author

Snawbar — [alanfaruq85@gmail.com](mailto:alanfaruq85@gmail.com)


## Links

- [GitHub Repository](https://github.com/mikailfaruqali/datatable)