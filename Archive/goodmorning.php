#!/usr/bin/php
<?php

class GPIO {

	const PATH = '/sys/class/gpio/';
	const GPIO = 'gpio';

	protected $pin;

	protected $path;

	public function __construct($pin, $direction = 'in') {
		$this->path = self::PATH . self::GPIO . $pin . '/';
		$this->pin = $pin;

		if (is_file($this->path . 'direction')) {
			var_dump($this->path . 'direction');
			throw new Exception('Pin ' . $this->pin . ' already taken!');
		}

		if (!is_writable(self::PATH . 'export')) {
			throw new \Exception('Can not export to ' . self::PATH . 'export. Write-protected.');
		}

		file_put_contents(self::PATH . 'export', $this->pin);
		file_put_contents($this->path . 'direction', 'out');
	}

	public function __destruct() {
		$this->off();
		file_put_contents(self::PATH . 'unexport', $this->pin);
	}

	public function on() {
		file_put_contents($this->path . 'value', 1);
	}

	public function off() {
		file_put_contents($this->path . 'value', 0);
	}
}

class LED extends \GPIO {
}

class GoodMorning {

	public function __construct($red, $yellow, $green) {
		$this->red = new \LED($red, 'out');
		$this->yellow = new \LED($yellow, 'out');
		$this->green = new \LED($green, 'out');
	}

	public function red() {
		$this->yellow->off();
		$this->green->off();
		$this->red->on();
	}

	public function yellow() {
		$this->red->off();
		$this->green->off();
		$this->yellow->on();
	}
	public function green() {
		$this->yellow->off();
		$this->red->off();
		$this->green->on();
	}
}

$goodMorning = new \GoodMorning(4, 17, 27);
$goodMorning->red();
echo PHP_EOL . PHP_EOL . 'Before 6 a.m.';
sleep(7);
echo PHP_EOL . PHP_EOL . 'It\'s 6 a.m. The kids are now allowed to play in their room.';
$goodMorning->yellow();
sleep(7);
echo PHP_EOL . PHP_EOL . 'It\'s 6.30 a.m. The kids are now allowed to wake up mum and dad.';
$goodMorning->green();
sleep(7);
echo PHP_EOL . PHP_EOL . 'It\'s 8 a.m., the kids are awake, the LEDs might sleep.' . PHP_EOL . PHP_EOL;
