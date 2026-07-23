<?php

namespace Config;

use App\Libraries\AuditLogger;
use App\Libraries\JitsiJwtService;
use CodeIgniter\Config\BaseService;
use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    public static function auditLogger(bool $getShared = true): AuditLogger
    {
        if ($getShared) {
            return static::getSharedInstance('auditLogger');
        }

        return new AuditLogger();
    }

    public static function jitsiJwt(bool $getShared = true): JitsiJwtService
    {
        if ($getShared) {
            return static::getSharedInstance('jitsiJwt');
        }

        return new JitsiJwtService();
    }

    /**
     * Email com From (no-reply) e Reply-To (academy@) oficiais.
     *
     * @param array|EmailConfig|null $config
     */
    public static function email($config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('email', $config);
        }

        if (empty($config) || (! is_array($config) && ! $config instanceof EmailConfig)) {
            $config = config(EmailConfig::class);
        }

        $email = new Email($config);

        $replyTo = is_object($config)
            ? (string) ($config->replyToEmail ?? '')
            : (string) ($config['replyToEmail'] ?? '');
        $replyName = is_object($config)
            ? (string) ($config->replyToName ?? '')
            : (string) ($config['replyToName'] ?? '');

        if ($replyTo !== '') {
            $email->setReplyTo($replyTo, $replyName);
        }

        return $email;
    }
}

