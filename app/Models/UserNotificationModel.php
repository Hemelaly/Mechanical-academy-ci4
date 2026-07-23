<?php

namespace App\Models;

use CodeIgniter\Model;

class UserNotificationModel extends Model
{
    protected $table            = 'user_notifications';
    protected $primaryKey       = 'id_notification';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'type_notification',
        'title_notification',
        'body_notification',
        'link_notification',
        'context_notification',
        'read_at',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps          = false;

    public function createForUser(
        int $userId,
        string $type,
        string $title,
        ?string $body = null,
        ?string $link = null,
        array $context = []
    ): ?int {
        if ($userId <= 0 || $type === '' || $title === '') {
            return null;
        }

        $paymentId = (int) ($context['payment_id'] ?? 0);
        if ($paymentId > 0 && $this->existsForPayment($userId, $type, $paymentId)) {
            return null;
        }

        $json = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $json = '{}';
        }

        $id = $this->insert([
            'user_id'              => $userId,
            'type_notification'    => $type,
            'title_notification'   => mb_substr($title, 0, 180),
            'body_notification'    => $body !== null ? mb_substr($body, 0, 500) : null,
            'link_notification'    => $link !== null ? mb_substr($link, 0, 255) : null,
            'context_notification' => $json,
            'read_at'              => null,
            'created_at'           => date('Y-m-d H:i:s'),
        ], true);

        return $id === false ? null : (int) $id;
    }

    public function existsForPayment(int $userId, string $type, int $paymentId): bool
    {
        if ($userId <= 0 || $paymentId <= 0) {
            return false;
        }

        $needle = '"payment_id":' . $paymentId;
        $row = $this->where('user_id', $userId)
            ->where('type_notification', $type)
            ->like('context_notification', $needle)
            ->first();

        return $row !== null;
    }

    public function unreadCount(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }

        return (int) $this->where('user_id', $userId)
            ->where('read_at', null)
            ->countAllResults();
    }

    public function markAllRead(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        $this->where('user_id', $userId)
            ->where('read_at', null)
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    public function markRead(int $userId, int $notificationId): bool
    {
        if ($userId <= 0 || $notificationId <= 0) {
            return false;
        }

        return (bool) $this->where('user_id', $userId)
            ->where('id_notification', $notificationId)
            ->where('read_at', null)
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
