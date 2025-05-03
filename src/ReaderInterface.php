<?php

declare(strict_types=1);

namespace Diseltoofast\PhpNinja;

interface ReaderInterface
{
    public function readInt8(): int;

    public function readUInt8(): int;

    public function readInt16(): int;

    public function readUInt16(): int;

    public function readInt32(): int;

    public function readUInt32(): int;

    public function readInt64(): int;

    public function readUInt64(): int;

    public function readFloat(): float;

    public function readDouble(): float;

    public function readString(int $length): string;

    public function readStringUTF16(int $length, string $toEncoding): string;

    public function readStringUTF32(int $length, string $toEncoding): string;
}
