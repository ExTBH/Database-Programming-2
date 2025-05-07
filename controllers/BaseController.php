<?php
abstract class BaseController
{
    // The index method is abstract and must be defined in any subclass
    // abstract public function index();

    protected function render(string $view, array $data = []): void
    {
        // Extract data to make variables available in view
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        require __DIR__ . '/../views/' . $view . '.phtml';

        // Get the view content
        $content = ob_get_clean();

        // Include the layout
        require __DIR__ . '/../views/_layout.php';
    }
}
