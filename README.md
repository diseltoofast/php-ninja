PHP Ninja
==============

This PHP library simplifies working with binary data.

## Installation

Requirements
- PHP 8.0 or higher
- ext-mbstring

Install via Composer:

```bash
composer require diseltoofast/php-ninja
```

## Reading

```php
// Reading from a resource
$binaryData = fopen('example.file', 'rb');
$reader = new \Diseltoofast\PhpNinja\Stream($binaryData);
// Or reading from raw data
$fileData = file_get_contents('example.file');
$reader = new \Diseltoofast\PhpNinja\Stream($fileData);

$intValue = $reader->readInt8(); // Reads a single-byte signed integer
$intValue = $reader->readUInt32(); // Reads a 4-byte unsigned integer
$stringValue = $reader->readString(8); // Reads an 8-byte string
$stringValueUTF16 = $reader->readStringUTF16(8, 'UTF-8'); // Reads an 8-byte string from UTF-16BE or UTF-16LE
```

## Writing

```php
// Writing to a resource
$file = fopen('example.file', 'wb');

$writer = new \Diseltoofast\PhpNinja\Stream($file);
$writer->writeInt8(100); // Writes a single-byte signed integer
$writer->writeUInt32(1000000000); // Writes a 4-byte unsigned integer
$writer->writeString('Hello world!'); // Writes a string
$writer->writeStringUTF16('Привет мир!', 'UTF-8'); // Writes a string in UTF-16BE or UTF-16LE
```
