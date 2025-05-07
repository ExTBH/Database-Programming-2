<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ChargePoint.php';

class BrowseController extends BaseController
{
    public function index()
    {
        $this->render('browse', [
            'title' => 'Browse Chargers'
        ]);
    }

    /**
     * Search for chargers based on given criteria and write the results to the output as JSON.
     * 
     * @param string $text The search text for location (address, postcode, or coordinates)
     * @param float|null $min_price Minimum price per kWh
     * @param float|null $max_price Maximum price per kWh
     * @param string|null $availability Filter by availability ('true'/'false'/null)
     */
    public function search(
        string $text = '',
        ?float $min_price = null,
        ?float $max_price = null,
        ?string $availability = null
    ) {


        $chargePoints = ChargePoint::getAll();

        // Apply filters
        $filteredChargers = array_filter($chargePoints, function ($charger) use ($text, $min_price, $max_price, $availability) {
            // Location search
            if ($text) {
                $searchText = strtolower($text);
                $address = strtolower($charger->address);
                $postcode = strtolower($charger->postcode);
                // Use sprintf to avoid rounding and preserve precision
                $coordinates = strtolower($charger->latitude . ',' . $charger->longitude);

                if (
                    !str_contains($address, $searchText) &&
                    !str_contains($postcode, $searchText) &&
                    !str_contains($coordinates, $searchText)
                ) {
                    return false;
                }
            }

            // Price range filter
            if ($min_price !== null && $charger->price_per_kwh < $min_price) {
                return false;
            }
            if ($max_price !== null && $charger->price_per_kwh > $max_price) {
                return false;
            }

            // Availability filter
            if ($availability !== null && $availability !== '') {
                $isAvailable = $availability === 'true';
                if ($charger->is_available !== $isAvailable) {
                    return false;
                }
            }

            return true;
        });

        header('Content-Type: application/json');
        echo json_encode(array_values($filteredChargers));
    }
}
