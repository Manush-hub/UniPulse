<?php

/**
 * AdminActivity - Logs administrative actions performed by admins
 * (moderator management, admin creation, etc.)
 */
class AdminActivity
{
    use Database;

    protected $table = 'admin_activities';

    /**
     * Log an admin action
     *
     * @param int    $adminId    ID of the admin performing the action
     * @param string $adminName  Full name of the admin
     * @param string $actionType e.g. moderator_created, moderator_edited, moderator_deleted, admin_created
     * @param string $targetType e.g. moderator, admin
     * @param int    $targetId   ID of the entity acted upon
     * @param string $targetName Name of the entity acted upon
     * @param string $description Human-readable description
     * @param string $icon       Font Awesome icon name (without fa- prefix)
     */
    public static function log(
        int    $adminId,
        string $adminName,
        string $actionType,
        string $targetType,
        ?int   $targetId,
        ?string $targetName,
        string $description,
        string $icon = 'shield-alt'
    ): bool {
        try {
            $instance = new self();
            $conn = $instance->connect();
            $stmt = $conn->prepare(
                "INSERT INTO admin_activities 
                 (admin_id, admin_name, action_type, target_type, target_id, target_name, description, icon)
                 VALUES (:admin_id, :admin_name, :action_type, :target_type, :target_id, :target_name, :description, :icon)"
            );
            return $stmt->execute([
                'admin_id'    => $adminId,
                'admin_name'  => $adminName,
                'action_type' => $actionType,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'target_name' => $targetName,
                'description' => $description,
                'icon'        => $icon,
            ]);
        } catch (Exception $e) {
            error_log('AdminActivity::log error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch the most recent admin activity entries
     *
     * @param int $limit
     * @return array
     */
    public static function getRecent(int $limit = 20): array
    {
        try {
            $instance = new self();
            $conn = $instance->connect();
            $stmt = $conn->prepare(
                "SELECT * FROM admin_activities ORDER BY created_at DESC LIMIT :limit"
            );
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ) ?: [];
        } catch (Exception $e) {
            error_log('AdminActivity::getRecent error: ' . $e->getMessage());
            return [];
        }
    }
}
