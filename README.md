[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=bugs)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=coverage)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Lines of Code](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=ncloc)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=RocketPHP-org_RocketUI&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=RocketPHP-org_RocketUI)

# RocketUI

RocketUI turns XML view definitions into ready-to-render JSON responses. It powers dynamic forms and grids by hydrating layouts with values sourced from PHP domain objects and can also export React JSON Schema Form (RJSF) compatible structures.

## Features
- Parse XML layouts (validated with the bundled XSDs) into structured form and grid definitions.
- Hydrate field values from PHP objects via public properties or getters, including nested dot-notation lookups.
- Generate RJSF `schema`, `uiSchema`, and `formData` payloads from the native layout JSON.
- Support UI primitives such as containers, tabs, inputs, selects, status chips, file uploads, toasts, and actions.
- Provide a small utility layer for conditional rendering, value parsing, and reusable layout components.

## Requirements
- PHP 8.2 or higher (required by PHPUnit 11).
- The `ext-dom` and `ext-libxml` extensions.
- Composer for dependency management.

## Installation
```bash
composer require rocket-php/rocket-ui
```

If you are working within this repository, install the dev dependencies first:
```bash
composer install
```

## Defining a Form
Layouts are described in XML and validated against `src/Views/Form/XSD/schema.xsd`. A minimal example looks like:
```xml
<RocketForm xmlns="http://rocket.com/rf-schema">
  <metadata>
    <title>Customer Profile</title>
    <description>Primary contact information</description>
    <version>1.0</version>
  </metadata>
  <layout type="modal">
    <container id="main" label="Details" direction="column">
      <input name="name" type="text" label="Name" required="true" />
      <input name="email" type="email" label="Email" />
      <status name="status" label="Status" />
    </container>
  </layout>
  <actions>
    <button type="primary" name="submit" label="Save" />
  </actions>
</RocketForm>
```
See `tests/data/form.xml` and `tests/data/grid.xml` for more complete samples and schema coverage.

## Usage
```php
use RocketPhp\RocketUI\UIEngine;
use RocketPhp\RocketUI\Views\Form\Form;

$form = new Form(__DIR__ . '/form.xml');
$data = new class () {
    public string $name = 'Ada Lovelace';
    public function getEmail(): string { return 'ada@example.com'; }
    public function getStatus(): string { return 'active'; }
};

$engine = new UIEngine();

// Native form layout (metadata, layout blocks, actions)
$jsonLayout = $engine->buildForm($form, $data);

// React JSON Schema Form payloads
$rjsfPayload = $engine->buildRJSFSchema($form, $data);
```
The `buildForm` method walks the layout, resolves values (including nested properties like `address.street`), and returns a JSON string. `buildRJSFSchema` adapts that response for RJSF consumers, producing the familiar `schema`, `uiSchema`, and `formData` arrays.

Grids follow the same pattern via `RocketPhp\RocketUI\Views\Grid\Grid` definitions and `UIEngine::buildGrid()`.

## Running Tests
Execute the full suite with:
```bash
./vendor/bin/phpunit
```

To continuously run tests while developing, the project ships with [`spatie/phpunit-watcher`](https://github.com/spatie/phpunit-watcher):
```bash
./vendor/bin/phpunit-watcher watch
```

The provided tests (`tests/formTest.php`) also demonstrate real-world usage, including XML validation, value hydration, and RJSF conversion.

## Project Structure
- `src/UIEngine.php` – entry point for converting form and grid definitions into JSON payloads.
- `src/Views/Form` – XML parsing, layout components, actions, and helper services for forms.
- `src/Views/Grid` – grid layout parsing and rendering helpers.
- `src/Adapter/RjsfSchemaBuilder.php` – converts native form layouts into RJSF structures.
- `tests/` – PHPUnit suites plus fixture XML/JSON data.

## License
RocketUI is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
