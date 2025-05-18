<?php


class Message
{
    public int $message_id;
    public int $sender_id;
    public int $recipient_id;
    public string $subject;
    public string $message;
    public string $created_at;
    public bool $is_read;

    public function __construct(
        int $message_id,
        int $sender_id,
        int $recipient_id,
        string $subject,
        string $message,
        string $created_at,
        bool $is_read
    ) {
        $this->message_id = $message_id;
        $this->sender_id = $sender_id;
        $this->recipient_id = $recipient_id;
        $this->subject = $subject;
        $this->message = $message;
        $this->created_at = $created_at;
        $this->is_read = $is_read;
    }
    private static function fromRow(array $row): Message
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
    public static function getAllForHomeowner(int $homeowner_id): array
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM messages WHERE recipient_id = ?");
        $stmt->execute([$homeowner_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([self::class, 'fromRow'], $rows);
    }

    public static function addMessage(
        int $sender_id,
        int $recipient_id,
        string $subject,
        string $message
    ): void {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, subject, message, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->execute([$sender_id, $recipient_id, $subject, $message]);
    }
    public static function markAsRead(int $message_id): void
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ?");
        $stmt->execute([$message_id]);
    }
}
