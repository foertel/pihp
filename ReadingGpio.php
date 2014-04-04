<?php
namespace PIHP\Base;

require_once('Gpio.php');

class ReadingGPIO extends GPIO {

	public function __construct($pin) {
		return parent::__construct($pin, 'in');
	}

	public function read() {
		$value = file_get_contents($this->path . 'value');
		return (bool)trim($value);
	}
}
