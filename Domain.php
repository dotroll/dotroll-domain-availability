<?php

/**
 * domain, Domain class for whois search
 * 
 * @copyright Copyright (c) 2007 DotRoll Kft. (http://www.dotroll.com)
 * @author Zoltán Istvanovszki <zoltan.istvanovszki@dotroll.com>
 * @since 2016.09.20.
 * @package dotroll-domain-availability
 * @license https://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3
 */
class Domain {

	/**
	 * Domain name
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * Domain parts
	 * 
	 * @var array
	 */
	protected $parts = [];

	/**
	 * Domain TLD
	 * 
	 * @var string
	 */
	protected $tld;

	/**
	 * Domain SLD
	 * 
	 * @var string
	 */
	protected $sld;

	/**
	 * Doamin name validation with or without TLDs
	 * 
	 * @var boolean
	 */
	protected $valid = null;

	/**
	 * Searchable TLD's
	 * 
	 * @var array
	 */
	protected $search = ['.hu', '.com', '.net', '.org', '.eu'];

	/**
	 * Whois servers address
	 * 
	 * @var array
	 */
	protected $whoisServers = [];

	/**
	 * @const WHMCS path
	 */
	const WHMCS = 'https://admin.dotroll.com';

	/**
	 * Domain constructor
	 *
	 * @param string $domain Domain name
	 */
	public function __construct($domain) {
		$this->parts = \explode('.', \strtolower(\idn_to_ascii(\explode(' ', \trim($domain))[0])));
		$this->domain = \implode('.', $this->parts);
		$this->tld = \implode('.', \array_slice($this->parts, 1));
		if (!empty($this->tld)) {
			$this->tld = ".{$this->tld}";
		}
		$this->sld = $this->parts[0];
		$this->parseWhoisServer();
	}

	/**
	 * Fill up whoisServer array from plain text WHMCS file
	 */
	private function parseWhoisServer() {
		foreach (\array_merge(
			\json_decode(\file_get_contents(\dirname(__FILE__) . '/dist.whois.json'), true),
			\json_decode(\file_get_contents(\dirname(__FILE__) . '/whois.json'), true)
		) as $whois) {
			foreach (\explode(',', $whois['extensions']) as $tld) {
				$this->whoisServers[$tld] = [
					'server' => \substr($whois['uri'], 0, 9) == 'socket://' ? \substr($whois['uri'], 9) : $whois['uri'],
					'match' => $whois['available'],
					'type' => \substr($whois['uri'], 0, 9) == 'socket://' ? 'whois' : 'http'
				];
			}
		}
	}

	/**
	 * Check Tlds valid or not
	 * 
	 * @return boolean
	 */
	private function validate() {
		if ($this->valid === null) {
			$this->valid = true;
			if (!isset($this->parts[0]) || empty($this->parts[0]) || !\preg_match('/^[a-z0-9][-a-z0-9]+[a-z0-9]$/', $this->parts[0], $matches)) {
				$this->valid = false;
			}
			if ($this->valid === true && isset($this->parts[1]) && !isset($this->whoisServers[$this->tld])) {
				$this->valid = false;
			}
		}
		return $this->valid;
	}

	/**
	 * Search domain search across all TLD's
	 * 
	 * @return mixed
	 */
	public function search() {
		if ($this->validate() === true) {
			$result = [];
			if (!empty($this->tld) && !\in_array($this->tld, $this->search)) {
				\array_unshift($this->search, $this->tld);
			} elseif (!empty($this->tld)) {
				unset($this->search[\array_search($this->tld, $this->search)]);
				\array_unshift($this->search, $this->tld);
			}
			foreach ($this->search as $tld) {
				$result[] = $this->lookup($tld);
			}
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * Domain lookup
	 * 
	 * @param string $tld Lookup SLD with this TLD
	 * @return array
	 */
	private function lookup($tld) {
		if ($this->whoisServers[$tld]['type'] == 'whois') {
			$available = $this->whoisLookup($tld);
		} else {
			$available = $this->httpLookup($tld);
		}
		return [
			'domain' => \idn_to_utf8("{$this->sld}$tld"),
			'available' => $available,
			'link' => self::WHMCS . '/cart.php?a=add&domain=' . ($available === true ? 'register' : 'transfer') . '&sld=' . \idn_to_utf8($this->sld) . '&tld=' . \idn_to_utf8($tld)
		];
	}

	/**
	 * Domain http lookup
	 *
	 * @param string $tld Lookup SLD with this TLD
	 * @return boolean
	 */
	private function httpLookup($tld) {
		$ch = \curl_init();
		\curl_setopt($ch, \CURLOPT_URL, $this->whoisServers[$tld]['server'] . $this->sld . $tld);
		\curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, false);
		\curl_setopt($ch, \CURLOPT_TIMEOUT, 60);
		\curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
		\curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, false);
		\curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
		$data = \curl_exec($ch);
		if (!\curl_error($ch)) {
			\curl_close($ch);
			return \strpos($data, $this->whoisServers[$tld]['match']) === false ? false : true;
		}
		\curl_close($ch);
		return null;
	}

	/**
	 * Domain whois lookup
	 * 
	 * @param string $tld Lookup SLD with this TLD
	 * @return boolean
	 */
	private function whoisLookup($tld) {
		$fp = @\fsockopen($this->whoisServers[$tld]['server'], 43, $errno, $errstr, 10);
		if ($fp) {
			@\fputs($fp, "{$this->sld}$tld\r\n");
			@\socket_set_timeout($fp, 10);
			$data = '';
			while (!@\feof($fp)) {
				$data .= @\fread($fp, 4096);
			}
			@\fclose($fp);
			return \strpos($data, $this->whoisServers[$tld]['match']) === false ? false : true;
		}
		return null;
	}

}
