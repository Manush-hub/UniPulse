<?php

class Donation
{
    use Model;

    protected $table = 'donations';
    protected $allowedColumns = [
        'user_id',
        'user_type',
        'event_id',
        'amount',
        'currency',
        'payment_method',
        'payment_id',
        'transaction_reference',
        'status',
        'donor_name',
        'donor_email',
        'donor_phone',
        'is_anonymous',
        'message',
        'receipt_sent'
    ];

    /**
     * Get all donations by a user
     */
    public function getUserDonations($userId, $userType, $status = null)
    {
        $sql = "SELECT d.*, e.title as event_title, e.event_date, e.image_url as event_image
                FROM {$this->table} d
                LEFT JOIN events e ON d.event_id = e.id
                WHERE d.user_id = :user_id 
                AND d.user_type = :user_type";

        if ($status) {
            $sql .= " AND d.status = :status";
        }

        $sql .= " ORDER BY d.created_at DESC";

        $params = [
            'user_id' => $userId,
            'user_type' => $userType
        ];

        if ($status) {
            $params['status'] = $status;
        }

        return $this->query($sql, $params);
    }

    /**
     * Get user's monthly donations
     * @param int $userId User ID
     * @param string $userType User type (public/university)
     * @param string $month Month in format 'YYYY-MM'
     * @return array Array of donation records
     */
    public function getUserMonthlyDonations($userId, $userType, $month)
    {
        $sql = "SELECT d.*, e.title as event_title, e.event_date, e.location,
                       e.image_url as event_image, e.university_name
                FROM {$this->table} d
                LEFT JOIN events e ON d.event_id = e.id
                WHERE d.user_id = :user_id 
                AND d.user_type = :user_type
                AND d.status IN ('accepted', 'completed')
                AND DATE_FORMAT(d.created_at, '%Y-%m') = :month
                ORDER BY d.created_at DESC";

        return $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'month' => $month
        ]);
    }

    /**
     * Get total donations by user for a specific month
     */
    public function getUserMonthlyDonationTotal($userId, $userType, $month)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total
                FROM {$this->table}
                WHERE user_id = :user_id 
                AND user_type = :user_type
                AND status IN ('accepted', 'completed')
                AND DATE_FORMAT(created_at, '%Y-%m') = :month";

        $result = $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType,
            'month' => $month
        ]);

        return $result[0]->total ?? 0;
    }

    /**
     * Get total donations by user (all time)
     */
    public function getUserTotalDonations($userId, $userType)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total,
                       COUNT(*) as count
                FROM {$this->table}
                WHERE user_id = :user_id 
                AND user_type = :user_type
                AND status IN ('accepted', 'completed')";

        $result = $this->query($sql, [
            'user_id' => $userId,
            'user_type' => $userType
        ]);

        return $result[0] ?? (object)['total' => 0, 'count' => 0];
    }

    /**
     * Create a new donation
     */
    public function createDonation($data)
    {
        return $this->insert($data);
    }

    /**
     * Update donation status
     */
    public function updateStatus($donationId, $status)
    {
        $sql = "UPDATE {$this->table} 
                SET status = :status, updated_at = NOW()
                WHERE id = :id";

        return $this->query($sql, [
            'id' => $donationId,
            'status' => $status
        ]);
    }
}
