<?php
namespace Willshaw\WorldFirst\TillReceipt\Model;
use Willshaw\WorldFirst\TillReceipt\ProductInterface;

class Product implements ProductInterface
{
	protected $price;
	protected $name;
	protected $discount;

	/**
	 * @param string $name
	 * @param float $price
	 * @param float $discount
	 */
	public function __construct($name, $price, $discount = 0.00) {
		$this->name = $name;
		$this->price = (float)$price;
		$this->discount = (float)$discount;
	}

	/**
	 * @return float
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return float
	 */
	public function getDiscount() {
		return $this->discount;
	}
}