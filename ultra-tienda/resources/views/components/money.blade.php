@props(['value', 'suffix' => ''])
{{ '$' . number_format((float) $value, 2, ',', '.') . $suffix }}