<?php
namespace module;
class BunceEventTrigger extends \Opencart\System\Engine\Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/bunce_event_trigger');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_bunce_event_trigger', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['action'] = $this->url->link('extension/module/bunce_event_trigger', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $this->response->setOutput($this->load->view('extension/module/bunce_event_trigger', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/bunce_event_trigger')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }
}
