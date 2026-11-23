<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Determine whether the user can view any audit logs.
     *
     * Authorization: Editor+ (editor, super_admin)
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    /**
     * Determine whether the user can view the audit log.
     *
     * Authorization: Editor+ (editor, super_admin)
     *
     * @param User $user
     * @param AuditLog $auditLog
     * @return bool
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    /**
     * Determine whether the user can restore an audit log.
     *
     * Authorization: Super Admin only
     *
     * Note: Non-restorable logs are handled as business validation
     * (422 error) in the controller, not authorization (403 error).
     * This policy only checks role.
     *
     * @param User $user
     * @param AuditLog $auditLog
     * @return bool
     */
    public function restore(User $user, AuditLog $auditLog): bool
    {
        // Only super admins can restore
        return (bool) $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can force delete an audit log.
     *
     * Authorization: Super Admin only (for admin management)
     *
     * @param User $user
     * @param AuditLog $auditLog
     * @return bool
     */
    public function forceDelete(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete an audit log.
     *
     * Authorization: Super Admin only (for admin management)
     *
     * @param User $user
     * @param AuditLog $auditLog
     * @return bool
     */
    public function delete(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole('super_admin');
    }
}
