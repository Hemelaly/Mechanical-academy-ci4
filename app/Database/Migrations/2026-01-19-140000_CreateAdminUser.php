<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Config\Database;

class CreateAdminUser extends Migration
{
    public function up()
    {
        $db = Database::connect();
        $tables = config('Auth')->tables;

        $email = 'admin@admin.co.mz';
        $username = 'Admin';
        $password = 'Admin123';

        $identityTable = $tables['identities'];
        $userTable = $tables['users'];

        $existing = $db->table($identityTable)
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRow();

        if ($existing) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $userData = [
            'username'   => $username,
            'active'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($db->fieldExists('role', $userTable)) {
            $userData['role'] = 'admin';
        }

        $db->table($userTable)->insert($userData);
        $userId = $db->insertID();

        $db->table($identityTable)->insert([
            'user_id'    => $userId,
            'type'       => 'email_password',
            'secret'     => $email,
            'secret2'    => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        $db = Database::connect();
        $tables = config('Auth')->tables;

        $email = 'admin@admin.co.mz';

        $identityTable = $tables['identities'];
        $userTable = $tables['users'];

        $identity = $db->table($identityTable)
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRow();

        if ($identity) {
            $db->table($identityTable)->where('id', $identity->id)->delete();
            $db->table($userTable)->where('id', $identity->user_id)->delete();
        }
    }
}
