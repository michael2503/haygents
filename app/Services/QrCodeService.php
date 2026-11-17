<?php

namespace App\Services;

/**
 * Pure PHP QR Code Generator (Versions 1-3, Byte Mode, ECC Level M/L)
 * Produces valid, scannable QR Code SVG and PNG Data URIs without external dependencies.
 */
class QrCodeService
{
    // GF(256) log and exp tables for primitive polynomial 0x11D (285)
    private static array $exp = [];
    private static array $log = [];
    private static bool $tablesInitialized = false;

    private static function initTables(): void
    {
        if (self::$tablesInitialized) {
            return;
        }

        self::$exp = array_fill(0, 512, 0);
        self::$log = array_fill(0, 256, 0);

        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$exp[$i] = $x;
            self::$log[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11D;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            self::$exp[$i] = self::$exp[$i - 255];
        }

        self::$tablesInitialized = true;
    }

    private static function gfMul(int $x, int $y): int
    {
        if ($x === 0 || $y === 0) {
            return 0;
        }
        return self::$exp[self::$log[$x] + self::$log[$y]];
    }

    private static function rsPoly(int $ecLen): array
    {
        self::initTables();
        $poly = [1];
        for ($i = 0; $i < $ecLen; $i++) {
            $next = [0];
            $factor = self::$exp[$i];
            $temp = [];
            foreach ($poly as $c) {
                $temp[] = self::gfMul($c, $factor);
            }
            // ($poly * x) + ($poly * factor)
            $pLen = count($poly);
            $newPoly = array_fill(0, $pLen + 1, 0);
            for ($j = 0; $j < $pLen; $j++) {
                $newPoly[$j] ^= $poly[$j];
                $newPoly[$j + 1] ^= $temp[$j];
            }
            $poly = $newPoly;
        }
        return $poly;
    }

    private static function rsEncode(array $data, int $ecLen): array
    {
        self::initTables();
        $gen = self::rsPoly($ecLen);
        $remainder = array_fill(0, $ecLen, 0);

        foreach ($data as $byte) {
            $factor = $byte ^ $remainder[0];
            array_shift($remainder);
            $remainder[] = 0;
            if ($factor !== 0) {
                $leadExp = self::$log[$factor];
                for ($i = 0; $i < $ecLen; $i++) {
                    $remainder[$i] ^= self::$exp[($leadExp + self::$log[$gen[$i + 1]]) % 255];
                }
            }
        }

        return $remainder;
    }

    /**
     * Determines QR version based on byte length.
     * Version 1 (21x21): 16 data bytes (ECC M), 19 bytes (ECC L)
     * Version 2 (25x25): 28 data bytes (ECC M), 34 bytes (ECC L)
     * Version 3 (29x29): 44 data bytes (ECC M), 55 bytes (ECC L)
     * Version 4 (33x33): 64 data bytes (ECC M), 80 bytes (ECC L)
     */
    private static function getVersionSpecs(int $dataLen): array
    {
        // Specs table: [version, size, totalBytes, dataBytes, ecBytes, alignPos]
        $specs = [
            1 => ['size' => 21, 'total' => 26, 'data' => 19, 'ec' => 7, 'align' => []],
            2 => ['size' => 25, 'total' => 44, 'data' => 34, 'ec' => 10, 'align' => [6, 18]],
            3 => ['size' => 29, 'total' => 70, 'data' => 55, 'ec' => 15, 'align' => [6, 22]],
            4 => ['size' => 33, 'total' => 100, 'data' => 80, 'ec' => 20, 'align' => [6, 26]],
        ];

        foreach ($specs as $ver => $spec) {
            if ($dataLen + 2 <= $spec['data']) { // 2 bytes for mode & length
                return array_merge(['version' => $ver], $spec);
            }
        }

        return array_merge(['version' => 4], $specs[4]);
    }

