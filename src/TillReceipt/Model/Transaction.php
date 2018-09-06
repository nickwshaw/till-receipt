<?php
namespace Willshaw\WorldFirst\TillReceipt\Model;

use Willshaw\WorldFirst\TillReceipt\ProductInterface;

class Transaction
{
	/**
	 * @var $products Product[]
	 */
	protected $products = array();

	/**
	 * @param ProductInterface $product
	 */
	public function addProduct(ProductInterface $product) {
		$this->products[] = $product;
	}

	/**
	 * @return Product[]
	 */
	public function getProducts() {
		return $this->products;
	}

	/**
	 * Get NET value of transaction
	 * @return float
	 */
	public function getSubTotal() {
		return (float)bcsub((string)$this->getGrandTotal(), (string)$this->getDiscounts(), 4);
	}

	/**
	 * Total discounts for transaction
	 * @return float
	 */
	public function getDiscounts() {
		$total = '0';
		foreach ($this->products as $product) {
			$total = bcadd((string)$product->getDiscount(), $total, 4);
		}
		return (float)$total;
	}

	/**
	 * Get Gross value of transaction
	 * @return float
	 */
	public function getGrandTotal() {
		$total = '0';
		foreach ($this->products as $product) {
			$total = bcadd((string)$product->getPrice(), $total, 4);
		}
		return (float)$total;
	}
}