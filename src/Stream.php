<?php

declare(strict_types=1);

namespace Diseltoofast\PhpNinja;

use LengthException;
use InvalidArgumentException;

/**
 * A basic stream reader and writer.
 */
class Stream implements ReaderInterface, WriterInterface
{
    /**
     * The handle to the stream (resource).
     *
     * @var resource
     */
    private $handle;
    private string $endian;

    /**
     * Initializes a new instance of this class.
     *
     * @param string|resource $input
     */
    public function __construct(mixed $input)
    {
        if (is_resource($input)) {
            $this->handle = $input;
        } elseif (is_string($input)) {
            $this->setInputToMemory($input);
        } else {
            throw new InvalidArgumentException('Input must be a string or a stream resource.');
        }

        $this->endian = Endian::detect();
    }

    public function setInputToMemory(string $string): void
    {
        $handle = fopen('php://memory', 'rb+');
        fwrite($handle, $string);
        rewind($handle);
        $this->handle = $handle;
    }

    public function getStat(): array
    {
        return fstat($this->handle);
    }

    public function setEndian(string $endian): void
    {
        if (in_array($endian, [Endian::ENDIAN_BIG, Endian::ENDIAN_LITTLE], true)) {
            $this->endian = $endian;
        } else {
            throw new InvalidArgumentException('Invalid endian provided. Endian must be set as big or little');
        }
    }

    public function getEndian(): string
    {
        return $this->endian;
    }

    public function getSize(): int
    {
        return $this->getStat()['size'];
    }

    public function setPosition(int $position): void
    {
        fseek($this->handle, $position);
    }

    public function getPosition(): int
    {
        return ftell($this->handle);
    }

    public function skipPosition($bytes): void
    {
        fseek($this->handle, $bytes, SEEK_CUR);
    }

    public function resetPosition(): void
    {
        rewind($this->handle);
    }

    protected function read(int $bytes): ?string
    {
        if ($bytes < 1) {
            return null;
        }

        $chars = fread($this->handle, $bytes);
        return $chars !== false ? $chars : null;
    }

    public function readInt8(): int
    {
        if ($bytes = $this->read(1)) {
            return current(unpack('c', $bytes));
        }
        return 0;
    }

    public function readUInt8(): int
    {
        if ($bytes = $this->read(1)) {
            return current(unpack('C', $bytes));
        }
        return 0;
    }

    public function readInt16(): int
    {
        if ($bytes = $this->read(2)) {
            return current(unpack('s', $bytes));
        }
        return 0;
    }

    public function readUInt16(): int
    {
        if ($bytes = $this->read(2)) {
            return current(unpack($this->endian === Endian::ENDIAN_BIG ? 'n' : 'v', $bytes));
        }
        return 0;
    }

    public function readInt32(): int
    {
        if ($bytes = $this->read(4)) {
            return current(unpack('l', $bytes));
        }
        return 0;
    }

    public function readUInt32(): int
    {
        if ($bytes = $this->read(4)) {
            return current(unpack($this->endian === Endian::ENDIAN_BIG ? 'N' : 'V', $bytes));
        }
        return 0;
    }

    public function readInt64(): int
    {
        if ($bytes = $this->read(8)) {
            return current(unpack('q', $bytes));
        }
        return 0;
    }

    public function readUInt64(): int
    {
        if ($bytes = $this->read(8)) {
            return current(unpack($this->endian === Endian::ENDIAN_BIG ? 'J' : 'P', $bytes));
        }
        return 0;
    }

    public function readFloat(): float
    {
        if ($bytes = $this->read(4)) {
            return current(unpack($this->endian === Endian::ENDIAN_BIG ? 'G' : 'g', $bytes));
        }
        return 0;
    }

    public function readDouble(): float
    {
        if ($bytes = $this->read(8)) {
            return current(unpack($this->endian === Endian::ENDIAN_BIG ? 'E' : 'e', $bytes));
        }
        return 0;
    }

