<?php
namespace PIHP\Base;

class GPIO {

	const PATH = '/sys/class/gpio/';
	const GPIO = 'gpio';

	protected $pin;

	protected $path;

	protected $direction;

	public function __construct($pin, $direction) {
		$this->path = self::PATH . self::GPIO . $pin . '/';
		$this->pin = $pin;
		$this->direction = $direction;

		if (is_file($this->path . 'direction')) {
			throw new Exception('Pin ' . $this->pin . ' already taken!');
		}

		if (!is_writable(self::PATH . 'export')) {
			throw new \Exception('Can not export to ' . self::PATH . 'export. Write-protected.');
		}

		file_put_contents(self::PATH . 'export', $this->pin);
		file_put_contents($this->path . 'direction', $this->direction);
	}

	public function __destruct() {
		file_put_contents(self::PATH . 'unexport', $this->pin);
	}
}
