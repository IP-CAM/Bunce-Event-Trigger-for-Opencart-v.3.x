<?php

namespace Opencart\Catalog\Controller\Module;

use Opencart\System\Engine\Controller;

class BunceApi extends Controller
{
	public function index()
	{
		$this->load->language('extension/bunce_api/bunce_api');

		// Set the title and data for the view
		$this->document->setTitle($this->language->get('heading_title'));

		$data['some_setting'] = $this->config->get('bunce_api_key');  // Example usage of your setting
		$data['action'] = $this->url->link('extension/bunce_api/save', 'user_token=' . $this->session->data['user_token'], true);

		// Load view
		$this->response->setOutput($this->load->view('extension/bunce_api', $data));
	}

	public function save()
	{
		$this->load->language('extension/bunce_api/bunce_api');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('bunce_api', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			// Redirect to the extension page
			$this->response->redirect($this->url->link('extension/bunce_api', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->index();
	}
}
