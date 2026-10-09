<?php

namespace App\Services;

class AuditLogService
{
    /**
     * Log an action to audit_logs
     *
     * Supports both caller conventions safely:
     * 1. log(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null)
     * 2. log(string $action, ?string $entityType = null, ?int $entityId = null, ?array $details = null, ?int $userId = null)
     */
    public static function log(
        $arg1,
        $arg2 = null,
        $arg3 = null,
        $arg4 = null,
        $arg5 = null
    ): bool {
        try {
            $db = \Config\Database::connect();
            $request = service('request');

            // Detect signature:
            // Standard across KOMEO controllers: log($userId, $action, $entityType, $entityId, $details)
            if (is_string($arg1) && ! is_numeric($arg1) && (is_null($arg3) || is_int($arg3)) && (is_null($arg4) || is_array($arg4))) {
                // Signature: (action, entityType, entityId, details, userId)
                $action     = (string) $arg1;
                $entityType = $arg2 !== null ? (string) $arg2 : null;
                $entityId   = $arg3 !== null ? (int) $arg3 : null;
                $details    = is_array($arg4) ? $arg4 : null;
                $uid        = $arg5 !== null ? (int) $arg5 : null;
            } else {
                // Signature: (userId, action, entityType, entityId, details)
                $uid        = $arg1 !== null ? (int) $arg1 : null;
                $action     = (string) $arg2;
                $entityType = $arg3 !== null ? (string) $arg3 : null;
                $entityId   = $arg4 !== null ? (int) $arg4 : null;
                $details    = is_array($arg5) ? $arg5 : null;
            }

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
