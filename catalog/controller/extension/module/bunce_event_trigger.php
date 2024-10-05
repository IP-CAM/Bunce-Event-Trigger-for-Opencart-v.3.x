<?php
namespace Opencart\Catalog\Controller\Extension\Module;

class BunceEventTrigger extends \Opencart\System\Engine\Controller {
    public function triggerEvent() {
        // Load the settings for the module
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('module_bunce_event_trigger');
        $event_id = isset($settings['module_bunce_event_trigger_event_id']) ? $settings['module_bunce_event_trigger_event_id'] : null;
        $api_token = isset($settings['module_bunce_event_trigger_api_token']) ? $settings['module_bunce_event_trigger_api_token'] : null;

        // Validate input and settings
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!$event_id || !$api_token) {
                $this->response->setOutput($this->language->get('error_missing_settings'));
                return;
            }

            // Prepare the data payload
            $email = $this->customer->getEmail(); // Get the customer's email
            $amount = $this->cart->getTotal(); // Get the total cart amount
            $payload = array(
                'event_id' => $event_id,
                'payload' => array(
                    'email' => $email,
                    'amount' => $amount,
                    'departure' => "11:42",
                    'arrival' => "11:42",
                    'PNR' => 'PNR_T',
                    'departure_time' => "11:42",
                    'arrival_time' => "11:42",
                    'flight_number' => '312431AS',
                    'ticket_type' => "21321",
                    'payment_reference' => 'OPEN_CART',
                    'customer_name' => 'OPEN CART'
                )
            );

            // Initialize cURL
            $ch = curl_init('https://test.api.bunce.so/v1/events/trigger');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: "Bearer ' . $api_token . '"'
            ));

            // Execute the cURL request
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Check for cURL errors
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                $this->log->write('Bunce Event Trigger cURL Error: ' . $error_msg);
                $this->response->setOutput($this->language->get('error_api_call'));
                return;
            }

            // Close the cURL session
            curl_close($ch);

            // Handle the response
            if ($httpcode == 200) {
                $this->response->setOutput($this->language->get('text_success_event_trigger'));
            } else {
                $this->response->setOutput($this->language->get('text_failed_event_trigger'));
            }
        }
    }
}
