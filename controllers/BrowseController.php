<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ChargePoint.php';

class BrowseController extends BaseController
{
    public function index()
    {
        $title = "Browse Chargers";
        ob_start();
        include __DIR__ . '/../views/browse.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }

    /**
     * Search for chargers based on the given text and writes the results to the output as JSON.
     * 
     * @param string $text The search text.
     */
    public function search(string $text)
    {
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

        header('Content-Type: application/json');
        echo json_encode($fakeChargePoints);
    }
}
