<?php

namespace BeycanPress\CryptoPay\WooCommerce;

class Lang
{
    public static function get() : array
    {
        return [
            "connect" => esc_html__('Connect wallet', 'cryptopay'),
            "notDetected" => esc_html__('The wallet you want to connect to could not be detected!', 'cryptopay'),
            "waitingConnection" => esc_html__("Establishing connection please wait!", 'cryptopay'),
            "waiting" => esc_html__('Waiting...', 'cryptopay'),
            "walletTitle" => esc_html__('Please connect to a wallet to continue', 'cryptopay'),
            "connectionRefused" => esc_html__('Connection refused', 'cryptopay'),
            "notSupportedChain" => esc_html__('You are currently on an unsupported network, please try again after passing one of the supported networks', 'cryptopay'),
            "connectedWallet" => esc_html__('Connected wallet: ', 'cryptopay'),
            "activeChain" => esc_html__('Active chain: ', 'cryptopay'),
            "connectedAccount" => esc_html__('Connected account: ', 'cryptopay'),
            "orderPrice" => esc_html__('Order price: ', 'cryptopay'),
            "donateAmount" => esc_html__('Donate amount: ', 'cryptopay'),
            "donate" => esc_html__('Donate', 'cryptopay'),
            "pleaseEnterDonateAmount" => esc_html__('Please enter donation amount', 'cryptopay'),
            "confirmDonate" => esc_html__('Confirm donate', 'cryptopay'),
            "donateRejected" => esc_html__('Donate rejected', 'cryptopay'),
            "paymentPrice" => esc_html__('Payment price: ', 'cryptopay'),
            "paymentCurrency" => esc_html__('Payment currency: ', 'cryptopay'),
            "notCurrencySelected" => esc_html__('Not currency selected', 'cryptopay'),
            "notCurrencySelectedPay" => esc_html__('Please select the currency you want to pay in', 'cryptopay'),
            "notCurrencySelectedDonate" => esc_html__('Please select the currency you want to donate in', 'cryptopay'),
            "payWith" => esc_html__('Pay with', 'cryptopay'),
            "confirmPayment" => esc_html__('Confirm payment', 'cryptopay'),
            "cancel" => esc_html__('Cancel', 'cryptopay'),
            "confirm" => esc_html__('Confirm', 'cryptopay'),
            "insufficientBalance" => esc_html__('Insufficient balance!', 'cryptopay'),
            "paymentRejected" => esc_html__('Payment rejected', 'cryptopay'),
            "paymentFailed" => esc_html__('Payment not verified via Blockchain', 'cryptopay'),
            "unexpectedError" => esc_html__('An unexpected error has occurred', 'cryptopay'),
            "pleaseWait" => esc_html__('Please wait...', 'cryptopay'),
            "confirmWithWallet" => esc_html__('Confirm this action in your wallet', 'cryptopay'),
            "verifyTransaction" => esc_html__('Awaiting verification of payment via blockchain. Please do not close the page.', 'cryptopay'),
            "transactionId" => esc_html__('Transaction Id: ', 'cryptopay'),
            "notFoundAcceptedWallets" => esc_html__('No active wallets found, please contact the site administrator.', 'cryptopay'),
            "notFoundAcceptedChains" => esc_html__('No active chains found, please contact the site administrator.', 'cryptopay'),
            "completedDonate" => esc_html__('Donation sent successfully', 'cryptopay'),
            "completedPayment" => esc_html__('Payment completed successfully', 'cryptopay'),
            "disconnect" => esc_html__('Disconnect', 'cryptopay'),
            'supportedNetworks' => esc_html__('Supported networks', 'cryptopay'),
            'problemConvertingCurrency' => esc_html__('There was a problem converting currency!', 'cryptopay'),
            'intrinsicGasTooLow' => esc_html__('Intrinsic gas too low', 'cryptopay'),
            "chainChanged" => esc_html__("Network change detected page will reload.", 'cryptopay'),
            "notAcceptedCurrency" => esc_html__("Not accepted currency", 'cryptopay'),
            "timeOut" => esc_html__('The operation timed out, The page will reload.', 'cryptopay'),
            "discountRate" => esc_html__("{discountRate}% discount on purchases with this coin.", 'cryptopay'),
            "connectionFailed" => esc_html__("There was a problem establishing the connection!", 'cryptopay'),
            "notFoundInfuraId" => esc_html__("Infura id is required for WalletConnect", 'cryptopay'),
            'alreadyProcessing' => esc_html__("A connection request is already pending, check the wallet plugin!", 'cryptopay'),
            'transferAmount' => esc_html__('The transfer amount cannot be less than zero.', 'cryptopay')
        ];
    }

}