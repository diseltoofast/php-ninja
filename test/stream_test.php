<?php

require __DIR__ . '/../vendor/autoload.php';

use Diseltoofast\PhpNinja\Stream;

function assert_eq($a, $b, $msg = '') {
    if ($a !== $b) {
        echo "FAIL: " . ($msg ?: "Values differ") . "\n";
        var_dump($a);
        var_dump($b);
        exit(1);
    }
}

$stream = new Stream('');

// Int8
$stream->writeInt8(-5);
$stream->resetPosition();
assert_eq($stream->readInt8(), -5, 'int8');

// UInt8
$stream = new Stream('');
$stream->writeUInt8(250);
$stream->resetPosition();
assert_eq($stream->readUInt8(), 250, 'uint8');

// Int16
$stream = new Stream('');
$stream->writeInt16(-32000);
$stream->resetPosition();
assert_eq($stream->readInt16(), -32000, 'int16');

// UInt16
$stream = new Stream('');
$stream->writeUInt16(65000);
$stream->resetPosition();
assert_eq($stream->readUInt16(), 65000, 'uint16');

// Int32
$stream = new Stream('');
$stream->writeInt32(-2000000000);
$stream->resetPosition();
assert_eq($stream->readInt32(), -2000000000, 'int32');

// UInt32
$stream = new Stream('');
$stream->writeUInt32(4000000000);
$stream->resetPosition();
assert_eq($stream->readUInt32(), 4000000000, 'uint32');

// Float
$stream = new Stream('');
$stream->writeFloat(3.14159);
$stream->resetPosition();
assert_eq(round($stream->readFloat(), 5), 3.14159, 'float');

// Double
$stream = new Stream('');
$stream->writeDouble(2.718281828459045);
$stream->resetPosition();
assert_eq(round($stream->readDouble(), 12), 2.718281828459, 'double');

// String
$stream = new Stream('');
$stream->writeString("hello");
$stream->resetPosition();
assert_eq($stream->readString(5), 'hello', 'string');

echo "All tests passed.\n";
