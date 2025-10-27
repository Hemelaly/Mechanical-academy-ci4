<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class ExtendedUserModel extends ShieldUserModel
{
    protected $allowedFields = [
        // Campos padrão do Shield (mantenha-os)
        'username', 'email', 'password_hash', 'reset_hash', 'reset_at', 'reset_expires',
        'activate_hash', 'status', 'status_message', 'active', 'force_pass_reset',
        'created_at', 'updated_at', 'deleted_at', 'last_active', 'last_login',

        // Seus campos extras:
        'img', 'country', 'province', 'city', 'phone',
    ];
}
