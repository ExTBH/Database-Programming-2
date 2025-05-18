<?php

class Message
{
    /** @var int */
    public $message_id;

    /** @var int */
    public $sender_id;

    /** @var int */
    public $recipient_id;

    /** @var string */
    public $subject;

    /** @var string */
    public $message;

    /** @var string */
    public $created_at;

    /** @var bool */
    public $is_read;

    /**
     * @param int $message_id
     * @param int $sender_id
     * @param int $recipient_id
     * @param string $subject
     * @param string $message
     * @param string $created_at
     * @param bool $is_read
     */
    public function __construct(
        $message_id,
        $sender_id,
        $recipient_id,
        $subject,
        $message,
        $created_at,
        $is_read
    ) {
        $this->message_id = $message_id;
        $this->sender_id = $sender_id;
        $this->recipient_id = $recipient_id;
        $this->subject = $subject;
        $this->message = $message;
        $this->created_at = $created_at;
        $this->is_read = $is_read;
    }

    /**
     * @param array $row
     * @return Message
     */
    private static function fromRow($row)
    {
        return new Message(
            (int)$row['message_id'],
            (int)$row['sender_id'],
            (int)$row['recipient_id'],
            $row['subject'],
            $row['message'],
            $row['created_at'],
            (bool)$row['is_read']
        );
    }

    /**
     * @param int $homeowner_id
     * @return Message[]
     */
    public static function getAllForHomeowner($homeowner_id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM messages WHERE recipient_id = ?");
        $stmt->execute([$homeowner_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([self::class, 'fromRow'], $rows);
    }

    /**
     * @param int $sender_id
     * @param int $recipient_id
     * @param string $subject
     * @param string $message
     * @return void
     */
    public static function addMessage(
        $sender_id,
        $recipient_id,
        $subject,
        $message
    ) {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, subject, message, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->execute([$sender_id, $recipient_id, $subject, $message]);
    }

    /**
     * @param int $message_id
     * @return void
     */
    public static function markAsRead($message_id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ?");
        $stmt->execute([$message_id]);
    }
}
