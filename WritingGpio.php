<?php
namespace PIHP\Base;

require_once('Gpio.php');

class WritingGPIO extends GPIO {

	public function __construct($pin) {
		return parent::__construct($pin, 'out');
	}

	public function on() {
		file_put_contents($this->path . 'value', 1);
	}

	public function off() {
		file_put_contents($this->path . 'value', 0);
	}

	public function __destruct() {
		$this->off();
		parent::__destruct();
	}
}
