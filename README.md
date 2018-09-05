# Advanced Custom Fields Encrypt Field Option

Adds an option to encrypt text field values upon save. Useful for storing sensitive data, such as API keys.

## Requirements

- [Advanced Custom Fields](https://www.advancedcustomfields.com/) plugin
- PHP 7+ with OpenSSL enabled

## Installation

Download and unzip plugin direcrtory inside of `wp-content/plugins` and activate plugin

Define the `ACF_EFO_SECRET_KEY` constant inside of `wp-config.php`

```
/** ACF Encrypt Field Option Key */
define('ACF_EFO_SECRET_KEY', 'your key here');
```

## Screen Shots

### Field Options

![Field Options Settings](/field-options.png?raw=true "Field Options Settings")

### Post Edit

![Post Edit](/post-edit.png?raw=true "Post Edit")