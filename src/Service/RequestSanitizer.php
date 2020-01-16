<?php declare(strict_types = 1);

namespace Kernolab\Service;

class RequestSanitizer
{
    /**
     * Filters the given array of data (POST or GET) and returns the sanitized array.
     *
     * @param array $data
     *
     * @return array
     */
    public function sanitize(array $data): array
    {
        $filteredData = [];
        
        foreach ($data as $key => $value) {
            $sanitizedValue     = filter_var(trim($value), FILTER_SANITIZE_STRING);
            $filteredData[$key] = $sanitizedValue;
        }
        
        return $filteredData;
    }
}