    public function readString(int $length): string
    {
        if ($bytes = $this->read($length)) {
            $length = min(strlen($bytes), $length); // fix end file
            return current(unpack('a' . $length, $bytes));
        }
        return '';
    }

    public function readStringUTF16(int $length, string $toEncoding): string
    {
        if ($bytes = $this->readString($length)) {
            return mb_convert_encoding($bytes, $toEncoding, $this->endian === Endian::ENDIAN_BIG ? 'UTF-16BE' : 'UTF-16LE');
        }
        return '';
    }

    public function readStringUTF32(int $length, string $toEncoding): string
    {
        if ($bytes = $this->readString($length)) {
            return mb_convert_encoding($bytes, $toEncoding, $this->endian === Endian::ENDIAN_BIG ? 'UTF-32BE' : 'UTF-32LE');
        }
        return '';
    }

    public function writeInt8(int $value): void
    {
        if ($value < -0x80 || $value > 0x7f) {
            throw new LengthException('Value out of range for signed 8-bit integer.');
        }

        $bytes = pack('c', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeUInt8(int $value): void
    {
        if ($value < 0 || $value > 0xff) {
            throw new LengthException('Value out of range for unsigned 8-bit integer.');
        }

        $bytes = pack('C', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeInt16(int $value): void
    {
        if ($value < -0x8000 || $value > 0x7fff) {
            throw new LengthException('Value out of range for signed 16-bit integer.');
        }

        $bytes = pack('s', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeUInt16(int $value): void
    {
        if ($value < 0 || $value > 0xffff) {
            throw new LengthException('Value out of range for unsigned 16-bit integer.');
        }

        $bytes = pack($this->endian === Endian::ENDIAN_BIG ? 'n' : 'v', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeInt32(int $value): void
    {
        if ($value < -0x80000000 || $value > 0x7fffffff) {
            throw new LengthException('Value out of range for signed 32-bit integer.');
        }

        $bytes = pack('l', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeUInt32(int $value): void
    {
        if ($value < 0 || $value > 0xffffffff) {
            throw new LengthException('Value out of range for unsigned 32-bit integer.');
        }

        $bytes = pack($this->endian === Endian::ENDIAN_BIG ? 'N' : 'V', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeInt64(int $value): void
    {
        if (PHP_INT_SIZE < 8) {
            throw new LengthException('Signed 64-bit integers require a 64-bit PHP build.');
        }

        if ($value < -0x8000000000000000 || $value > 0x7fffffffffffffff) {
            throw new LengthException('Value out of range for signed 64-bit integer.');
        }

        $bytes = pack('q', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeUInt64(int $value): void
    {
        if (PHP_INT_SIZE < 8) {
            throw new LengthException('Unsigned 64-bit integers require a 64-bit PHP build.');
        }

        if ($value < 0 || $value > 0xffffffffffffffff) {
            throw new LengthException('Value out of range for unsigned 64-bit integer.');
        }

        $bytes = pack($this->endian === Endian::ENDIAN_BIG ? 'J' : 'P', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeFloat(float $value): void
    {
        $bytes = pack($this->endian === Endian::ENDIAN_BIG ? 'G' : 'g', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeDouble(float $value): void
    {
        $bytes = pack($this->endian === Endian::ENDIAN_BIG ? 'E' : 'e', $value);
        fwrite($this->handle, $bytes);
    }

    public function writeString(string $value): void
    {
        $bytes = pack('A' . strlen($value), $value);
        fwrite($this->handle, $bytes);
    }

    public function writeStringUTF16(string $value, string $fromEncoding): void
    {
        $this->writeString(mb_convert_encoding($value, $this->endian === Endian::ENDIAN_BIG ? 'UTF-16BE' : 'UTF-16LE', $fromEncoding));
    }

    public function writeStringUTF32(string $value, string $fromEncoding): void
    {
        $this->writeString(mb_convert_encoding($value, $this->endian === Endian::ENDIAN_BIG ? 'UTF-32BE' : 'UTF-32LE', $fromEncoding));
    }
}
