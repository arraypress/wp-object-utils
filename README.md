# WordPress Object Utilities

Minimal, memorable object manipulation utilities for everyday PHP development. Just 5 methods you'll actually use and remember.

## Features

* 🎯 **Focused**: Only the most useful object operations
* 🧠 **Memorable**: Simple method names you'll remember months later
* 📝 **Change Tracking**: Compare objects with new data
* 🔍 **Dot Notation**: Access nested properties easily
* 🎨 **Clean API**: Intuitive, consistent interface

## Installation

```bash
composer require arraypress/wp-object-utils
```

## Quick Start

```php
use ArrayPress\ObjectUtils\Obj;

// Track what changed
$changes = Obj::changes( $user, $_POST );

// Apply updates
Obj::apply( $user, $changes );

// Get nested values
$city = Obj::get( $user, 'address.city', 'Unknown' );

// Extract subset
$public = Obj::pick( $user, [ 'name', 'email' ] );

// Convert to array
$array = Obj::to_array( $user );
```

## Methods

### changes()
Compare an object with new data and get what changed:
```php
$user    = (object) [ 'name' => 'John', 'email' => 'john@example.com' ];
$changes = Obj::changes( $user, [ 'name' => 'John', 'email' => 'new@example.com' ] );
// Returns: ['email' => 'new@example.com']
```

### apply()
Apply changes to an object (only updates existing properties):
```php
$count = Obj::apply( $user, [ 'name' => 'Jane', 'email' => 'jane@example.com' ] );
// Returns: 2 (number of properties updated)
```

### get()
Get nested values using dot notation:
```php
$data = (object) [ 'user' => (object) [ 'address' => [ 'city' => 'NYC' ] ] ];
$city = Obj::get( $data, 'user.address.city' );
// Returns: 'NYC'
```

### pick()
Extract specific properties:
```php
$user   = (object) [ 'id' => 1, 'name' => 'John', 'password' => 'secret' ];
$public = Obj::pick( $user, [ 'id', 'name' ] );
// Returns: (object) ['id' => 1, 'name' => 'John']
```

### to_array()
Convert object to array:
```php
$array = Obj::to_array( $user );        // Public properties only
$array = Obj::to_array( $user, true );  // Include private/protected
```

## Requirements

- PHP 7.4+
- WordPress 5.0+ (optional, works with any PHP project)

## License

GPL-2.0-or-later

## Support

- [Documentation](https://github.com/arraypress/wp-object-utils)
- [Issue Tracker](https://github.com/arraypress/wp-object-utils/issues)