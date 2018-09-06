<?php
namespace Willshaw\WorldFirst\TillReceipt;

interface ProductInterface
{
	public function getName();
	public function getPrice();
	public function getDiscount();
}