<?php

class Ticket extends Controller
{
    use Database;

    public function download()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/signin');
            exit();
        }

        $orderNumber = $_GET['order'] ?? null;
        if (!$orderNumber) {
            die("Order number not provided.");
        }

        $query = "SELECT r.*, e.title as event_title, e.event_date, e.event_time, e.location_type, e.location, e.venue_name, e.city, e.street_address 
                  FROM paid_event_registrations r 
                  JOIN events e ON r.event_id = e.id 
                  WHERE r.order_number = :order AND r.registered_user_id = :uid";
        
        $result = $this->query($query, [
            'order' => $orderNumber,
            'uid' => $_SESSION['user_id']
        ]);

        if (empty($result)) {
            die("Ticket not found or unauthorized.");
        }

        $ticketData = $result[0];
        $this->view('User/ticket_pdf', ['ticket' => $ticketData]);
    }
}
