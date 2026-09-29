<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use DOMDocument;

class ValidSvg implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty values (since the field is optional)
        }

        // Suppress XML warnings to prevent log pollution
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $loaded = $dom->loadXML($value);

        libxml_clear_errors();

        // Check if it's valid XML and the root tag is 'svg'
        if (!$loaded || strtolower($dom->documentElement->nodeName) !== 'svg') {
            $fail('The :attribute must be valid SVG XML code starting with an <svg> tag.');
        }
    }
}
