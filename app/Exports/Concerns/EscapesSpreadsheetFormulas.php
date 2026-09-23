<?php

namespace App\Exports\Concerns;

/**
 * PhpSpreadsheet treats any cell value starting with =, +, -, or @ as a
 * formula. Guest-supplied text (name, email, notes) comes from the public
 * booking form and must never reach a cell unescaped, or a booking like
 * `=HYPERLINK(...)` becomes a live formula when an admin opens the export.
 */
trait EscapesSpreadsheetFormulas
{
    private static function escapeFormula(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) === 1 ? "'" . $value : $value;
    }
}
