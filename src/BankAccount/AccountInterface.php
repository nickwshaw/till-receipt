<?php
namespace Willshaw\WorldFirst\BankAccount;

interface AccountInterface
{
	public function openAccount();
	public function closeAccount();
	public function getAccountStatus();
	public function getBalance();
	public function displayBalance();
	public function depositFunds($amount);
	public function withdrawFunds($amount);
	public function setOverdraft($amount);
	public function getOverdraft();

}