<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model de log de acesso
 * 
 * @package App\Models
 */
class UserAccessLog extends Model
{
    protected string $table = 'user_access_logs';
    
    /**
     * Registra log de acesso
     * 
     * @param int|null $userId
     * @param int $eventTypeId
     * @param bool $success
     * @param string|null $details
     * @return int|false
     */
    public function log(?int $userId, int $eventTypeId, bool $success, ?string $details = null): int|false
    {
        return $this->create([
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'event_type_id' => $eventTypeId,
            'success' => $success ? 1 : 0,
            'details' => $details
        ]);
    }
}
