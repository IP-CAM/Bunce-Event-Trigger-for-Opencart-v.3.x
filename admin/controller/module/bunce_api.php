<?php

namespace Opencart\Admin\Controller\Extension\BunceApi\Module;

class BunceApi extends \Opencart\System\Engine\Controller
{

	public function index(): void
	{
		$this->load->language('extension/bunceapi/module/bunce_api');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/bunceapi/module/bunce_api', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/bunceapi/module/bunce_api.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$this->load->model('setting/setting');

		// Load existing settings if they exist
		$settings = $this->model_setting_setting->getSetting('bunce_api');

		$data['bunce_api_event_id'] = $settings['bunce_api_event_id'] ?? '';
		$data['bunce_api_key'] = $settings['bunce_api_key'] ?? '';
		$data['bunce_api_status'] = $settings['bunce_api_status'] ?? '';

		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/bunceapi/module/bunce_api', $data));
	}


	public function save(): void
	{
		$this->load->language('extension/bunceapi/module/bunce_api');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			// Create a response payload with submitted data
			$payload = [
				'bunce_api_event_id' => $this->request->post['bunce_api_event_id'] ?? '',
				'bunce_api_key' => $this->request->post['bunce_api_key'] ?? '',
				'bunce_api_status' => $this->request->post['bunce_api_status'] ?? ''
			];

			// Add success message to the session data for display
			$this->session->data['success'] = $this->language->get('text_success');

			// Save the submitted data to the `setting` table
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('bunce_api', $payload);
		}

		$this->index();
	}


	public function triggerCheckoutAccessBeforeEvent(): void
	{
		// Load Bunce API settings from the configuration
		$event_id = $this->config->get('bunce_api_event_id');
		$api_key = $this->config->get('bunce_api_key');

		if ($event_id && $api_key) {
			// Prepare the data for the API request
			$url = 'https://test.api.bunce.so/v1/events/trigger';

			$data = array(
				'event_id' => $event_id,
				'payload' => array(
					'message' => 'Checkout page accessed - before'
				)
			);

			// Set the request headers
			$options = array(
				'header' => array(
					'Authorization: Bearer ' . $api_key,
					'Content-Type: application/json',
					'Content-Length: ' . strlen(json_encode($data))
				)
			);

			// Initialize CURL to trigger the API event
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			// Log the result of the API call
			if ($err) {
				$this->log->write('Bunce API Error (before): ' . $err);
			} else {
				$this->log->write('Bunce API Response (before): ' . $response);
			}
		} else {
			$this->log->write('Bunce API Error: Missing event_id or api_key for before event');
		}
	}

	public function triggerCheckoutAccessAfterEvent(): void
	{
		// Load Bunce API settings from the configuration
		$event_id = $this->config->get('bunce_api_event_id');
		$api_key = $this->config->get('bunce_api_key');

		if ($event_id && $api_key) {
			// Prepare the data for the API request
			$url = 'https://test.api.bunce.so/v1/events/trigger';

			$data = array(
				'event_id' => $event_id,
				'payload' => array(
					'message' => 'Checkout page accessed - after'
				)
			);

			// Set the request headers
			$options = array(
				'header' => array(
					'Authorization: Bearer ' . $api_key,
					'Content-Type: application/json',
					'Content-Length: ' . strlen(json_encode($data))
				)
			);

			// Initialize CURL to trigger the API event
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			// Log the result of the API call
			if ($err) {
				$this->log->write('Bunce API Error (after): ' . $err);
			} else {
				$this->log->write('Bunce API Response (after): ' . $response);
			}
		} else {
			$this->log->write('Bunce API Error: Missing event_id or api_key for after event');
		}
	}







	protected function validate()
	{
		// if (!$this->user->hasPermission('modify', 'extension/bunceapi/module/bunce_api')) {
		// 	$this->error['warning'] = $this->language->get('error_permission');
		// }

		// return !$this->error;
		return true;
	}

	public function install()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('bunce_api', [
			'bunce_api_status' => 0, // Disabled by default
			'bunce_api_event_id' => '',
			'bunce_api_key' => ''
		]);

		// Register the event to trigger Bunce API on checkout success
		$this->load->model('setting/event');
		// Register event for when checkout page is accessed (before)
		$this->model_setting_event->addEvent([
			'code' => 'extension_bunce_api_checkout_access_before',
			'description' => 'Trigger Bunce API before checkout page is accessed',
			'trigger' => 'catalog/controller/checkout/checkout/before',
			'action' => 'extension/bunceapi/module/bunce_api.triggerCheckoutAccessBeforeEvent',
			'status' => 1,
			'sort_order' => 0
		]);

		// Register event for when checkout page is accessed (after)
		$this->model_setting_event->addEvent([
			'code' => 'extension_bunce_api_checkout_access_after',
			'description' => 'Trigger Bunce API after checkout page is accessed',
			'trigger' => 'catalog/controller/checkout/checkout/after',
			'action' => 'extension/bunceapi/module/bunce_api.triggerCheckoutAccessAfterEvent',
			'status' => 1,
			'sort_order' => 0
		]);
	}

	public function uninstall()
	{
		$this->load->model('setting/setting');

		// Remove the settings added during installation
		$this->model_setting_setting->deleteSetting('bunce_api');

		$this->load->model('setting/event');

		// Remove the event
		$this->model_setting_event->deleteEventByCode('extension_bunce_api_checkout_access_before');
		$this->model_setting_event->deleteEventByCode('extension_bunce_api_checkout_access_after');
	}
}
