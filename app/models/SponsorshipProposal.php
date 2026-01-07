<?php

class SponsorshipProposal {
    
    use Model;
    
    protected $table = 'sponsorship_proposals';
    protected $allowedColumns = [
        'event_id',
        'sponsor_id',
        'sponsor_name',
        'proposal_type',
        'title',
        'description',
        'monetary_amount',
        'currency',
        'payment_schedule',
        'in_kind_items',
        'estimated_value',
        'service_description',
        'service_duration',
        'deliverables',
        'expected_benefits',
        'contact_person',
        'contact_phone',
        'contact_email',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'contract_url',
        'agreement_status',
        'deleted_at'
    ];
    
    /**
     * Create a new sponsorship proposal
     */
    public function createProposal($data) {
        // Validate required fields
        if (empty($data['event_id']) || empty($data['sponsor_id']) || 
            empty($data['title']) || empty($data['description'])) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }
        
        // Set defaults
        $data['status'] = $data['status'] ?? 'draft';
        $data['proposal_type'] = $data['proposal_type'] ?? 'mixed';
        $data['agreement_status'] = 'pending';
        
        // Convert arrays to JSON for storage
        if (isset($data['deliverables']) && is_array($data['deliverables'])) {
            $data['deliverables'] = json_encode($data['deliverables']);
        }
        if (isset($data['expected_benefits']) && is_array($data['expected_benefits'])) {
            $data['expected_benefits'] = json_encode($data['expected_benefits']);
        }
        if (isset($data['in_kind_items']) && is_array($data['in_kind_items'])) {
            $data['in_kind_items'] = json_encode($data['in_kind_items']);
        }
        
