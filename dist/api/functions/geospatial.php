<?php

class Geospatial
{
    public static function extractCoordinate($coordinateString)
    {
        // Split the string into parts using the comma delimiter
        $parts = explode(',', $coordinateString);

        // Initialize variables to store latitude and longitude
        $latitude = null;
        $longitude = null;

        // Loop through the parts and check if they resemble coordinates
        foreach ($parts as $part) {
            // Remove leading and trailing whitespace
            $part = trim($part);

            // Check if it resembles latitude (between -90 and 90)
            if (preg_match('/^-?(\d{1,2}(\.\d+)?|\d{1,2}\.\d+)$/', $part)) {
                if ($latitude === null) {
                    $latitude = (float) $part;
                }
            }
            // Check if it resembles longitude (between -180 and 180)
            elseif (preg_match('/^-?(\d{1,3}(\.\d+)?|\d{1,3}\.\d+)$/', $part)) {
                if ($longitude === null) {
                    $longitude = (float) $part;
                }
            }

            // If both latitude and longitude are found, exit the loop
            if ($latitude !== null && $longitude !== null) {
                break;
            }
        }

        // Return latitude and longitude as an associative array
        return [
            'lat' => $latitude,
            'lng' => $longitude,
        ];
    }

    // extract subkey value from array
    public static function extractArraySubkeyValues($array, $subkey)
    {
        $result = array();

        foreach ($array as $item) {
            if (isset($item[$subkey])) {
                $result[] = $item[$subkey];
            }
        }

        return $result;
    }

    // get average of set of number
    public static function calculateAverage($numbers)
    {
        // Convert all values to integers
        $intNumbers = array_map(function ($value) {
            return is_numeric($value) ? intval($value) : 0;
        }, $numbers);

        // Calculate the sum of the numbers
        $sum = array_sum($intNumbers);

        // Calculate the average
        $count = count($intNumbers);
        if ($count > 0) {
            $average = round($sum / $count);
            return $average;
        } else {
            return 0; // Avoid division by zero error
        }
    }

}
