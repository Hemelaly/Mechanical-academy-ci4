<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalyticsEventModel extends Model
{
    protected $table            = 'analytics_events';
    protected $primaryKey       = 'id_analytics';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'event_type',
        'path',
        'route_label',
        'referrer',
        'element',
        'persona',
        'user_id',
        'visitor_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device',
        'meta_json',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps          = false;
}
