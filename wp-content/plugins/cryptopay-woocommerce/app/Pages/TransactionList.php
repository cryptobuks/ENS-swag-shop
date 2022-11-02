<?php 

namespace BeycanPress\CryptoPay\WooCommerce\Pages;

use \Beycan\WPTable\Table;
use \Beycan\MultiChain\Utils;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Page;
use \BeycanPress\CryptoPay\WooCommerce\Models\Transaction;
use \BeycanPress\CryptoPay\WooCommerce\Services\Verifier;

/**
 * Transaction list page
 */
class TransactionList extends Page
{   
    /**
     * Class construct
     * @return void
     */
    public function __construct()
    {
        parent::__construct([
            'pageName' => esc_html__('CryptoPay WooCommerce', 'cryptopay'),
            'subMenuPageName' => esc_html__('Transaction list', 'cryptopay'),
            'icon' => $this->getImageUrl('menu.png'),
            'subMenu' => true
        ]);
    }

    /**
     * @return void
     */
    public function page() : void
    {
        (new Verifier())->verifyPendingTransactions();

        $transaction = new Transaction();

        if (isset($_GET['id']) && $transaction->delete(['id' => absint($_GET['id'])])) {
            $this->notice(esc_html__('Successfully deleted!', 'cryptopay'), 'success', true);
        }

        $table = (new Table($transaction))
        ->setColumns([
            'transaction_id'   => esc_html__('Transaction id', 'cryptopay'),
            'chain_id'         => esc_html__('Chain id', 'cryptopay'),
            'chain_name'       => esc_html__('Chain name', 'cryptopay'),
            'used_wallet'      => esc_html__('Used wallet', 'cryptopay'),
            'payment_price'    => esc_html__('Payment price', 'cryptopay'),
            'order_id'         => esc_html__('Order id', 'cryptopay'),
            'order_price'      => esc_html__('Order price', 'cryptopay'),
            'discount'         => esc_html__('Discount', 'cryptopay'),
            'sender_address'   => esc_html__('Sender address', 'cryptopay'),
            'status'           => esc_html__('Status', 'cryptopay'),
            'created_at'       => esc_html__('Created at', 'cryptopay'),
            'delete'           => esc_html__('Delete', 'cryptopay')
        ])
        ->setOptions([
            'search' => [
                'id' => 'search-box',
                'title' => esc_html__('Search...', 'cryptopay')
            ]
        ])
        ->setOrderQuery(['created_at', 'desc'])
        ->setSortableColumns(['created_at'])
        ->addHooks([
            'transaction_id' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                $explorerUrl = rtrim($paymentInfo->usedChain->explorerUrl, '/');
                $url = $explorerUrl.'/tx/'.$transaction->transaction_id;
                return '<a href="'.esc_url($url).'" target="_blank">'.esc_html($transaction->transaction_id).'</a>';
            },
            'chain_id' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                return esc_html($paymentInfo->usedChain->id);
            },
            'chain_name' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                return esc_html($paymentInfo->usedChain->name);
            },
            'used_wallet' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                return isset($paymentInfo->usedWallet) ? esc_html($paymentInfo->usedWallet) : null;
            },
            'payment_price' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                $paymentPrice = Utils::toString($paymentInfo->paymentPrice, $paymentInfo->selectedCurrency->decimals);
                return esc_html($paymentPrice . " " . $paymentInfo->paymentCurrency);
            },
            'order_price' => function($transaction) {
                $order = unserialize($transaction->payment_info)->order;
                return esc_html($order->price . " " . $order->currency);
            },
            'discount' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                if (isset($paymentInfo->discountRate)) {
                    return esc_html__('Discount: ', 'cryptopay') . " " . $paymentInfo->discountRate . ' % <br><br>' . esc_html__('Real price: ', 'cryptopay') . " " . $paymentInfo->realPrice . " " . $paymentInfo->paymentCurrency;
                }
                return esc_html__('No discount', 'cryptopay');
            },
            'sender_address' => function($transaction) {
                $paymentInfo = unserialize($transaction->payment_info);
                $explorerUrl = rtrim($paymentInfo->usedChain->explorerUrl, '/');
                $url = $explorerUrl.'/address/'.$paymentInfo->senderAddress;
                return '<a href="'.esc_url($url).'" target="_blank">'.esc_html($paymentInfo->senderAddress).'</a>';
            },
            'status' => function($transaction) {
                if ($transaction->status == 'pending') {
                    return esc_html__('Pending', 'cryptopay');
                } elseif ($transaction->status == 'verified') {
                    return esc_html__('Verified', 'cryptopay');
                } elseif ($transaction->status == 'failed') {
                    return esc_html__('Failed', 'cryptopay');
                }
            },
            'delete' => function($transaction) {
                if (strtolower($transaction->status) == 'pending') return;
                return '<a class="button" href="'.$this->getCurrentUrl() . '&id=' . $transaction->id.'">'.esc_html__('Delete', 'cryptopay').'</a>';
            }
        ])->addHeaderElements(function() {
            return $this->view('pages/transaction-list/form');
        })
        ->createDataList(function(object $model, array $orderQuery, int $limit, int $offset) {
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $s = sanitize_text_field($_GET['status']);
                if (isset($dataList)) {
                    $dataList = array_filter($dataList, function($obj) use ($s) {
                        return $obj->status == $s;
                    });
                    $dataListCount = count($dataList);
                } else {
                    $dataList = $model->findBy([
                        'status' => $s,
                    ], $orderQuery, $limit, $offset);
                    $dataListCount = $model->getCount([
                        'status' => $s,
                    ]);
                }

                return [$dataList, $dataListCount];
            } 

            return null;
        });

        $this->viewEcho('pages/transaction-list/index', [
            'table' => $table
        ]);
    }

}