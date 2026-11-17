<?php

namespace App\Helpers;

class BarcodeHelper
{
    /**
     * Generate pure SVG Code 128 barcode
     */
    public static function generateSvg(string $code, int $height = 50, int $scale = 2): string
    {
        $code = trim($code);
        if ($code === '') {
            $code = '00000000';
        }

        // Code 128 B patterns
        $patterns = [
            ' ' => '212222', '!' => '222122', '"' => '222221', '#' => '121223',
            '$' => '121322', '%' => '131222', '&' => '122213', '\'' => '122312',
            '(' => '132212', ')' => '221213', '*' => '221312', '+' => '231212',
            ',' => '112232', '-' => '122132', '.' => '122231', '/' => '113222',
            '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132',
            '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131',
            '8' => '311222', '9' => '321122', ':' => '321221', ';' => '312212',
            '<' => '322112', '=' => '322211', '>' => '212123', '?' => '212321',
            '@' => '232121', 'A' => '111323', 'B' => '131123', 'C' => '131321',
            'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313',
            'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331',
            'L' => '132131', 'M' => '113123', 'N' => '113321', 'O' => '133121',
            'P' => '313121', 'Q' => '211331', 'R' => '231131', 'S' => '213113',
            'T' => '213311', 'U' => '213131', 'V' => '311123', 'W' => '311321',
            'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111',
            '\\' => '314111', ']' => '221411', '^' => '431111', '_' => '111224',
            '`' => '111422', 'a' => '121124', 'b' => '121421', 'c' => '141122',
            'd' => '141221', 'e' => '112214', 'f' => '112412', 'g' => '122114',
            'h' => '122411', 'i' => '142112', 'j' => '142211', 'k' => '241211',
            'l' => '221114', 'm' => '413111', 'n' => '241112', 'o' => '134111',
            'p' => '111242', 'q' => '121142', 'r' => '121241', 's' => '114212',
            't' => '124112', 'u' => '124211', 'v' => '411212', 'w' => '421112',
            'x' => '421211', 'y' => '212141', 'z' => '214121', '{' => '412121',
            '|' => '111143', '}' => '111341', '~' => '131141',
        ];

        // Start B pattern & Stop pattern
        $startPattern = '211214';
        $stopPattern = '2331112';

        $rawBars = $startPattern;
        $chars = str_split($code);
        $checkSum = 104; // Start Code B value

        foreach ($chars as $index => $char) {
            $val = ord($char) - 32;
            if ($val < 0 || $val > 95) {
                $char = '-';
                $val = ord('-') - 32;
            }
            $checkSum += $val * ($index + 1);
            $rawBars .= $patterns[$char] ?? '122132';
        }

        $checkVal = $checkSum % 103;
        $checkKeys = array_keys($patterns);
        $checkChar = $checkKeys[$checkVal] ?? '-';
        $rawBars .= $patterns[$checkChar] ?? '122132';
        $rawBars .= $stopPattern;

        // Build SVG rectangles
        $x = 10;
        $rects = '';
        $isBar = true;

        for ($i = 0; $i < strlen($rawBars); $i++) {
            $width = (int) $rawBars[$i] * $scale;
            if ($isBar) {
                $rects .= sprintf('<rect x="%d" y="5" width="%d" height="%d" fill="#000000" />', $x, $width, $height);
            }
            $x += $width;
            $isBar = ! $isBar;
        }

        $totalWidth = $x + 10;
        $totalHeight = $height + 25;

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">',
            $totalWidth, $totalHeight, $totalWidth, $totalHeight
        );
        $svg .= '<rect width="100%" height="100%" fill="#ffffff" />';
        $svg .= $rects;
        $svg .= sprintf(
            '<text x="%d" y="%d" text-anchor="middle" font-family="monospace" font-size="12" font-weight="bold" fill="#000000">%s</text>',
            $totalWidth / 2, $totalHeight - 5, htmlspecialchars($code)
        );
        $svg .= '</svg>';

        return $svg;
    }
}
