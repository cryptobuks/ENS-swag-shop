<section class="cryptopay-woocommerce-woocommerce-order-details">
    <h2 class="woocommerce-order-details__title"><?php echo esc_html__('CryptoPay payment details', 'cryptopay'); ?></h2>
    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
        <tr>
            <th scope="row">
                <?php echo esc_html__('Price: ', 'cryptopay'); ?>
            </th>
            <td>
                <?php echo esc_html($paymentPrice); ?> <?php echo esc_html($paymentInfo->paymentCurrency); ?>
            </td>
        </tr>
        <?php if (isset($paymentInfo->discountRate)) : ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html__('Discount: ', 'cryptopay'); ?>
                </th>
                <td>
                    <?php echo esc_html($paymentInfo->discountRate . ' %'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo esc_html__('Real price: ', 'cryptopay'); ?>
                </th>
                <td>
                    <?php echo esc_html($paymentInfo->realPrice . " " . $paymentInfo->paymentCurrency); ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th scope="row">
                <?php echo esc_html__('Status: ', 'cryptopay'); ?>
            </th>
            <td>
                <?php
                    if ($transaction->status == 'pending') {
                        echo esc_html__('Pending', 'cryptopay');
                    } elseif ($transaction->status == 'verified') {
                        echo esc_html__('Verified', 'cryptopay');
                    } elseif ($transaction->status == 'failed') {
                        echo esc_html__('Failed', 'cryptopay');
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php echo esc_html__('Transaction id: ', 'cryptopay'); ?>
            </th>
            <td>
                <?php
                    $explorerUrl = rtrim($paymentInfo->usedChain->explorerUrl, '/');
                ?>
                <a href="<?php echo esc_url($explorerUrl.'/tx/'.$transaction->transaction_id); ?>" target="_blank" style="word-break: break-word">
                    <?php echo esc_html($transaction->transaction_id); ?>
                </a>
            </td>
        </tr>
    </table>
</section>