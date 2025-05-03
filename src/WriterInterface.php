<?php

declare(strict_types=1);

namespace Diseltoofast\PhpNinja;

interface WriterInterface
{
    public function writeInt8(int $value): void;

    public function writeUInt8(int $value): void;

    public function writeInt16(int $value): void;

    public function writeUInt16(int $value): void;

    public function writeInt32(int $value): void;

    public function writeUInt32(int $value): void;

    public function writeInt64(int $value): void;

    public function writeUInt64(int $value): void;

    public function writeFloat(float $value): void;

    public function writeDouble(float $value): void;

    public function writeString(string $value): void;

    public function writeStringUTF16(string $value, string $fromEncoding): void;

    public function writeStringUTF32(string $value, string $fromEncoding): void;
}
