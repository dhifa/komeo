<?php

namespace App\Services;

class AuditLogService
{
    /**
     * Log an action to audit_logs
     *
     * @param string $action
     * @param string|null $entityType
     * @param int|null $entityId
     * @param array|null $details
     * @param int|null $userId
     * @return bool
     */
    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $details = null,
        ?int $userId = null
    ): bool {
        try {
            $db = \Config\Database::connect();
            $request = service('request');

            $uid = $userId;
            if ($uid === null && function_exists('auth') && auth()->loggedIn()) {
                $uid = (int) auth()->id();
            }

            $ip = null;
            $userAgent = null;
            if ($request && method_exists($request, 'getIPAddress')) {
                $ip = $request->getIPAddress();
                $userAgent = (string) $request->getUserAgent();
            }

            $db->table('audit_logs')->insert([
                'user_id'     => $uid,
                'action'      => $action,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'ip_address'  => $ip,
                'user_agent'  => $userAgent,
                'details'     => $details ? json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'AuditLogService error: ' . $e->getMessage());
            return false;
        }
    }
}
