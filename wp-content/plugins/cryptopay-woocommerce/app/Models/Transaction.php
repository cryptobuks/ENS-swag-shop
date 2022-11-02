<?php

namespace BeycanPress\CryptoPay\WooCommerce\Models;

use Beycan\Moodel\AbstractModel;

/**
 * Transaction table model
 */
class Transaction extends AbstractModel 
{
    public $prefix = 'cryptopay_woocommerce';

    public $name = 'transaction';

    public function __construct()
    {
        parent::__construct([
            'transaction_id' => [
                'type' => 'string',
                'length' => 70,
                'index' => [
                    'type' => 'unique'
                ]
            ],
            'order_id' => [
                'type' => 'integer'
            ],
            'user_id' => [
                'type' => 'integer'
            ],
            'status' => [
                'type' => 'string',
                'length' => 10
            ],
            'payment_info' => [
                'type' => 'text'
            ],
            'created_at' => [
                'type' => 'timestamp',
                'default' => 'current_timestamp',
            ],
        ]);
    }

    
    public function search(string $text) : array
    {
        return $this->getResults(str_ireplace(
            '%s', 
            '%' . $this->db->esc_like($text) . '%', "
            SELECT * FROM {$this->tableName} 
            WHERE transaction_id LIKE '%s' 
            OR user_id LIKE '%s' 
            OR status LIKE '%s' 
            OR order_id LIKE '%s'
            OR payment_info LIKE '%s'
			ORDER BY id DESC
        "));
    }

    
    public function searchCount(string $text) : float
    {
        return (int) $this->getVar(str_ireplace(
            '%s', 
            '%' . $this->db->esc_like($text) . '%', "
            SELECT COUNT(id) FROM {$this->tableName} 
            WHERE transaction_id LIKE '%s' 
            OR user_id LIKE '%s' 
            OR status LIKE '%s' 
            OR order_id LIKE '%s'
            OR payment_info LIKE '%s'
        "));
    }
}