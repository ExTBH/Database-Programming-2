<?php
include_once 'BaseController.php';
include_once __DIR__ . '/../models/Feature.php';

class HomeController extends BaseController
{
    public function index()
    {

        $featuresList = [
            new Feature("fas fa-truck", "Fast delivery", "Get gear delivered right to your door, or pick up from a nearby location."),
            new Feature("fas fa-camera", "Professional grade", "Rent high-quality equipment from top brands and local experts."),
            new Feature("fas fa-music", "Live support", "Need help? Our team is here 24/7 to answer questions and provide support."),
            new Feature("fas fa-wrench", "Flexible rental", "Choose the rental duration that works best for you—daily, weekly, or monthly."),
            new Feature("fas fa-paint-brush", "Curated gear", "Discover unique items curated by our community of experts, artists, and adventurers."),
            new Feature("fas fa-desktop", "Easy tracking", "Easily track your orders and manage your rentals on our mobile app or website."),
            new Feature("fas fa-campground", "Adventure ready", "Rent gear that's built to handle everything from outdoor adventures to professional gigs.")
        ];

        $title = "Home";
        ob_start();
        include __DIR__ . '/../views/home.phtml';
        $content = ob_get_clean();
        include __DIR__ . '/../views/_layout.php';
    }
}
