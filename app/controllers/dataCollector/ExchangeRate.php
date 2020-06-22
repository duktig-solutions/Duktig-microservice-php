<?php

namespace App\Controllers\dataCollector;

class ExchangeRate {

	/**
	 * Collect and Parse XML Data from CBA - Exchange rate
	 *
	 * @return bool
	 */
	public function cliCollect() {

		$data = \Lib\HttpClient::sendRequest(
			'https://www.cba.am/_layouts/rssreader.aspx?rss=280F57B8-763C-4EE4-90E0-8136C13E47DA',
			'GET',
			''
		);

		$xml = simplexml_load_string($data['result']);

		if ($xml === false) {
			\System\Logger::Log('Unable to fetch XML Data from CBA.', \System\Logger::ERROR);
			return false;
		}

		\Lib\HttpClient::sendRequestAsync(
			\System\Config::get()['DataReceptionAccess']['url'] . '/exchange-rate',
			'POST',
			$data['result'],
			[
				\System\Config::get()['DataReceptionAccess']['Auth']['DRAuthKey'] => \System\Config::get()['DataReceptionAccess']['Auth']['DRAuthKeyValue'],
				'Content-Type' => 'application/xml; charset=utf-8'
			]
		);

		return true;

	}

}
