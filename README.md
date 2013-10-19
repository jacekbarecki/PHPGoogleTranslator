PHPGoogleTranslator
===================

A simple PHP wrapper over Google Translate API methods.

## Overview
GoogleTranslator class offers 3 static methods which are the same as the ones that are available in Google Translate API:
`translate()`, `detect()` and `languages()` (see the [API documentation](https://developers.google.com/translate/) for the details). The class uses the cURL library to connect with the API. When something goes wrong, it throws some nice exceptions with detailed error messages.

## Setup
Open `GoogleTranslator.php` and enter your configuration:
- `$_apiKey` - paste your Google Translate API key,
- `$_testMode` - set it to true/false depending on your needs.


**The test mode**

When the test mode is enabled, no requests to Google Translate API are made but `translate()` method still returns some result. You can use the test mode just to check whether your app works fine and avoid getting billed for the translations.
The other methods - `detect()` and `languages()` return null when working in the test mode.

## Usage

Include `GoogleTranslator.php` in your project and just get some translations:

```php
require_once 'GoogleTranslator.php';
echo GoogleTranslator::translate('fr', 'Hello world!'); //prints out 'Bonjour tout le monde!'
```

Each of the three methods - `translate()`, `detect()` and `languages()` - is documented right in the code. Read the
documentation to check how to use each of them.