        try {
            $result = $this->insert($data);
            return $result ? ['success' => true, 'id' => $result, 'message' => 'Proposal created'] 
                          : ['success' => false, 'message' => 'Failed to create proposal'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get proposals for an event
     */
    public function getProposalsByEvent($eventId, $status = null) {
        $where = "WHERE event_id = :event_id AND deleted_at IS NULL";
        $params = ['event_id' => $eventId];
        
        if ($status) {
            $where .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $query = "SELECT sp.*, s.company_name, s.email as sponsor_email
                  FROM {$this->table} sp
                  LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                  $where
                  ORDER BY sp.created_at DESC";
        
        return $this->query($query, $params);
    }
    
    /**
     * Get proposals by sponsor
     */
    public function getProposalsBySponsor($sponsorId, $status = null) {
        $where = "WHERE sponsor_id = :sponsor_id AND deleted_at IS NULL";
        $params = ['sponsor_id' => $sponsorId];
        
        if ($status) {
            $where .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $query = "SELECT sp.*, e.title as event_title
                  FROM {$this->table} sp
                  LEFT JOIN events e ON sp.event_id = e.id
                  $where
                  ORDER BY sp.created_at DESC";
        
        return $this->query($query, $params);
    }
    
    /**
     * Get proposal by ID
     */
    public function getProposalById($proposalId) {
        $query = "SELECT sp.*,
                         s.company_name, s.email as sponsor_email,
                         e.title as event_title, e.id as event_id,
                         a.email as reviewed_by_admin
                  FROM {$this->table} sp
                  LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                  LEFT JOIN events e ON sp.event_id = e.id
                  LEFT JOIN admin a ON sp.reviewed_by = a.id
                  WHERE sp.id = :proposal_id AND sp.deleted_at IS NULL";
        
        $proposal = $this->getRow($query, ['proposal_id' => $proposalId]);
        
        // Decode JSON fields
        if ($proposal) {
            if ($proposal->deliverables) {
                $proposal->deliverables = json_decode($proposal->deliverables, true) ?: [];
            }
            if ($proposal->expected_benefits) {
                $proposal->expected_benefits = json_decode($proposal->expected_benefits, true) ?: [];
            }
            if ($proposal->in_kind_items) {
                $proposal->in_kind_items = json_decode($proposal->in_kind_items, true) ?: [];
            }
        }
        
        return $proposal;
    }
    
    /**
     * Update proposal
     */
    public function updateProposal($proposalId, $sponsorId, $data) {
        // Check if proposal exists and belongs to sponsor
        $proposal = $this->getProposalById($proposalId);
        if (!$proposal || $proposal->sponsor_id != $sponsorId) {
            return ['success' => false, 'message' => 'Cannot update this proposal'];
        }
        
        // Can only update draft proposals
        if ($proposal->status !== 'draft') {
            return ['success' => false, 'message' => 'Can only edit draft proposals'];
        }
        
        try {
            $updateData = [];
            $allowed = ['title', 'description', 'proposal_type', 'monetary_amount', 'currency',
                       'payment_schedule', 'in_kind_items', 'estimated_value', 'service_description',
                       'service_duration', 'deliverables', 'expected_benefits', 'contact_person',
                       'contact_phone', 'contact_email'];
            
            foreach ($allowed as $field) {
                if (isset($data[$field])) {
                    if (in_array($field, ['deliverables', 'expected_benefits', 'in_kind_items']) 
                        && is_array($data[$field])) {
                        $updateData[$field] = json_encode($data[$field]);
                    } else {
                        $updateData[$field] = $data[$field];
                    }
                }
            }
            
            if (empty($updateData)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }
            
            $updateData['id'] = $proposalId;
            $result = $this->update($proposalId, $updateData);
            
            return $result ? ['success' => true, 'message' => 'Proposal updated'] 
                          : ['success' => false, 'message' => 'Update failed'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Submit proposal for review
     */
    public function submitProposal($proposalId, $sponsorId) {
        $proposal = $this->getProposalById($proposalId);
        if (!$proposal || $proposal->sponsor_id != $sponsorId) {
            return ['success' => false, 'message' => 'Cannot submit this proposal'];
        }
        
        if ($proposal->status !== 'draft') {
            return ['success' => false, 'message' => 'Proposal has already been submitted'];
        }
        
        // Validate required fields are filled
        if (empty($proposal->title) || empty($proposal->description) || 
            empty($proposal->contact_person) || empty($proposal->contact_email)) {
            return ['success' => false, 'message' => 'Please fill in all required fields'];
        }
        
        $query = "UPDATE {$this->table}
                  SET status = 'submitted',
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute(['proposal_id' => $proposalId]);
        
        return $result && $stm->rowCount() > 0 ? 
            ['success' => true, 'message' => 'Proposal submitted for review'] :
            ['success' => false, 'message' => 'Submission failed'];
    }
    
    /**
     * Accept proposal
     */
    public function acceptProposal($proposalId, $adminId) {
        $query = "UPDATE {$this->table}
                  SET status = 'accepted',
                      reviewed_by = :reviewed_by,
                      reviewed_at = CURRENT_TIMESTAMP,
                      rejection_reason = NULL,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'proposal_id' => $proposalId,
            'reviewed_by' => $adminId
        ]);
        
        return $result && $stm->rowCount() > 0;
    }
    
    /**
     * Reject proposal
     */
    public function rejectProposal($proposalId, $adminId, $reason) {
        $query = "UPDATE {$this->table}
                  SET status = 'rejected',
                      reviewed_by = :reviewed_by,
                      reviewed_at = CURRENT_TIMESTAMP,
                      rejection_reason = :reason,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'proposal_id' => $proposalId,
            'reviewed_by' => $adminId,
            'reason' => $reason
        ]);
        
        return $result && $stm->rowCount() > 0;
    }
    
    /**
     * Update agreement status
     */
    public function updateAgreementStatus($proposalId, $sponsorId, $status) {
        if (!in_array($status, ['pending', 'signed', 'declined'])) {
            return ['success' => false, 'message' => 'Invalid agreement status'];
        }
        
        $proposal = $this->getProposalById($proposalId);
        if (!$proposal || $proposal->sponsor_id != $sponsorId) {
            return ['success' => false, 'message' => 'Cannot update agreement status'];
        }
        
        $query = "UPDATE {$this->table}
                  SET agreement_status = :status,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute([
            'proposal_id' => $proposalId,
            'status' => $status
        ]);
        
        return $result && $stm->rowCount() > 0 ? 
            ['success' => true, 'message' => 'Agreement status updated'] :
            ['success' => false, 'message' => 'Update failed'];
    }
    
    /**
     * Delete proposal (soft delete)
     */
    public function deleteProposal($proposalId, $sponsorId) {
        $proposal = $this->getProposalById($proposalId);
        if (!$proposal || $proposal->sponsor_id != $sponsorId) {
            return ['success' => false, 'message' => 'Cannot delete this proposal'];
        }
        
        if ($proposal->status !== 'draft') {
            return ['success' => false, 'message' => 'Can only delete draft proposals'];
        }
        
        $query = "UPDATE {$this->table}
                  SET deleted_at = CURRENT_TIMESTAMP
                  WHERE id = :proposal_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        $result = $stm->execute(['proposal_id' => $proposalId]);
        
        return $result && $stm->rowCount() > 0 ? 
            ['success' => true, 'message' => 'Proposal deleted'] :
            ['success' => false, 'message' => 'Delete failed'];
    }
    
    /**
     * Get pending proposals awaiting review
     */
    public function getPendingProposals() {
        $query = "SELECT sp.*, s.company_name, e.title as event_title
                  FROM {$this->table} sp
                  LEFT JOIN sponsors s ON sp.sponsor_id = s.id
                  LEFT JOIN events e ON sp.event_id = e.id
                  WHERE sp.status IN ('submitted', 'under_review')
                  AND sp.deleted_at IS NULL
                  ORDER BY sp.created_at ASC";
        
        return $this->query($query, []);
    }
    
    /**
     * Track proposal view
     */
    public function trackView($proposalId) {
        $query = "UPDATE {$this->table}
                  SET views_count = views_count + 1
                  WHERE id = :proposal_id";
        
        $conn = $this->connect();
        $stm = $conn->prepare($query);
        return $stm->execute(['proposal_id' => $proposalId]);
    }
}
