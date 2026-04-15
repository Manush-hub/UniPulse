<?php

class Report {
    
    use Model;
    
    protected $table = 'reports';
    protected $allowedColumns = [
        'reporter_id',
        'reported_content_type',
        'reported_content_id',
        'report_type',
        'priority',
        'description',
        'status',
        'assigned_moderator_id',
        'resolution',
        'action_taken',
        'resolved_at'
    ];
    
    /**
     * Get reports for a specific university
     */
    public function getReportsForUniversity($university, $limit = 50) {
        $query = "SELECT r.*, 
                         u.full_name as reporter_name,
                         u.email as reporter_email,
                         m.full_name as assigned_moderator_name
                  FROM reports r
                  LEFT JOIN users u ON r.reporter_id = u.id
                  LEFT JOIN moderators m ON r.assigned_moderator_id = m.id
                  WHERE r.university = :university
                  ORDER BY r.created_at DESC
                  LIMIT :limit";
        
        return $this->query($query, [
            'university' => $university,
            'limit' => $limit
        ]);
    }
    
    /**
     * Get report statistics for a university
     */
    public function getReportStatsForUniversity($university) {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'resolved' AND DATE(resolved_at) = CURDATE() THEN 1 ELSE 0 END) as resolved_today
                  FROM reports 
                  WHERE university = :university";
        
        return $this->getRow($query, ['university' => $university]);
    }
    
    /**
     * Assign report to a moderator
     */
    public function assignToModerator($reportId, $moderatorId) {
        $query = "UPDATE reports 
                  SET assigned_moderator_id = :moderator_id,
                      status = 'in_progress',
                      updated_at = NOW()
                  WHERE id = :report_id";
        
        $result = $this->query($query, [
            'report_id' => $reportId,
            'moderator_id' => $moderatorId
        ]);
        
        return $result !== false;
    }
    
    /**
     * Resolve a report
     */
    public function resolve($reportId, $moderatorId, $resolution, $actionTaken) {
        $query = "UPDATE reports 
                  SET status = 'resolved',
                      resolution = :resolution,
                      action_taken = :action_taken,
                      assigned_moderator_id = :moderator_id,
                      resolved_at = NOW(),
                      updated_at = NOW()
                  WHERE id = :report_id";
        
        $result = $this->query($query, [
            'report_id' => $reportId,
            'moderator_id' => $moderatorId,
            'resolution' => $resolution,
            'action_taken' => $actionTaken
        ]);
        
        return $result !== false;
    }
    
    /**
     * Get detailed report information
     */
    public function getReportDetails($reportId) {
        $query = "SELECT r.*, 
                         u.full_name as reporter_name,
                         u.email as reporter_email,
                         m.full_name as assigned_moderator_name,
                         CASE 
                           WHEN r.reported_content_type = 'event' THEN e.title
                           WHEN r.reported_content_type = 'comment' THEN c.content
                           ELSE 'Unknown Content'
                         END as reported_content_title
                  FROM reports r
                  LEFT JOIN users u ON r.reporter_id = u.id
                  LEFT JOIN moderators m ON r.assigned_moderator_id = m.id
                  LEFT JOIN events e ON r.reported_content_type = 'event' AND r.reported_content_id = e.id
                  LEFT JOIN comments c ON r.reported_content_type = 'comment' AND r.reported_content_id = c.id
                  WHERE r.id = :report_id";
        
        return $this->getRow($query, ['report_id' => $reportId]);
    }
    
    /**
     * Get filtered reports
     */
    public function getFilteredReports($university, $status = 'all', $type = 'all', $priority = 'all', $dateRange = 'all') {
        $query = "SELECT r.*, 
                         u.full_name as reporter_name,
                         u.email as reporter_email,
                         m.full_name as assigned_moderator_name
                  FROM reports r
                  LEFT JOIN users u ON r.reporter_id = u.id
                  LEFT JOIN moderators m ON r.assigned_moderator_id = m.id
                  WHERE r.university = :university";
        
        $params = ['university' => $university];
        
        // Add status filter
        if ($status !== 'all') {
            $query .= " AND r.status = :status";
            $params['status'] = $status;
        }
        
        // Add type filter
        if ($type !== 'all') {
            $query .= " AND r.report_type = :type";
            $params['type'] = $type;
        }
        
        // Add priority filter
        if ($priority !== 'all') {
            $query .= " AND r.priority = :priority";
            $params['priority'] = $priority;
        }
        
        // Add date filter
        switch ($dateRange) {
            case 'today':
                $query .= " AND DATE(r.created_at) = CURDATE()";
                break;
            case 'week':
                $query .= " AND r.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $query .= " AND r.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        return $this->query($query, $params);
    }
    
    /**
     * Create a new report
     */
    public function create($data) {
        $query = "INSERT INTO reports (
                    reporter_id, reported_content_type, reported_content_id, 
                    report_type, priority, description, university, status
                  ) VALUES (
                    :reporter_id, :reported_content_type, :reported_content_id,
                    :report_type, :priority, :description, :university, 'pending'
                  )";
        
        $result = $this->query($query, $data);
        if ($result !== false) {
            $conn = $this->connect();
            return $conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Get report by ID
     */
    public function findById($id) {
        $query = "SELECT * FROM reports WHERE id = :id LIMIT 1";
        return $this->getRow($query, ['id' => $id]);
    }
}
?>