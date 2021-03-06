<?php namespace App\Withdraw\Handler\Form\WithdrawAdmin;

use Core\Support\AttributesAccess;
use App\Purse\PurseFactory as PurseFactory;
use App\Purse\Model\PurseModel as PurseModel;

class WithdrawAdminForm extends AttributesAccess
{
	/**
	 * Danh sach attribute bo sung
	 *
	 * @var array
	 */
	protected $additional = [];

	/**
	 * Key luu tru
	 */
	const STORAGE_KEY = 'withdraw_admin';


	/**
	 * Lay thong tin purse
	 *
	 * @return PurseModel|null
	 */
	public function getPurseAttribute()
	{
		if ( ! array_key_exists('purse', $this->additional))
		{
			$purse_number = $this->getAttribute('purse_number');

			$this->additional['purse'] = PurseFactory::purse()->findByNumber($purse_number);
		}

		return $this->additional['purse'];
	}

	/**
	 * Format du lieu
	 *
	 * @param $key
	 * @return string
	 */
	public function format($key)
	{
		switch($key)
		{
			case 'amount':
			{
				$amount = $this->getAttribute($key);

				$currency_id = $this->getAttribute('purse')->currency_id;

				return currency_format_amount($amount, $currency_id);
			}
		}
	}

	/**
	 * Luu du lieu
	 */
	public function save()
	{
		t('session')->set_userdata(static::STORAGE_KEY, $this->attributes);
	}

	/**
	 * Lay thong tin
	 *
	 * @return null|static
	 */
	public static function get()
	{
		$attributes = t('session')->userdata(static::STORAGE_KEY);

		return $attributes ? new static($attributes) : null;
	}

	/**
	 * Xoa du lieu
	 */
	public static function delete()
	{
		t('session')->unset_userdata(static::STORAGE_KEY);
	}

}