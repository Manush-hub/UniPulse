<?php

class Donations extends Controller
{
    use Database;

    /**
     * AJAX endpoint for publisher nav donation badge.
     */
    public function pendingCount($a = '', $b = '', $c = '')
    {
        header('Content-Type: application/json');

        try {
            $currentUser = AuthService::getCurrentUser();
            if (!$currentUser || ($currentUser['type'] ?? '') !== 'publisher') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized', 'count' => 0]);
                exit();
            }

            $count = $this->getPendingDonationCount((int)$currentUser['id']);
            echo json_encode(['success' => true, 'count' => $count]);
        } catch (Throwable $e) {
            error_log('Donations::pendingCount error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load donation count', 'count' => 0]);
        }

        exit();
    }

    /**
     * Show publisher's donation management page
     */
    public function index()
    {
        SessionMiddleware::requireAuth('publisher');
        $currentUser = AuthService::getCurrentUser();

        try {
            $sql = "SELECT 
                        d.*,
                        e.title as event_title,
                        e.event_date,
                        e.venue_name,
                        e.city,
                        e.university_name
                    FROM donations d
                    INNER JOIN events e ON d.event_id = e.id
                    WHERE e.created_by = ? AND e.created_by_type = 'publisher'
                    ORDER BY 
                        CASE d.status
                            WHEN 'pending' THEN 1
                            WHEN 'accepted' THEN 2
                            WHEN 'rejected' THEN 3
                            WHEN 'completed' THEN 4
                            WHEN 'failed' THEN 5
                            WHEN 'refunded' THEN 6
                            ELSE 5
                        END,
                        d.created_at DESC";

            $donations = $this->query($sql, [$currentUser['id']]);

            if (!$donations) {
                $donations = [];
            } else {
                $donations = array_map(function ($item) {
                    return (array) $item;
                }, $donations);
            }

            $grouped = [
                'pending' => [],
                'accepted' => [],
                'rejected' => []
            ];

            foreach ($donations as $donation) {
                $status = strtolower((string)($donation['status'] ?? 'pending'));
                if ($status === 'completed') {
                    $status = 'accepted';
                    $donation['status'] = 'accepted';
                } elseif ($status === 'failed' || $status === 'refunded') {
                    $status = 'rejected';
                    $donation['status'] = 'rejected';
                }

                if (isset($grouped[$status])) {
                    $grouped[$status][] = $donation;
                } elseif (isset($grouped[$donation['status']])) {
                    $grouped[$donation['status']][] = $donation;
                }
            }

            $this->view('Publisher/donations', [
                'donations' => $donations,
                'grouped' => $grouped,
                'stats' => [
                    'pending' => count($grouped['pending']),
                    'accepted' => count($grouped['accepted']),
                    'rejected' => count($grouped['rejected']),
                    'total' => count($donations)
                ]
            ]);
        } catch (Exception $e) {
            error_log('Donations::index error: ' . $e->getMessage());
            $this->view('Publisher/donations', [
                'donations' => [],
                'grouped' => [
                    'pending' => [],
                    'accepted' => [],
                    'rejected' => []
                ],
                'stats' => [
                    'pending' => 0,
                    'accepted' => 0,
                    'rejected' => 0,
                    'total' => 0
                ],
                'error' => 'Failed to load donations'
            ]);
        }
    }

    /**
     * Mark pending donation as accepted
     */
    public function accept()
    {
        $this->updateDonationStatus('accepted');
    }

    /**
     * Mark pending donation as rejected
     */
    public function reject()
    {
        $this->updateDonationStatus('rejected');
    }

    private function updateDonationStatus($newStatus)
    {
        header('Content-Type: application/json');

        SessionMiddleware::requireAuth('publisher');
        $currentUser = AuthService::getCurrentUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        try {
            $donationId = $_POST['donation_id'] ?? null;

            if (!$donationId || !is_numeric($donationId)) {
                throw new Exception('Valid donation ID is required');
            }

            $checkSql = "SELECT d.id, d.user_id, d.user_type, d.amount, d.currency, d.event_id,
                                                                e.title as event_title
                         FROM donations d
                         INNER JOIN events e ON d.event_id = e.id
                         WHERE d.id = ?
                           AND d.status = 'pending'
                           AND e.created_by = ?
                           AND e.created_by_type = 'publisher'";

            $donation = $this->query($checkSql, [(int)$donationId, (int)$currentUser['id']]);

            if (empty($donation)) {
                throw new Exception('Donation not found, not pending, or unauthorized');
            }

            $updateSql = "UPDATE donations
                          SET status = ?,
                              updated_at = NOW()
                          WHERE id = ?";

            $statusToPersist = $this->resolvePersistedStatusValue($newStatus);

            $conn = $this->connect();
            $stmt = $conn->prepare($updateSql);
            $result = $stmt->execute([$statusToPersist, (int)$donationId]);

            if (!$result) {
                throw new Exception('Failed to update donation status');
            }

            try {
                $activityModel = new Activity();
                $donationRow = $donation[0];
                $targetUserId = (int)($donationRow->user_id ?? 0);
                $targetUserType = (string)($donationRow->user_type ?? 'public');
                $eventId = (int)($donationRow->event_id ?? 0);
                $eventTitle = (string)($donationRow->event_title ?? 'Event');
                $amount = (float)($donationRow->amount ?? 0);
                $currency = (string)($donationRow->currency ?? 'LKR');

                if ($targetUserId > 0 && in_array($targetUserType, ['public', 'university'])) {
                    $isApproved = ($newStatus === 'accepted');
                    $activityModel->logActivity(
                        $targetUserId,
                        $targetUserType,
                        'event_registration',
                        $isApproved ? 'Donation Approved' : 'Donation Rejected',
                        $isApproved
                            ? 'Your donation for "' . $eventTitle . '" was approved by the publisher.'
                            : 'Your donation for "' . $eventTitle . '" was rejected by the publisher.',
                        $isApproved ? 'award' : 'bell',
                        $eventId,
                        $eventTitle,
                        [
                            'notification_category' => 'donation_status',
                            'donation_status' => $newStatus,
                            'amount' => $amount,
                            'currency' => $currency
                        ]
                    );
                }
            } catch (Throwable $activityError) {
                error_log('Donations::updateDonationStatus activity log warning: ' . $activityError->getMessage());
            }

            echo json_encode([
                'success' => true,
                'message' => $newStatus === 'accepted'
                    ? 'Donation marked as accepted'
                    : 'Donation marked as rejected'
            ]);
        } catch (Exception $e) {
            error_log('Donations::updateDonationStatus error: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    private function resolvePersistedStatusValue($requestedStatus)
    {
        $requested = strtolower((string)$requestedStatus);
        $allowed = $this->getDonationStatusEnumValues();

        if (empty($allowed)) {
            return $requested;
        }

        if (in_array($requested, $allowed, true)) {
            return $requested;
        }

        $fallbackMap = [
            'accepted' => ['completed'],
            'rejected' => ['failed', 'refunded'],
            'pending' => ['pending']
        ];

        foreach ($fallbackMap[$requested] ?? [] as $candidate) {
            if (in_array($candidate, $allowed, true)) {
                return $candidate;
            }
        }

        return $requested;
    }

    private function getDonationStatusEnumValues()
    {
        try {
            $columns = $this->query("SHOW COLUMNS FROM donations LIKE 'status'");
            if (empty($columns)) {
                return [];
            }

            $column = $columns[0];
            $type = '';

            if (is_object($column)) {
                $type = (string)($column->Type ?? '');
            } elseif (is_array($column)) {
                $type = (string)($column['Type'] ?? '');
            }

            if ($type === '') {
                return [];
            }

            if (!preg_match_all("/'([^']+)'/", $type, $matches)) {
                return [];
            }

            $values = array_map(function ($value) {
                return strtolower(trim((string)$value));
            }, $matches[1] ?? []);

            return array_values(array_unique(array_filter($values, function ($value) {
                return $value !== '';
            })));
        } catch (Throwable $e) {
            error_log('Donations::getDonationStatusEnumValues warning: ' . $e->getMessage());
            return [];
        }
    }

    private function getPendingDonationCount($publisherId)
    {
        if ($publisherId <= 0) {
            return 0;
        }

        $sql = "SELECT COUNT(*) AS total
                FROM donations d
                INNER JOIN events e ON d.event_id = e.id
                WHERE e.created_by = ?
                  AND e.created_by_type = 'publisher'
                  AND d.status = 'pending'";

        $result = $this->query($sql, [$publisherId]);
        if (empty($result)) {
            return 0;
        }

        $row = $result[0];
        if (is_object($row)) {
            return (int)($row->total ?? 0);
        }

        if (is_array($row)) {
            return (int)($row['total'] ?? 0);
        }

        return 0;
    }
}
