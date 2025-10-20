<?php

class Message {
    
    use Model;
    protected $table = 'messages';
    
    /**
     * Send a message from one user to another
     */
    public function sendMessage($data) {
        $query = "INSERT INTO messages (
            from_user_id, from_user_type, to_user_id, to_user_type, 
            subject, message
        ) VALUES (
            :from_user_id, :from_user_type, :to_user_id, :to_user_type,
            :subject, :message
        )";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute($data);
        
        if ($result) {
            return $conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Get messages for a specific user (both sent and received)
     */
    public function getUserMessages($userId, $userType, $type = 'all') {
        $whereClause = '';
        $params = [
            'user_id' => $userId,
            'user_type' => $userType
        ];
        
        switch ($type) {
            case 'received':
                $whereClause = "WHERE to_user_id = :user_id AND to_user_type = :user_type";
                break;
            case 'sent':
                $whereClause = "WHERE from_user_id = :user_id AND from_user_type = :user_type";
                break;
            default:
                $whereClause = "WHERE (to_user_id = :to_user_id AND to_user_type = :to_user_type) 
                               OR (from_user_id = :from_user_id AND from_user_type = :from_user_type)";
                $params = [
                    'to_user_id' => $userId,
                    'to_user_type' => $userType,
                    'from_user_id' => $userId,
                    'from_user_type' => $userType
                ];
                break;
        }
        
        $query = "SELECT m.*, 
                         -- Sender info
                         CASE 
                             WHEN m.from_user_type = 'publisher' THEN p.society_name
                             WHEN m.from_user_type = 'sponsor' THEN s.company_name
                             ELSE m.from_user_type
                         END as sender_name,
                         CASE 
                             WHEN m.from_user_type = 'publisher' THEN p.email
                             WHEN m.from_user_type = 'sponsor' THEN s.email
                             ELSE NULL
                         END as sender_email,
                         -- Recipient info
                         CASE 
                             WHEN m.to_user_type = 'publisher' THEN p2.society_name
                             WHEN m.to_user_type = 'sponsor' THEN s2.company_name
                             ELSE m.to_user_type
                         END as recipient_name,
                         CASE 
                             WHEN m.to_user_type = 'publisher' THEN p2.email
                             WHEN m.to_user_type = 'sponsor' THEN s2.email
                             ELSE NULL
                         END as recipient_email
                  FROM messages m
                  LEFT JOIN publishers p ON (m.from_user_type = 'publisher' AND m.from_user_id = p.id)
                  LEFT JOIN sponsors s ON (m.from_user_type = 'sponsor' AND m.from_user_id = s.id)
                  LEFT JOIN publishers p2 ON (m.to_user_type = 'publisher' AND m.to_user_id = p2.id)
                  LEFT JOIN sponsors s2 ON (m.to_user_type = 'sponsor' AND m.to_user_id = s2.id)
                  $whereClause
                  ORDER BY m.created_at DESC";
        
        $result = $this->query($query, $params);
        return is_array($result) ? $result : [];
    }
    
    /**
     * Get a specific message by ID
     */
    public function getMessageById($messageId, $userId = null, $userType = null) {
        $query = "SELECT m.*, 
                         -- Sender info
                         CASE 
                             WHEN m.from_user_type = 'publisher' THEN p.society_name
                             WHEN m.from_user_type = 'sponsor' THEN s.company_name
                             ELSE m.from_user_type
                         END as sender_name,
                         CASE 
                             WHEN m.from_user_type = 'publisher' THEN p.email
                             WHEN m.from_user_type = 'sponsor' THEN s.email
                             ELSE NULL
                         END as sender_email,
                         -- Recipient info
                         CASE 
                             WHEN m.to_user_type = 'publisher' THEN p2.society_name
                             WHEN m.to_user_type = 'sponsor' THEN s2.company_name
                             ELSE m.to_user_type
                         END as recipient_name,
                         CASE 
                             WHEN m.to_user_type = 'publisher' THEN p2.email
                             WHEN m.to_user_type = 'sponsor' THEN s2.email
                             ELSE NULL
                         END as recipient_email
                  FROM messages m
                  LEFT JOIN publishers p ON (m.from_user_type = 'publisher' AND m.from_user_id = p.id)
                  LEFT JOIN sponsors s ON (m.from_user_type = 'sponsor' AND m.from_user_id = s.id)
                  LEFT JOIN publishers p2 ON (m.to_user_type = 'publisher' AND m.to_user_id = p2.id)
                  LEFT JOIN sponsors s2 ON (m.to_user_type = 'sponsor' AND m.to_user_id = s2.id)
                  WHERE m.id = :message_id";
        
        $params = ['message_id' => $messageId];
        
        // Add user verification if provided
        if ($userId && $userType) {
            $query .= " AND ((m.from_user_id = :from_user_id AND m.from_user_type = :from_user_type) 
                            OR (m.to_user_id = :to_user_id AND m.to_user_type = :to_user_type))";
            $params['from_user_id'] = $userId;
            $params['from_user_type'] = $userType;
            $params['to_user_id'] = $userId;
            $params['to_user_type'] = $userType;
        }
        
        return $this->getRow($query, $params);
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead($messageId, $userId, $userType) {
        $query = "UPDATE messages 
                  SET is_read = TRUE, read_at = CURRENT_TIMESTAMP
                  WHERE id = :message_id 
                  AND to_user_id = :user_id 
                  AND to_user_type = :user_type";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'message_id' => $messageId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        
        // Return true if execution was successful and at least one row was affected
        return $result && $stm->rowCount() > 0;
    }
    
    /**
     * Get unread message count for a user
     */
    public function getUnreadCount($userId, $userType) {
        $query = "SELECT COUNT(*) as unread_count 
                  FROM messages 
                  WHERE to_user_id = :user_id 
                  AND to_user_type = :user_type 
                  AND is_read = FALSE";
        
        $result = $this->getRow($query, [
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        
        return $result ? (int)$result->unread_count : 0;
    }
    
    /**
     * Update a message (only by sender and only if not read yet)
     */
    public function updateMessage($messageId, $userId, $userType, $subject, $messageContent) {
        // First check if the message exists, belongs to user, and hasn't been read
        $checkQuery = "SELECT id, is_read FROM messages 
                       WHERE id = :message_id 
                       AND from_user_id = :user_id 
                       AND from_user_type = :user_type";
        
        $message = $this->getRow($checkQuery, [
            'message_id' => $messageId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        
        if (!$message) {
            return ['success' => false, 'message' => 'Message not found or you do not have permission to edit it'];
        }
        
        if ($message->is_read) {
            return ['success' => false, 'message' => 'Cannot edit a message that has already been read'];
        }
        
        // Update the message
        $updateQuery = "UPDATE messages 
                        SET subject = :subject, 
                            message = :message_content,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = :message_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($updateQuery);
        $result = $stm->execute([
            'subject' => $subject,
            'message_content' => $messageContent,
            'message_id' => $messageId
        ]);
        
        if ($result && $stm->rowCount() > 0) {
            return ['success' => true, 'message' => 'Message updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update message'];
        }
    }
    
    /**
     * Check if a message can be edited (belongs to user and not read yet)
     */
    public function canEditMessage($messageId, $userId, $userType) {
        $query = "SELECT id, is_read FROM messages 
                  WHERE id = :message_id 
                  AND from_user_id = :user_id 
                  AND from_user_type = :user_type";
        
        $message = $this->getRow($query, [
            'message_id' => $messageId,
            'user_id' => $userId,
            'user_type' => $userType
        ]);
        
        return $message && !$message->is_read;
    }

    /**
     * Delete a message (only by sender or recipient)
     */
    public function deleteMessage($messageId, $userId, $userType) {
        $query = "DELETE FROM messages 
                  WHERE id = :message_id 
                  AND ((from_user_id = :from_user_id AND from_user_type = :from_user_type) 
                       OR (to_user_id = :to_user_id AND to_user_type = :to_user_type))";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'message_id' => $messageId,
            'from_user_id' => $userId,
            'from_user_type' => $userType,
            'to_user_id' => $userId,
            'to_user_type' => $userType
        ]);
        
        // Return true if execution was successful and at least one row was affected
        return $result && $stm->rowCount() > 0;
    }
    
    /**
     * Get conversation between two users
     */
    public function getConversation($user1Id, $user1Type, $user2Id, $user2Type) {
        $query = "SELECT m.*, 
                         -- Sender info
                         CASE 
                             WHEN m.from_user_type = 'publisher' THEN p.society_name
                             WHEN m.from_user_type = 'sponsor' THEN s.company_name
                             ELSE m.from_user_type
                         END as sender_name,
                         -- Recipient info
                         CASE 
                             WHEN m.to_user_type = 'publisher' THEN p2.society_name
                             WHEN m.to_user_type = 'sponsor' THEN s2.company_name
                             ELSE m.to_user_type
                         END as recipient_name
                  FROM messages m
                  LEFT JOIN publishers p ON (m.from_user_type = 'publisher' AND m.from_user_id = p.id)
                  LEFT JOIN sponsors s ON (m.from_user_type = 'sponsor' AND m.from_user_id = s.id)
                  LEFT JOIN publishers p2 ON (m.to_user_type = 'publisher' AND m.to_user_id = p2.id)
                  LEFT JOIN sponsors s2 ON (m.to_user_type = 'sponsor' AND m.to_user_id = s2.id)
                  WHERE ((m.from_user_id = :user1_id AND m.from_user_type = :user1_type AND m.to_user_id = :user2_id AND m.to_user_type = :user2_type)
                         OR (m.from_user_id = :user2_id AND m.from_user_type = :user2_type AND m.to_user_id = :user1_id AND m.to_user_type = :user1_type))
                  ORDER BY m.created_at ASC";
        
        return $this->query($query, [
            'user1_id' => $user1Id,
            'user1_type' => $user1Type,
            'user2_id' => $user2Id,
            'user2_type' => $user2Type
        ]);
    }
}