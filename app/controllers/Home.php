<?php

class Home extends Controller{
    use Database;

    public function index($a = '', $b = '' , $c = ''){
        $eventModel = new Event();

        // Public landing page should only show public events for guests.
        $data['boosted_events'] = $eventModel->getActiveBoostedEvents(10, null);
        $data['upcoming_events'] = $eventModel->getNextUpcomingPublicEvents(4, null);
        $data['stats'] = $this->getHomeStats();

        $this->view('home', $data);
    }

    private function getHomeStats()
    {
        $stats = [
            'total_events' => 0,
            'total_users' => 0,
        ];

        try {
            $eventsResult = $this->getRow("SELECT COUNT(*) AS total_events FROM events WHERE is_deleted = 0");
            if ($eventsResult && isset($eventsResult->total_events)) {
                $stats['total_events'] = (int) $eventsResult->total_events;
            }

            $usersResult = $this->getRow(
                "SELECT (
                    (SELECT COUNT(*) FROM publishers)
                    + (SELECT COUNT(*) FROM sponsors)
                    + (SELECT COUNT(*) FROM university_users)
                    + (SELECT COUNT(*) FROM public_users)
                ) AS total_users"
            );

            if ($usersResult && isset($usersResult->total_users)) {
                $stats['total_users'] = (int) $usersResult->total_users;
            }
        } catch (Exception $e) {
            error_log('Home stats error: ' . $e->getMessage());
        }

        return $stats;
    }
}
