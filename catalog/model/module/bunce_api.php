<?php

namespace Opencart\Catalog\Model\Module\BunceApi;

class BunceApi extends \Opencart\System\Engine\Model
{
	public function triggerEvent($order_info)
	{
		$event_id = $this->config->get('bunce_api_event_id');
		$url = 'https://test.api.bunce.so/v1/events/trigger';

		$data = array(
			'event_id' => $event_id,
			'payload' => array(
				'email' => $order_info['email'],
				'amount' => $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100
			)
		);

		$options = array(
			'header' => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen(json_encode($data))
			)
		);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$this->log->write('Bunce API Error: ' . $err);
		} else {
			$this->log->write('Bunce API Response: ' . $response);
		}
	}
}
