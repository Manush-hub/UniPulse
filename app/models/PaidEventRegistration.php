<?php

class PaidEventRegistration
{
    use Model;

    protected $table = 'paid_event_registrations';
    protected $allowedColumns = [
        'event_id',
        'publisher_id',
        'payment_record_id',
        'event_title_snapshot',
        'publisher_name_snapshot',
        'registered_user_id',
        'registered_user_type',
        'registered_user_name_snapshot',
        'registered_user_email_snapshot',
        'registered_user_phone_snapshot',
        'order_number',
        'ticket_tier_name',
        'ticket_quantity',
        'currency_code',
        'subtotal_amount',
        'discount_amount',
        'service_fee_amount',
        'tax_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_gateway',
        'paid_at',
        'registration_status',
        'checked_in_at',
        'cancelled_at',
        'cancellation_reason',
        'refund_amount',
        'refunded_at',
        'registration_source',
        'metadata'
    ];

    private $tableColumnsCache = null;

    public function upsertByPaymentReference($data)
    {
        $columns = $this->getExistingColumns();
        if (empty($columns)) {
            return false;
        }

        $paymentReference = trim((string)($data['payment_transaction_id'] ?? ''));

        if ($paymentReference !== '') {
            $existingByPayment = $this->query(
                "SELECT id FROM {$this->table} WHERE payment_transaction_id = :ref LIMIT 1",
                ['ref' => $paymentReference]
            );

            if (!empty($existingByPayment)) {
                $existingId = (int)$existingByPayment[0]->id;
                $updateData = $this->filterExistingColumns($data, $columns);
                if (isset($updateData['metadata']) && is_array($updateData['metadata'])) {
                    $updateData['metadata'] = json_encode($updateData['metadata']);
                }
                if (empty($updateData)) {
                    return $existingId;
                }
                return $this->update($existingId, $updateData) ? $existingId : false;
            }
        }

        $existingByOrder = null;
        if (!empty($data['order_number'])) {
            $existingByOrder = $this->query(
                "SELECT id FROM {$this->table} WHERE order_number = :order_number LIMIT 1",
                ['order_number' => $data['order_number']]
            );
        }

        if (!empty($existingByOrder)) {
            $existingId = (int)$existingByOrder[0]->id;
            $updateData = $this->filterExistingColumns($data, $columns);
            if (isset($updateData['metadata']) && is_array($updateData['metadata'])) {
                $updateData['metadata'] = json_encode($updateData['metadata']);
            }
            if (empty($updateData)) {
                return $existingId;
            }
            return $this->update($existingId, $updateData) ? $existingId : false;
        }

        $insertData = $this->filterExistingColumns($data, $columns);
        if (isset($insertData['metadata']) && is_array($insertData['metadata'])) {
            $insertData['metadata'] = json_encode($insertData['metadata']);
        }

        foreach (['event_id', 'registered_user_id', 'registered_user_type', 'order_number', 'total_amount'] as $requiredKey) {
            if (!array_key_exists($requiredKey, $insertData)) {
                return false;
            }
        }

        $keys = array_keys($insertData);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $keys) . ") VALUES (:" . implode(',:', $keys) . ")";

        $conn = $this->connect();
        $stm = $conn->prepare($sql);
        $executeResult = $stm->execute($insertData);
        return $executeResult ? $conn->lastInsertId() : false;
    }

    private function filterExistingColumns($data, $columns)
    {
        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $columns, true)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    private function getExistingColumns()
    {
        if (is_array($this->tableColumnsCache)) {
            return $this->tableColumnsCache;
        }

        try {
            $result = $this->query("SHOW COLUMNS FROM {$this->table}");
            if (!$result) {
                $this->createTableIfMissing();
                $result = $this->query("SHOW COLUMNS FROM {$this->table}");
            }

            if (!$result) {
                $this->tableColumnsCache = [];
                return $this->tableColumnsCache;
            }

            $columns = [];
            foreach ($result as $column) {
                if (isset($column->Field)) {
                    $columns[] = $column->Field;
                }
            }

            $this->tableColumnsCache = $columns;
            return $this->tableColumnsCache;
        } catch (Throwable $e) {
            try {
                $this->createTableIfMissing();
                $result = $this->query("SHOW COLUMNS FROM {$this->table}");

                if ($result) {
                    $columns = [];
                    foreach ($result as $column) {
                        if (isset($column->Field)) {
                            $columns[] = $column->Field;
                        }
                    }

                    $this->tableColumnsCache = $columns;
                    return $this->tableColumnsCache;
                }
            } catch (Throwable $innerError) {
                error_log('PaidEventRegistration table recovery failed: ' . $innerError->getMessage());
            }

            error_log('PaidEventRegistration schema introspection failed: ' . $e->getMessage());
            $this->tableColumnsCache = [];
            return $this->tableColumnsCache;
        }
    }

    private function createTableIfMissing()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            publisher_id INT NULL,
            payment_record_id INT NULL,
            event_title_snapshot VARCHAR(255) NOT NULL,
            publisher_name_snapshot VARCHAR(255) NULL,
            registered_user_id INT NOT NULL,
            registered_user_type ENUM('university', 'public', 'publisher', 'sponsor') NOT NULL,
            registered_user_name_snapshot VARCHAR(255) NOT NULL,
            registered_user_email_snapshot VARCHAR(255) NULL,
            registered_user_phone_snapshot VARCHAR(25) NULL,
            order_number VARCHAR(40) NOT NULL,
            ticket_tier_name VARCHAR(100) NOT NULL DEFAULT 'General',
            ticket_quantity INT NOT NULL DEFAULT 1,
            currency_code CHAR(3) NOT NULL DEFAULT 'LKR',
            subtotal_amount DECIMAL(12,2) NOT NULL,
            discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            service_fee_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            total_amount DECIMAL(12,2) NOT NULL,
            payment_status ENUM('unpaid', 'pending', 'paid', 'failed', 'refunded', 'partially_refunded') NOT NULL DEFAULT 'pending',
            payment_method VARCHAR(50) NULL,
            payment_transaction_id VARCHAR(100) NULL,
            payment_gateway VARCHAR(50) NULL,
            paid_at DATETIME NULL,
            registration_status ENUM('reserved', 'confirmed', 'cancelled', 'checked_in', 'no_show') NOT NULL DEFAULT 'reserved',
            checked_in_at DATETIME NULL,
            cancelled_at DATETIME NULL,
            cancellation_reason VARCHAR(255) NULL,
            refund_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
            refunded_at DATETIME NULL,
            registration_source ENUM('web', 'mobile', 'admin', 'import') NOT NULL DEFAULT 'web',
            metadata JSON NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_paid_order_number (order_number),
            KEY idx_paid_event_id (event_id),
            KEY idx_paid_publisher_id (publisher_id),
            KEY idx_paid_user (registered_user_id, registered_user_type),
            KEY idx_paid_payment_status (payment_status),
            KEY idx_paid_registration_status (registration_status),
            KEY idx_paid_paid_at (paid_at),
            KEY idx_paid_transaction (payment_transaction_id),
            CONSTRAINT fk_paid_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            CONSTRAINT fk_paid_publisher FOREIGN KEY (publisher_id) REFERENCES publishers(id) ON DELETE SET NULL,
            CONSTRAINT fk_paid_payment FOREIGN KEY (payment_record_id) REFERENCES payments(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $conn = $this->connect();
        $stm = $conn->prepare($sql);
        $stm->execute();
    }
}
