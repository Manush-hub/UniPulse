<?php

class SponsorPost {
    
    use Model;
    
    protected $table = 'sponsor_posts';
    protected $allowedColumns = [
        'event_id',
        'sponsor_id',
        'sponsor_name',
        'title',
        'content',
        'image_url',
        'brand_logo_url',
        'website_url',
        'call_to_action_text',
        'call_to_action_url',
        'approval_status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'display_priority',
        'views_count',
        'clicks_count',
        'deleted_at'
    ];
    
    /**
     * Create a new sponsor post
     */
    public function createPost($data) {
        // Validate required fields
        if (empty($data['event_id']) || empty($data['sponsor_id']) || 
            empty($data['title']) || empty($data['content'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }
        
        // Set default values
        $data['approval_status'] = 'pending';
        $data['views_count'] = 0;
        $data['clicks_count'] = 0;
        
        try {
            $result = $this->insert($data);
            return $result ? ['success' => true, 'id' => $result, 'message' => 'Post created and pending approval'] 
                          : ['success' => false, 'message' => 'Failed to create post'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get approved posts for an event
     */
    public function getApprovedPostsByEvent($eventId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE event_id = :event_id 
                  AND approval_status = 'approved'
                  AND deleted_at IS NULL
                  ORDER BY display_priority DESC, created_at DESC";
        
        return $this->query($query, ['event_id' => $eventId]);
    }
    
    /**
     * Get pending posts for approval
     */
    public function getPendingPosts() {
        $query = "SELECT sp.*, s.company_name, s.email, e.title as event_title
                  FROM {$this->table} sp
                  LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                  LEFT JOIN events e ON sp.event_id = e.id
                  WHERE sp.approval_status = 'pending'
                  AND sp.deleted_at IS NULL
                  ORDER BY sp.created_at ASC";
        
        return $this->query($query, []);
    }
    
    /**
     * Get posts by sponsor
     */
    public function getPostsBySponsor($sponsorId, $status = null) {
        $where = "WHERE sponsor_id = :sponsor_id AND deleted_at IS NULL";
        $params = ['sponsor_id' => $sponsorId];
        
        if ($status) {
            $where .= " AND approval_status = :status";
            $params['status'] = $status;
        }
        
        $query = "SELECT sp.*, e.title as event_title, e.id as event_id
                  FROM {$this->table} sp
                  LEFT JOIN events e ON sp.event_id = e.id
                  $where
                  ORDER BY sp.created_at DESC";
        
        return $this->query($query, $params);
    }
    
    /**
     * Get post by ID with details
     */
    public function getPostById($postId) {
        $query = "SELECT sp.*, 
                         s.company_name, s.email as sponsor_email,
                         e.title as event_title, e.description as event_description,
                         a.email as approved_by_admin
                  FROM {$this->table} sp
                  LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                  LEFT JOIN events e ON sp.event_id = e.id
                  LEFT JOIN admin a ON sp.approved_by = a.id
                  WHERE sp.id = :post_id AND sp.deleted_at IS NULL";
        
        return $this->getRow($query, ['post_id' => $postId]);
    }
    
    /**
     * Approve a post
     */
    public function approvePost($postId, $adminId) {
        $query = "UPDATE {$this->table}
                  SET approval_status = 'approved',
                      approved_by = :approved_by,
                      approved_at = CURRENT_TIMESTAMP,
                      rejection_reason = NULL,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :post_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'post_id' => $postId,
            'approved_by' => $adminId
        ]);
        
        return $result && $stm->rowCount() > 0;
    }
    
    /**
     * Reject a post
     */
    public function rejectPost($postId, $adminId, $reason) {
        $query = "UPDATE {$this->table}
                  SET approval_status = 'rejected',
                      approved_by = :approved_by,
                      approved_at = CURRENT_TIMESTAMP,
                      rejection_reason = :reason,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :post_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'post_id' => $postId,
            'approved_by' => $adminId,
            'reason' => $reason
        ]);
        
        return $result && $stm->rowCount() > 0;
    }
    
    /**
     * Update post (only if pending)
     */
    public function updatePost($postId, $sponsorId, $data) {
        // Check if post exists and belongs to sponsor and is editable (pending or rejected)
        $post = $this->getPostById($postId);
        if (!$post || $post->sponsor_id != $sponsorId) {
            return ['success' => false, 'message' => 'Cannot update this post'];
        }

        // Allow updates when post is pending or previously rejected (so sponsor can revise and resubmit)
        if ($post->approval_status !== 'pending' && $post->approval_status !== 'rejected') {
            return ['success' => false, 'message' => 'Cannot update a reviewed post'];
        }
        
        try {
            $updateData = [];
            $allowed = ['title', 'content', 'image_url', 'brand_logo_url', 'website_url', 
                       'call_to_action_text', 'call_to_action_url'];
            
            foreach ($allowed as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }
            
            if (empty($updateData)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }

            // If the post was rejected, reset approval status to pending and clear rejection reason on resubmit
            if ($post->approval_status === 'rejected') {
                $updateData['approval_status'] = 'pending';
                $updateData['rejection_reason'] = null;
            }

            $updateData['id'] = $postId;
            $result = $this->update($postId, $updateData);

            return $result ? ['success' => true, 'message' => 'Post updated and resubmitted for review'] 
                          : ['success' => false, 'message' => 'Update failed'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete post (soft delete)
     */
    public function deletePost($postId, $sponsorId) {
        // Only sponsor or admin can delete
        $post = $this->getPostById($postId);
        if (!$post || $post->sponsor_id != $sponsorId) {
            return ['success' => false, 'message' => 'Cannot delete this post'];
        }
        
        $query = "UPDATE {$this->table}
                  SET deleted_at = CURRENT_TIMESTAMP
                  WHERE id = :post_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute(['post_id' => $postId]);
        
        return $result && $stm->rowCount() > 0 ? 
            ['success' => true, 'message' => 'Post deleted'] :
            ['success' => false, 'message' => 'Delete failed'];
    }
    
    /**
     * Track post view
     */
    public function trackView($postId) {
        $query = "UPDATE {$this->table}
                  SET views_count = views_count + 1
                  WHERE id = :post_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        return $stm->execute(['post_id' => $postId]);
    }
    
    /**
     * Track CTA click
     */
    public function trackClick($postId) {
        $query = "UPDATE {$this->table}
                  SET clicks_count = clicks_count + 1
                  WHERE id = :post_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        return $stm->execute(['post_id' => $postId]);
    }
    
    /**
     * Validate sponsor profile is complete
     */
    public static function validateSponsorProfile($sponsorId) {
        $sponsor = new Sponsor();
        $profile = $sponsor->getSponsorById($sponsorId);
        
        if (!$profile) {
            return ['valid' => false, 'message' => 'Sponsor not found'];
        }
        
        // Check for required profile fields
        $requiredFields = ['company_name', 'email', 'phone'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($profile->$field ?? null)) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            return [
                'valid' => false, 
                'message' => 'Complete your sponsor profile first: ' . implode(', ', $missingFields)
            ];
        }
        
        return ['valid' => true, 'message' => 'Profile complete'];
    }
    
    /**
     * Validate post content against guidelines
     */
    public static function validateContent($title, $content) {
        $errors = [];
        
        // Title validation
        if (strlen($title) < 5) {
            $errors[] = 'Title must be at least 5 characters';
        }
        if (strlen($title) > 255) {
            $errors[] = 'Title must not exceed 255 characters';
        }
        
        // Content validation
        if (strlen($content) < 20) {
            $errors[] = 'Content must be at least 20 characters';
        }
        if (strlen($content) > 5000) {
            $errors[] = 'Content must not exceed 5000 characters';
        }
        
        // Check for prohibited content
        $prohibited = ['viagra', 'casino', 'porn', 'xxx', 'drug', 'illegal'];
        $lowerContent = strtolower($content);
        
        foreach ($prohibited as $word) {
            if (strpos($lowerContent, $word) !== false) {
                $errors[] = 'Content contains prohibited keywords';
                break;
            }
        }
        
        return empty($errors) ? ['valid' => true] : ['valid' => false, 'errors' => $errors];
    }
}
