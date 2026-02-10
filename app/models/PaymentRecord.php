<?php

class PaymentRecord {
    
    use Model;
    
    protected $table = 'payments';
    protected $allowedColumns = [
        'user_id',
        'amount',
        'payment_method',
        'payment_type',
        'transaction_id',
        'status',
        'event_id',
        'publisher_id',
        'commission_amount',
        'organizer_amount',
        'description',
        'created_at'
    ];

    public function validate($data) {
        $this->errors = [];
        
        // Add validation rules if needed
        
        return empty($this->errors);
    }
}
