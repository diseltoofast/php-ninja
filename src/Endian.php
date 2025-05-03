<?php

declare(strict_types=1);

namespace Diseltoofast\PhpNinja;

class Endian
{
    /** Big-endian byte order (0x01 0x02 0x03 0x04). */
    public const ENDIAN_BIG = 'big';

    /** Little-endian byte order (0x04 0x03 0x02 0x01). */
    public const ENDIAN_LITTLE = 'little';

    public static function detect(): string
    {
        $value = 0x00FF;
        $packed = pack('S', $value);

        if ($value === current(unpack('v', $packed))) {
            return self::ENDIAN_LITTLE;
        }

        return self::ENDIAN_BIG;
    }

    /** Converts the endian of a number from big to little or vise-versa */
    public static function convert(int $value): int
    {
        $data = dechex($value);

        if (strlen($data) <= 2) {
            return $value;
        }

        $unpack = unpack("H*", strrev(pack("H*", $data)));
        return hexdec($unpack[1]);
    }
}
