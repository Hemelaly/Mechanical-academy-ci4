<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id_audit_log';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_audit_log',
        'level_audit_log',
        'message_audit_log',
        'actor_user_id',
        'method_audit_log',
        'uri_audit_log',
        'ip_address_audit_log',
        'user_agent_audit_log',
        'context_audit_log',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps          = false;
}