    /**
     * Encode text into bits stream and return QR matrix (2D boolean array)
     */
    public static function createMatrix(string $text): array
    {
        $rawBytes = unpack('C*', $text);
        $rawBytes = array_values($rawBytes);
        $dataLen = count($rawBytes);

        $spec = self::getVersionSpecs($dataLen);
        $maxDataBytes = $spec['data'];
        $ecBytes = $spec['ec'];
        $size = $spec['size'];
        $alignPos = $spec['align'];

        // Bit stream construction (Byte mode = 0100)
        $bits = [0, 1, 0, 0];

        // 8-bit character count indicator for Versions 1-9
        $charCount = min($dataLen, $maxDataBytes - 2);
        for ($i = 7; $i >= 0; $i--) {
            $bits[] = ($charCount >> $i) & 1;
        }

        // Data bytes
        for ($i = 0; $i < $charCount; $i++) {
            $b = $rawBytes[$i];
            for ($k = 7; $k >= 0; $k--) {
                $bits[] = ($b >> $k) & 1;
            }
        }

        // Terminator bits (up to 4 zeroes)
        $bitsCount = count($bits);
        $maxBits = $maxDataBytes * 8;
        $termCount = min(4, $maxBits - $bitsCount);
        for ($i = 0; $i < $termCount; $i++) {
            $bits[] = 0;
        }

        // Pad to nearest byte boundary
        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        // Convert bits to byte codewords
        $codewords = [];
        for ($i = 0; $i < count($bits); $i += 8) {
            $byteVal = 0;
            for ($k = 0; $k < 8; $k++) {
                $byteVal = ($byteVal << 1) | $bits[$i + $k];
            }
            $codewords[] = $byteVal;
        }

        // Pad codewords up to maxDataBytes
        $padBytes = [0xEC, 0x11];
        $padIdx = 0;
        while (count($codewords) < $maxDataBytes) {
            $codewords[] = $padBytes[$padIdx % 2];
            $padIdx++;
        }

        // Error correction codewords
        $ecCodewords = self::rsEncode($codewords, $ecBytes);

        // Final sequence of codewords
        $finalCodewords = array_merge($codewords, $ecCodewords);
        $finalBits = [];
        foreach ($finalCodewords as $cw) {
            for ($k = 7; $k >= 0; $k--) {
                $finalBits[] = ($cw >> $k) & 1;
            }
        }

        // Initialize matrix and reserved flags
        $matrix = array_fill(0, $size, array_fill(0, $size, null));
        $reserved = array_fill(0, $size, array_fill(0, $size, false));

        // 1. Finder patterns (top-left, top-right, bottom-left)
        $finders = [
            [0, 0],
            [0, $size - 7],
            [$size - 7, 0],
        ];

        foreach ($finders as [$r0, $c0]) {
            for ($r = 0; $r < 7; $r++) {
                for ($c = 0; $c < 7; $c++) {
                    $isBlack = ($r === 0 || $r === 6 || $c === 0 || $c === 6 || ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4));
                    $matrix[$r0 + $r][$c0 + $c] = $isBlack;
                    $reserved[$r0 + $r][$c0 + $c] = true;
                }
            }
            // Separators
            for ($r = -1; $r <= 7; $r++) {
                for ($c = -1; $c <= 7; $c++) {
                    $row = $r0 + $r;
                    $col = $c0 + $c;
                    if ($row >= 0 && $row < $size && $col >= 0 && $col < $size) {
                        if (!isset($reserved[$row][$col]) || !$reserved[$row][$col]) {
                            $matrix[$row][$col] = false;
                            $reserved[$row][$col] = true;
                        }
                    }
                }
            }
        }

        // 2. Alignment patterns (if any)
        if (!empty($alignPos)) {
            foreach ($alignPos as $ar) {
                foreach ($alignPos as $ac) {
                    if ($reserved[$ar][$ac]) {
                        continue;
                    }
                    for ($r = -2; $r <= 2; $r++) {
                        for ($c = -2; $c <= 2; $c++) {
                            $isBlack = ($r === -2 || $r === 2 || $c === -2 || $c === 2 || ($r === 0 && $c === 0));
                            $matrix[$ar + $r][$ac + $c] = $isBlack;
                            $reserved[$ar + $r][$ac + $c] = true;
                        }
                    }
                }
            }
        }

        // 3. Timing patterns
        for ($i = 8; $i < $size - 8; $i++) {
            if (!$reserved[6][$i]) {
                $matrix[6][$i] = ($i % 2 === 0);
                $reserved[6][$i] = true;
            }
            if (!$reserved[$i][6]) {
                $matrix[$i][6] = ($i % 2 === 0);
                $reserved[$i][6] = true;
            }
        }

        // 4. Dark module
        $matrix[$size - 8][8] = true;
        $reserved[$size - 8][8] = true;

        // 5. Reserve format information areas
        for ($i = 0; $i < 9; $i++) {
            if ($i !== 6) {
                $reserved[8][$i] = true;
                $reserved[$i][8] = true;
            }
        }
        for ($i = 0; $i < 8; $i++) {
            $reserved[8][$size - 1 - $i] = true;
            $reserved[$size - 1 - $i][8] = true;
        }

        // 6. Place data bits (Mask 0: (r + c) % 2 === 0)
        $bitIdx = 0;
        $totalBits = count($finalBits);
        $col = $size - 1;
        $goingUp = true;

