<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Mailboxes oficiais da Mechanical Academy.
 */
class Academy extends BaseConfig
{
    /**
     * Email de contacto / Reply-To
     */
    public string $contactEmail = 'academy@mechanical.co.mz';

    /**
     * Senha do mailbox de contacto (uso administrativo / IMAP)
     */
    public string $contactPass = '';
}
