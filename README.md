# emailcheck

PHP library for validating email addresses via regex syntax check and DNS MX lookup.

## Requirements

- PHP 8.3+

## Installation

```bash
composer require damirco/emailcheck
```

## Usage

### Single email

```php
use Damirco\Emailcheck\Email;

$email = new Email('user@example.com');

$email->isSyntaxValid(); // true — regex check
$email->isMxValid();     // true — DNS MX lookup
$email->isValid();       // true — both checks pass

(string) $email; // 'user@example.com'
```

### Batch validation

```php
use Damirco\Emailcheck\EmailSet;

$set = new EmailSet([
    'user@example.com',
    'invalid',
    'admin@gmail.com',
]);

// Both syntax + MX
$result = $set->check();
// ['valid' => ['user@example.com', 'admin@gmail.com'], 'invalid' => ['invalid']]

// Syntax only (no DNS)
$result = $set->checkSyntax();

// MX only (no syntax)
$result = $set->checkMxRecords();

// Custom: syntax only, skip MX
$result = $set->check(check_syntax: true, check_mx: false);
```

`EmailSet` deduplicates input and filters non-string values automatically.

## Testing

```bash
composer test
```

## License

[MIT](LICENSE)
