<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/User.php';

class ContactController extends BaseController
{
    public function index()
    {
        $this->render('user/contact', [
            'title' => 'Contact a Homeowner',
        ]);
    }

    public function sendMessage($data)
    {
        // Validate required fields
        if (empty($data['homeowner_id']) || empty($data['subject']) || empty($data['message'])) {

            echo json_encode([
                'error' => 'All fields are required.',
            ]);
            exit;
        }

        // Get the sender ID from the session
        $sender_id = User::fromSession()->id;

        Message::addMessage(
            $sender_id,
            (int)$data['homeowner_id'],
            $data['subject'],
            $data['message']
        );

        echo json_encode([
            'success' => 'Message sent successfully.',
        ]);
        exit;
    }
}
