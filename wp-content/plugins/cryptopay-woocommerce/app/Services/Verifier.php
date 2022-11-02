<?php

namespace BeycanPress\CryptoPay\WooCommerce\Services;

use \Beycan\MultiChain\MultiChain;
use \Beycan\MultiChain\Transaction;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Helpers;
use \BeycanPress\CryptoPay\WooCommerce\Models\Transaction as TransactionModel;

class Verifier
{
    use Helpers;
    
    public function __construct()
    {
        $this->transaction = new TransactionModel();
    }
    
    /**
     * Beklemede olan işlemleri doğrular
     * @param int $userId
     * @return void
     */
    public function verifyPendingTransactions($userId = 0) : void
    {
        if ($userId == 0) {
            $transactions = $this->transaction->findBy([
                'status' => 'pending'
            ]);
        } else {
            $transactions = $this->transaction->findBy([
                'status' => 'pending',
                'user_id' => $userId
            ]);
        }
        
        if (empty($transactions)) return;

        $uniqureTransactions = [];
        foreach($transactions as $transaction) {
            $uniqureTransactions[$transaction->order_id] = $transaction;
        }

        $transactions = array_values($uniqureTransactions);

        foreach ($transactions as $key => $transaction) {
            
            $paymentInfo = unserialize($transaction->payment_info);

            try {
        
                $result = $this->verifyTransaction($paymentInfo);

                $paymentInfo->status = $result == 'failed' ? 'failed' : 'verified';

                if (
                    $paymentInfo->usedWallet == 'WalletConnect' && 
                    (time() - strtotime($transaction->created_at)) < 300
                    ) {
                    continue;
                }

                if ($result == 'pending') continue;

                if ($order = wc_get_order($paymentInfo->order->id)) {

                    if ($result == 'failed') {
                        $this->updateOrderAsFail($order, $transaction->id);
                    } else {
                        $this->updateOrderAsComplete($order, $transaction->id);
                    }

                    do_action(
                        'CryptoPay/WooCommerce/PaymentFinished', 
                        $this->userId, $order, $paymentInfo
                    );

                } else {
                    $this->transaction->update(['status' => 'failed'], ['id' => $transaction->id]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Siparişi tamamlandı veya işleme alındı olarak gönceller
     * @param object $order
     * @param string $transactionId
     * @return void
     */
    public function updateOrderAsComplete(object $order, string $transactionId) : void
    {
        if ($this->setting('payment_complete_order_status') == 'wc-completed') {
            $note = esc_html__('Your order is complete.', 'cryptopay');
        } else {
            $note = esc_html__('Your order is processing.', 'cryptopay');
        }

        $this->transaction->update(['status' => 'verified'], ['id' => $transactionId]);
        
        $order->payment_complete();

        $order->update_status($this->setting('payment_complete_order_status'), $note);

    }

    /**
     * Siparişi failed olarak günceller
     * @param object $order
     * @param string $transactionId
     * @return void
     */
    public function updateOrderAsFail(object $order, string $transactionId) : void
    {
        $this->transaction->update(['status' => 'failed'], ['id' => $transactionId]);

        $order->update_status('wc-failed', esc_html__('Payment not verified via Blockchain!', 'cryptopay'));
    }

    /**
     * @param object $paymentInfo
     * @return string
     */
    public function verifyTransaction(object $paymentInfo) : string
    {
        // Connect JSON-RPC api
        new MultiChain(
            $paymentInfo->usedChain->rpcUrl, 
            (array) $paymentInfo->usedChain->nativeCurrency,
            20
        );
        
        $transaction = new Transaction($paymentInfo->transactionId);

        $result = $transaction->verifyWithData(
            $paymentInfo->receiver, 
            $paymentInfo->paymentPrice, 
            $paymentInfo->selectedCurrency->address
        );

        return $result;
    }
}