        while ($col > 0) {
            if ($col === 6) {
                $col--;
            }
            $rowRange = $goingUp ? range($size - 1, 0) : range(0, $size - 1);
            foreach ($rowRange as $r) {
                for ($cOffset = 0; $cOffset <= 1; $cOffset++) {
                    $c = $col - $cOffset;
                    if (!$reserved[$r][$c]) {
                        $bit = ($bitIdx < $totalBits) ? $finalBits[$bitIdx] : 0;
                        $bitIdx++;
                        // Apply Mask 0: flip if (r + c) % 2 === 0
                        $isMasked = (($r + $c) % 2 === 0);
                        $matrix[$r][$c] = $isMasked ? ($bit === 0) : ($bit === 1);
                    }
                }
            }
            $col -= 2;
            $goingUp = !$goingUp;
        }

        // 7. Format Information (Mask 0, ECC Level L => Format 0x77C4 ^ 0x5412 = 0x23D6)
        // Standard format bits for L-0: 1 1 1 0 1 1 1 1 1 0 0 0 1 0 0 (15 bits)
        $formatBits = [1, 1, 1, 0, 1, 1, 1, 1, 1, 0, 0, 0, 1, 0, 0];

        // Format around top-left
        $coordsTopLeft = [
            [8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5], [8, 7], [8, 8],
            [7, 8], [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8]
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $coordsTopLeft[$i];
            $matrix[$r][$c] = ($formatBits[$i] === 1);
        }

        // Format around bottom-left & top-right
        $coordsSplit = [
            [$size - 1, 8], [$size - 2, 8], [$size - 3, 8], [$size - 4, 8],
            [$size - 5, 8], [$size - 6, 8], [$size - 7, 8],
            [8, $size - 8], [8, $size - 7], [8, $size - 6], [8, $size - 5],
            [8, $size - 4], [8, $size - 3], [8, $size - 2], [8, $size - 1]
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $coordsSplit[$i];
            $matrix[$r][$c] = ($formatBits[$i] === 1);
        }

        return $matrix;
    }

    /**
     * Generate inline SVG string
     */
    public static function generateSvg(string $text, int $size = 180, int $quietZone = 4, string $color = '#000000', string $bgColor = '#ffffff'): string
    {
        $matrix = self::createMatrix($text);
        $matrixSize = count($matrix);
        $totalSize = $matrixSize + ($quietZone * 2);

        $rects = [];
        for ($r = 0; $r < $matrixSize; $r++) {
            for ($c = 0; $c < $matrixSize; $c++) {
                if (!empty($matrix[$r][$c])) {
                    $x = $c + $quietZone;
                    $y = $r + $quietZone;
                    $rects[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"1\" height=\"1\" fill=\"{$color}\" />";
                }
            }
        }

        $rectsContent = implode('', $rects);
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$totalSize} {$totalSize}\" width=\"{$size}\" height=\"{$size}\" shape-rendering=\"crispEdges\"><rect width=\"100%\" height=\"100%\" fill=\"{$bgColor}\"/>{$rectsContent}</svg>";
    }

    /**
     * Generate base64 SVG data URI for use in <img> tags and CSS backgrounds
     */
    public static function generateSvgDataUri(string $text, int $size = 180, int $quietZone = 4, string $color = '#000000', string $bgColor = '#ffffff'): string
    {
        $svg = self::generateSvg($text, $size, $quietZone, $color, $bgColor);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate PNG Data URI using PHP GD (optimal for DomPDF embedded rendering)
     */
    public static function generatePngDataUri(string $text, int $moduleSize = 6, int $quietZone = 4, string $hexColor = '#000000'): string
    {
        $matrix = self::createMatrix($text);
        $matrixSize = count($matrix);
        $totalModules = $matrixSize + ($quietZone * 2);
        $imgSize = $totalModules * $moduleSize;

        $im = imagecreatetruecolor($imgSize, $imgSize);
        $bg = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, $imgSize, $imgSize, $bg);

        // Parse hex color
        $hex = ltrim($hexColor, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $fg = imagecolorallocate($im, $r, $g, $b);

        for ($row = 0; $row < $matrixSize; $row++) {
            for ($col = 0; $col < $matrixSize; $col++) {
                if (!empty($matrix[$row][$col])) {
                    $x1 = ($col + $quietZone) * $moduleSize;
                    $y1 = ($row + $quietZone) * $moduleSize;
                    $x2 = $x1 + $moduleSize - 1;
                    $y2 = $y1 + $moduleSize - 1;
                    imagefilledrectangle($im, $x1, $y1, $x2, $y2, $fg);
                }
            }
        }

        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);

        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}
