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
        // Sample data - in a real app, this would come from the database
        $fakeChargePoints = [
            new ChargePoint(
                1,
                201,
                "Building 123, Road 2803, Block 428, Seef",
                "428",
                26.2336,
                50.5860,
                0.25,
                "Fast charger near Seef Mall.",
                true,
                "2024-06-01 10:00:00",
                "2024-06-01 10:00:00"
            ),
            new ChargePoint(
                2,
                202,
                "Building 456, Road 1705, Block 317, Diplomatic Area",
                "317",
                26.2361,
                50.5831,
                0.30,
                "Covered parking spot with charger in Diplomatic Area.",
                false,
                "2024-06-02 11:30:00",
                "2024-06-02 11:30:00"
            ),
            new ChargePoint(
                3,
                203,
                "Building 789, Road 3931, Block 939, Riffa",
                "939",
                26.1276,
                50.5610,
                0.20,
                null,
                true,
                "2024-06-03 09:15:00",
                "2024-06-03 09:15:00"
            ),
        ];

        // Apply filters
        $filteredChargers = array_filter($fakeChargePoints, function ($charger) use ($text, $min_price, $max_price, $availability) {
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
