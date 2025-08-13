<?php

include_once ABSPATH . 'wp-load.php';

class EulenWebhook {

    private $database;

    public function __construct() {
        $this->database = new DepixTablesWP();
    }

    public function init() {
        add_action('rest_api_init', array($this, 'registerRoutes'));
    }

    public function registerRoutes() {
        register_rest_route('depix/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handleRequest'),
            'permission_callback' => '__return_true',
        ));
    }

    public function handleRequest(WP_REST_Request $request) {
        $rawData = $request->get_body();
        $data = json_decode($rawData, true);
        if(!is_array($data) || empty($data['id'])) {
            return new WP_REST_Response(['error' => 'invalid_payload'], 400);
        }

        $token = EulenPanel::get_plain_token();
        if ($token) {
            $provided = $request->get_header('x-depix-signature');
            $calc = base64_encode(hash_hmac('sha256', $rawData, $token, true));
            if (!$provided || !hash_equals($calc, $provided)) {
                return new WP_REST_Response(['error' => 'invalid_signature'], 401);
            }
        }

        $updated = $this->database->updateTransaction($data);

        return new WP_REST_Response([
            'ok' => (bool)$updated,
        ], 200);
    }

}
