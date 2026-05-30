<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Security_headers
{
	public function set_headers()
	{
		$CI =& get_instance();
		$is_https = (
			(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
			|| (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
		);

		$CI->output
			->set_header('X-Frame-Options: SAMEORIGIN')
			->set_header('X-Content-Type-Options: nosniff')
			->set_header('Referrer-Policy: strict-origin-when-cross-origin')
			->set_header('Permissions-Policy: camera=(), microphone=(), geolocation=(self)')
			->set_header('X-Permitted-Cross-Domain-Policies: none');

		if ($is_https) {
			$CI->output->set_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
		}
	}
}
