<?php

namespace BeycanPress\CryptoPay\WooCommerce;

use \Beycan\LicenseVerifier;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Plugin;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Setting;
use \BeycanPress\CryptoPay\WooCommerce\PluginHero\Helpers;

class Settings extends Setting
{
    use Helpers;
    
    /**
     * @var array
     */
    public static $customTokens = [];

    /**
     * @var array
     */
    public static $tokenDiscounts = [];

    /**
     * @var array
     */
    public static $acceptedChains = [];

    /**
     * @var array
     */
    public static $acceptedWallets = [];

    public function __construct()
    {
        $prefix = $this->settingKey;
        $parent = $this->pages->TransactionList->slug;

        add_action("csf_{$prefix}_save_after", function($data, $opt) {
            if (isset($opt->errors['license'])) self::deleteLicense();
        }, 10, 2);

        parent::__construct($prefix, esc_html__('Settings', 'walogin'), $parent);

        self::createSection(array(

            'id'     => 'general_options', 
            'title'  => esc_html__('General options', 'cryptopay'),
            'icon'   => 'fa fa-cog',
            'fields' => array(
                array(
                    'id'      => 'dds',
                    'title'   => esc_html__('Data deletion status', 'cryptopay'),
                    'type'    => 'switcher',
                    'default' => false,
                    'help'    => esc_html__('This setting is passive come by default. You enable this setting. All data created by the plug-in will be deleted while removing the plug-in.', 'cryptopay')
                ),
                array(
                    'id'      => 'payment_address',
                    'title'   => esc_html__('Wallet address', 'cryptopay'),
                    'type'    => 'text',
                    'help'    => esc_html__('The account address to which the payments will be transferred. (BEP20, ERC20, MetaMask, Trust Wallet, Binance Wallet )', 'cryptopay'),
                    'sanitize' => function($val) {
						return sanitize_text_field($val);
					},
                    'validate' => function($val) {
                        $val = sanitize_text_field($val);
                        if (empty($val)) {
                            return esc_html__('Wallet address cannot be empty.', 'cryptopay');
                        } elseif (strlen($val) < 42 || strlen($val) > 42) {
                            return esc_html__('Wallet address must consist of 42 characters.', 'cryptopay');
                        }
                    }
                ),
                array(
                    'id'      => 'payment_complete_order_status',
                    'title'   => esc_html__('Payment complete order status', 'cryptopay'),
                    'type'    => 'select',
                    'help'    => esc_html__('The status to apply for order after payment is complete.', 'cryptopay'),
                    'options' => [
                        'wc-completed' => esc_html__('Completed', 'cryptopay'),
                        'wc-processing' => esc_html__('Processing', 'cryptopay')
                    ],
                    'default' => 'wc-completed',
                ),
                array(
                    'id'      => 'only_logged_in_user',
                    'title'   => esc_html__('Only logged in users can pay', 'cryptopay'),
                    'type'    => 'switcher',
                    'help'    => esc_html__('Even if a user enters the CryptoPay payment page, if they are not logged in, CryptoPay will not work at all.', 'cryptopay'),
                    'default' => false,
                ),
            )
        ));

        self::createSection(array(
            'id'     => 'wallets_menu', 
            'title'  => esc_html__('Accepted wallets', 'cryptopay'),
            'icon'   => 'fas fa-wallet',
            'fields' => array(
                array(
                    'id'     => 'acceptedWallets',
                    'type'   => 'fieldset',
                    'title'  => esc_html__('Wallets', 'cryptopay'),
                    'help'   => esc_html__('Specify the wallets you want to accept payments from.', 'cryptopay'),
                    'fields' => array(
                        array(
                            'id'      => 'metamask',
                            'title'   => esc_html('MetaMask'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'trustwallet',
                            'title'   => esc_html('Trust Wallet'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'binancewallet',
                            'title'   => esc_html('Binance Wallet'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'walletconnect',
                            'title'   => esc_html('WalletConnect'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                    ),
                    'validate' => function($val) {
                        foreach ($val as $value) {
                            if ($value) {
                                break;
                            } else {
                                return esc_html__('You must activate at least one wallet!', 'cryptopay');
                            }
                        }
                    }
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'networks', 
            'title'  => esc_html__('Accepted networks', 'cryptopay'),
            'icon'   => 'fa fa-link',
            'fields' => array(
                array(
                    'id'      => 'testnets',
                    'title'   => esc_html__('Testnets', 'cryptopay'),
                    'type'    => 'switcher',
                    'help'    => esc_html__('When you activate this setting, predefined testnets are activated.', 'cryptopay'),
                    'default' => false,
                ),
                array(
                    'id'      => 'accepted_chains',
                    'title'   => esc_html__('Accepted networks', 'cryptopay'),
                    'type'    => 'group',
                    'help'    => esc_html__('Add the blockchain networks you accept to receive payments.', 'cryptopay'),
                    'button_title' => esc_html__('Add new', 'cryptopay'),
                    'default' => [
                        [
                            'name' =>  'Main Ethereum Network',
                            'rpc_url' =>  'https://mainnet.infura.io/v3/9aa3d95b3bc440fa88ea12eaa4456161',
                            'id' =>  1,
                            'explorer_url' =>  'https://etherscan.io/',
                            'active' => true,
                            'native_currency' => [
                                'active' =>  true,
                                'symbol' =>  'ETH',
                                'decimals' =>  18,
                                'image' => $this->getImageUrl('eth.png'),
                            ],
                            'currencies' => [
                                [ 
                                    'symbol' =>  'USDT',
                                    'address' =>  '0xdac17f958d2ee523a2206206994597c13d831ec7',
                                    'image' =>  $this->getImageUrl('usdt.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'USDC',
                                    'address' =>  '0xa0b86991c6218b36c1d19d4a2e9eb0ce3606eb48',
                                    'image' =>  $this->getImageUrl('usdc.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'BUSD',
                                    'address' =>  '0x4Fabb145d64652a948d72533023f6E7A623C7C53',
                                    'image' =>  $this->getImageUrl('busd.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'DAI',
                                    'address' =>  '0x6b175474e89094c44da98b954eedeac495271d0f',
                                    'image' =>  $this->getImageUrl('dai.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'BNB',
                                    'address' =>  '0xB8c77482e45F1F44dE1745F52C74426C631bDD52',
                                    'image' =>  $this->getImageUrl('bnb.png'),
                                    'active' => false
                                ],
                            ]
                        ],
                        [
                            'name' =>  'Binance Smart Chain',
                            'rpc_url' =>  'https://bsc-dataseed.binance.org/',
                            'id' =>  56,
                            'explorer_url' =>  'https://bscscan.com/',
                            'active' => true,
                            'native_currency' => [
                                'active' =>  true,
                                'symbol' =>  'BNB',
                                'decimals' =>  18,
                                'image' => $this->getImageUrl('bnb.png'),
                            ],
                            'currencies' => [
                                [ 
                                    'symbol' =>  'BUSD',
                                    'address' =>  '0xe9e7cea3dedca5984780bafc599bd69add087d56',
                                    'image' =>  $this->getImageUrl('busd.png'),
                                    'active' => true
                                ],
                                [
                                    'symbol' =>  'USDT',
                                    'address' =>  '0x55d398326f99059ff775485246999027b3197955',
                                    'image' =>  $this->getImageUrl('usdt.png'),
                                    'active' => true
                                ],
                                [
                                    'symbol' =>  'USDC',
                                    'address' =>  '0x8ac76a51cc950d9822d68b83fe1ad97b32cd580d',
                                    'image' =>  $this->getImageUrl('usdc.png'),
                                    'active' => true
                                ],
                                [
                                    'symbol' =>  'DAI',
                                    'address' =>  '0x1af3f329e8be154074d8769d1ffa4ee058b1dbc3',
                                    'image' =>  $this->getImageUrl('dai.png'),
                                    'active' => true
                                ],
                                [
                                    'symbol' =>  'ETH',
                                    'address' =>  '0x2170ed0880ac9a755fd29b2688956bd959f933f8',
                                    'image' =>  $this->getImageUrl('eth.png'),
                                    'active' => false
                                ],
                                [
                                    'symbol' =>  'LTC',
                                    'address' =>  '0x4338665cbb7b2485a8855a139b75d5e34ab0db94',
                                    'image' =>  $this->getImageUrl('ltc.png'),
                                    'active' => false
                                ],
                                [
                                    'symbol' =>  'DOGE',
                                    'address' =>  '0xba2ae424d960c26247dd6c32edc70b295c744c43',
                                    'image' =>  $this->getImageUrl('doge.png'),
                                    'active' => false
                                ]
                            ]
                        ],
                        [
                            'name' =>  'Avalanche Network',
                            'rpc_url' =>  'https://api.avax.network/ext/bc/C/rpc',
                            'id' =>  43114,
                            'explorer_url' =>  'https://cchain.explorer.avax.network/',
                            'active' => true,
                            'native_currency' => [
                                'active' =>  true,
                                'symbol' =>  'AVAX',
                                'decimals' =>  18,
                                'image' => $this->getImageUrl('avax.png'),
                            ],
                            'currencies' => [
                                [ 
                                    'symbol' =>  'USDT',
                                    'address' =>  '0xde3a24028580884448a5397872046a019649b084',
                                    'image' =>  $this->getImageUrl('usdt.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'DAI',
                                    'address' =>  '0xba7deebbfc5fa1100fb055a87773e1e99cd3507a',
                                    'image' =>  $this->getImageUrl('dai.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'ETH',
                                    'address' =>  '0xf20d962a6c8f70c731bd838a3a388D7d48fA6e15',
                                    'image' =>  $this->getImageUrl('eth.png'),
                                    'active' => true
                                ],
                            ]
                        ],
                        [
                            'name' =>  'Polygon Mainnet',
                            'rpc_url' =>  'https://rpc-mainnet.matic.network',
                            'id' =>  137,
                            'explorer_url' =>  'https://polygonscan.com/',
                            'active' => true,
                            'native_currency' => [
                                'active' =>  true,
                                'symbol' =>  'MATIC',
                                'decimals' =>  18,
                                'image' => $this->getImageUrl('matic.png'),
                            ],
                            'currencies' => [
                                [ 
                                    'symbol' =>  'USDT',
                                    'address' =>  '0xc2132d05d31c914a87c6611c10748aeb04b58e8f',
                                    'image' =>  $this->getImageUrl('usdt.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'USDC',
                                    'address' =>  '0x2791bca1f2de4661ed88a30c99a7a9449aa84174',
                                    'image' =>  $this->getImageUrl('usdc.png'),
                                    'active' => true
                                ],
                                [ 
                                    'symbol' =>  'DAI',
                                    'address' =>  '0x8f3Cf7ad23Cd3CaDbD9735AFf958023239c6A063',
                                    'image' =>  $this->getImageUrl('dai.png'),
                                    'active' => true
                                ],
                            ]
                        ]
                    ],
                    'sanitize' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => &$value) {
                                $value['name'] = sanitize_text_field($value['name']);
                                $value['rpc_url'] = sanitize_text_field($value['rpc_url']);
                                $value['id'] = absint($value['id']);
                                $value['explorer_url'] = sanitize_text_field($value['explorer_url']);
                                $value['native_currency']['symbol'] = strtoupper(sanitize_text_field($value['native_currency']['symbol']));
                                $value['native_currency']['decimals'] = absint($value['native_currency']['decimals']);
                                $value['native_currency']['image'] = sanitize_text_field($value['native_currency']['image']);
                                if (isset($value['currencies'])) {
                                    foreach ($value['currencies'] as $key => &$currency) {
                                        $currency['symbol'] = strtoupper(sanitize_text_field($currency['symbol']));
                                        $currency['address'] = sanitize_text_field($currency['address']);
                                        $currency['image'] = sanitize_text_field($currency['image']); 
                                    }
                                }
                            }
                        }

                        return $val;
                    },
                    'validate' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => $value) {
                                if (empty($value['name'])) {
                                    return esc_html__('Network name cannot be empty.', 'cryptopay');
                                } elseif (empty($value['rpc_url'])) {
                                    return esc_html__('Network RPC URL cannot be empty.', 'cryptopay');
                                } elseif (empty($value['id'])) {
                                    return esc_html__('Chain ID cannot be empty.', 'cryptopay');
                                } elseif (empty($value['explorer_url'])) {
                                    return esc_html__('Explorer URL cannot be empty.', 'cryptopay');
                                } elseif (empty($value['native_currency']['symbol'])) {
                                    return esc_html__('Native currency symbol cannot be empty.', 'cryptopay');
                                } elseif (empty($value['native_currency']['decimals'])) {
                                    return esc_html__('Native currency Decimals cannot be empty.', 'cryptopay');
                                } elseif (empty($value['native_currency']['image'])) {
                                    return esc_html__('Native currency Image cannot be empty.', 'cryptopay');
                                } elseif (!isset($value['currencies'])) {
                                    return esc_html__('You must add at least one currency!', 'cryptopay');
                                } elseif (isset($value['currencies'])) {
                                    foreach ($value['currencies'] as $key => $currency) {
                                        if (empty($currency['symbol'])) {
                                            return esc_html__('Currency symbol cannot be empty.', 'cryptopay');
                                        } elseif (empty($currency['address'])) {
                                            return esc_html__('Currency contract address cannot be empty.', 'cryptopay');
                                        } elseif (strlen($currency['address']) < 42 || strlen($currency['address']) > 42) {
                                            return esc_html__('Currency contract address must consist of 42 characters.', 'cryptopay');
                                        } elseif (empty($currency['image'])) {
                                            return esc_html__('Currency image cannot be empty.', 'cryptopay');
                                        }  
                                    }
                                }
                            }
                        } else {
                            return esc_html__('You must add at least one blockchain network!', 'cryptopay');
                        }
                    },
                    'fields'    => array(
                        array(
                            'title' => esc_html__('Network name', 'cryptopay'),
                            'id'    => 'name',
                            'type'  => 'text'
                        ),
                        array(
                            'title' => esc_html__('Network RPC URL', 'cryptopay'),
                            'id'    => 'rpc_url',
                            'type'  => 'text',
                        ),
                        array(
                            'title' => esc_html__('Chain ID', 'cryptopay'),
                            'id'    => 'id',
                            'type'  => 'number'
                        ),
                        array(
                            'title' => esc_html__('Explorer URL', 'cryptopay'),
                            'id'    => 'explorer_url',
                            'type'  => 'text'
                        ),
                        array(
                            'id'      => 'active',
                            'title'   => esc_html__('Active/Passive', 'cryptopay'),
                            'type'    => 'switcher',
                            'help'    => esc_html__('Get paid in this network?', 'cryptopay'),
                            'default' => true,
                        ),
                        array(
                            'id'     => 'native_currency',
                            'type'   => 'fieldset',
                            'title'  => esc_html__('Native currency', 'cryptopay'),
                            'fields' => array(
                                array(
                                    'id'      => 'active',
                                    'title'   => esc_html__('Active/Passive', 'cryptopay'),
                                    'type'    => 'switcher',
                                    'help'    => esc_html__('Get paid in native currency?', 'cryptopay'),
                                    'default' => true,
                                ),
                                array(
                                    'id'    => 'symbol',
                                    'type'  => 'text',
                                    'title' => esc_html__('Symbol', 'cryptopay')
                                ),
                                array(
                                    'id'    => 'decimals',
                                    'type'  => 'number',
                                    'title' => esc_html__('Decimals', 'cryptopay')
                                ),
                                array(
                                    'title' => esc_html__('Image', 'cryptopay'),
                                    'id'    => 'image',
                                    'type'  => 'upload'
                                ),
                            ),
                        ),
                        array(
                            'id'        => 'currencies',
                            'type'      => 'group',
                            'title'     => esc_html__('Currencies', 'cryptopay'),
                            'button_title' => esc_html__('Add new', 'cryptopay'),
                            'fields'    => array(
                                array(
                                    'title' => esc_html__('Symbol', 'cryptopay'),
                                    'id'    => 'symbol',
                                    'type'  => 'text'
                                ),
                                array(
                                    'title' => esc_html__('Contract address', 'cryptopay'),
                                    'id'    => 'address',
                                    'type'  => 'text'
                                ),
                                array(
                                    'title' => esc_html__('Image', 'cryptopay'),
                                    'id'    => 'image',
                                    'type'  => 'upload'
                                ),
                                array(
                                    'id'      => 'active',
                                    'title'   => esc_html__('Active/Passive', 'cryptopay'),
                                    'type'    => 'switcher',
                                    'help'    => esc_html__('You can easily activate or deactivate Token without deleting it.', 'cryptopay'),
                                    'default' => true,
                                ),
                            ),
                        ),
                    ),
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'custom_token_values', 
            'title'  => esc_html__('Custom token values', 'cryptopay'),
            'icon'   => 'fa fa-money',
            'fields' => array(
                array(
                    'id'           => 'custom_tokens',
                    'type'         => 'group',
                    'title'        => esc_html__('Custom tokens', 'cryptopay'),
                    'button_title' => esc_html__('Add new', 'cryptopay'),
                    'help'         => esc_html__('You can assign values ​​corresponding to fiat currencies to your own custom tokens.', 'cryptopay'),
                    'sanitize' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => &$value) {
                                $value['symbol'] = strtoupper(sanitize_text_field($value['symbol']));
                                if (isset($value['fiat_moneys'])) {
                                    foreach ($value['fiat_moneys'] as $key => &$money) {
                                        $money['symbol'] = strtoupper(sanitize_text_field($money['symbol']));
                                        $money['value'] = floatval($money['value']);
                                    }
                                }
                            }
                        }
                        
                        return $val;
                    },
                    'validate' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => $value) {
                                if (empty($value['symbol'])) {
                                    return esc_html__('Symbol cannot be empty.', 'cryptopay');
                                } elseif (!isset($value['fiat_moneys'])) {
                                    return esc_html__('You must add at least one FIAT money value!', 'cryptopay');
                                } elseif (isset($value['fiat_moneys'])) {
                                    foreach ($value['fiat_moneys'] as $key => $money) {
                                        if (empty($money['symbol'])) {
                                            return esc_html__('FIAT money symbol cannot be empty.', 'cryptopay');
                                        } elseif (empty($money['value'])) {
                                            return esc_html__('FIAT money value cannot be empty.', 'cryptopay');
                                        }
                                    }
                                }
                            }
                        }
                    },
                    'fields' => array(
                        array(
                            'title' => esc_html__('Symbol', 'cryptopay'),
                            'id'    => 'symbol',
                            'type'  => 'text'
                        ),
                        array(
                            'id'           => 'fiat_moneys',
                            'type'         => 'group',
                            'title'        => esc_html__('FIAT Moneys', 'cryptopay'),
                            'button_title' => esc_html__('Add new', 'cryptopay'),
                            'fields'      => array(
                                array(
                                    'title' => esc_html__('Symbol', 'cryptopay'),
                                    'id'    => 'symbol',
                                    'type'  => 'text',
                                    'help'  => esc_html__('The symbol of the fiat currency you want to value (ISO Code)', 'cryptopay')
                                ),
                                array(
                                    'title' => esc_html__('Value', 'cryptopay'),
                                    'id'    => 'value',
                                    'type'  => 'number',
                                ),
                            ),
                        ),
                    ),
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'token_discounts_rates', 
            'title'  => esc_html__('Token discounts', 'cryptopay'),
            'icon'   => 'fa fa-percent',
            'fields' => array(
                array(
                    'id'           => 'token_discounts',
                    'type'         => 'group',
                    'title'        => esc_html__('Token discounts', 'cryptopay'),
                    'button_title' => esc_html__('Add new', 'cryptopay'),
                    'help'         => esc_html__('You can define shopping-specific discounts for tokens with the symbols of the tokens.', 'cryptopay'),
                    'sanitize' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => &$value) {
                                $value['symbol'] = strtoupper(sanitize_text_field($value['symbol']));
                                $value['discount_rate'] = floatval($value['discount_rate']);
                            }
                        }

                        return $val;
                    },
                    'validate' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => $value) {
                                if (empty($value['symbol'])) {
                                    return esc_html__('Symbol cannot be empty.', 'cryptopay');
                                } elseif (empty($value['discount_rate'])) {
                                    return esc_html__('Discount rate cannot be empty.', 'cryptopay');
                                }
                            }
                        }
                    },
                    'fields'      => array(
                        array(
                            'title' => esc_html__('Symbol', 'cryptopay'),
                            'id'    => 'symbol',
                            'type'  => 'text'
                        ),
                        array(
                            'title' => esc_html__('Discount rate (in %)', 'cryptopay'),
                            'id'    => 'discount_rate',
                            'type'  => 'number'
                        ),
                    ),
                ),
            ) 
        ));

        $converters = apply_filters(
            "CryptoPay/WooCommerce/Converters", 
            [
                'cryptocompare' => 'Default (CryptoCompare)',
            ]
        );

        $apiOptions = apply_filters(
            "CryptoPay/WooCommerce/ApiOptions", 
            []
        );

        self::createSection(array(
            'id'     => 'apis', 
            'title'  => esc_html__('API\'s', 'cryptopay'),
            'icon'   => 'fas fa-project-diagram',
            'fields' => array_merge(array(
                array(
                    'id' => 'otherConverterLinks',
                    'type' => 'content',
                    'content' => '<a href="https://1.envato.market/6bZ0Wq" target="_blank">'.esc_html__('Buy custom converter APIs', 'cryptopay').'</a>',
                    'title' => esc_html__('Buy custom converter APIs', 'cryptopay')
                ),
                array(
                    'id' => 'converterApi',
                    'type'  => 'select',
                    'title' => esc_html__('Converter API', 'cryptopay'),
                    'options' => $converters,
                    'default' => 'cryptocompare'
                ),
                array(
                    'id' => 'infuraProjectId',
                    'type'  => 'text',
                    'title' => esc_html__('Infura Project ID', 'cryptopay'),
                    'help'  => esc_html__('Please enter an infura project id for WalletConnect to work.', 'cryptopay'),
                    'sanitize' => function($val) {
                        return sanitize_text_field($val);
                    }
                )  
            ), $apiOptions)
        ));

        do_action("CryptoPay/WooCommerce/AddOnSettings");

        self::createSection(array(
            'id'     => 'license', 
            'title'  => esc_html__('License', 'cryptopay'),
            'icon'   => 'fa fa-key',
            'fields' => array(
                array(
                    'id'    => 'license',
                    'type'  => 'text',
                    'title' => esc_html__('License (Purchase code)', 'cryptopay'),
                    'sanitize' => function($val) {
                        return sanitize_text_field($val);
                    },
                    'validate' => function($val) {
                        $val = sanitize_text_field($val);
                        if (empty($val)) {
                            return esc_html__('License cannot be empty.', 'cryptopay');
                        } elseif (strlen($val) < 36 || strlen($val) > 36) {
                            return esc_html__('License must consist of 36 characters.', 'cryptopay');
                        }

                        /** @var object $data */
                        $data = LicenseVerifier::verify($val, Plugin::$instance->pluginKey);
                        if (!$data->success) {
                            return esc_html__($data->message . " - Error code: " . $data->errorCode, 'cryptopay');
                        }
                    }
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'backup', 
            'title'  => esc_html__('Backup', 'cryptopay'),
            'icon'   => 'fas fa-shield-alt',
            'fields' => array(
                array(
                    'type'  => 'backup',
                    'title' => esc_html__('Backup', 'cryptopay')
                ),
            ) 
        ));

        self::createSection(array(
            'id'     => 'addOns', 
            'title'  => esc_html__('Add-ons', 'cryptopay'),
            'icon'   => 'fas fa-puzzle-piece',
            'fields' => array(
                array(
                    'id' => 'cmcConverterApi',
                    'type' => 'content',
                    'content' => '<a href="https://1.envato.market/vnd5zv" target="_blank"><img src="https://s3.envato.com/files/405984447/screenshots/primary-1.png"></a>',
                    'title' => esc_html__('CMC converter API', 'cryptopay')
                ),
                array(
                    'id' => 'moralisConverterApi',
                    'type' => 'content',
                    'content' => '<a href="https://1.envato.market/RyVAPN" target="_blank"><img src="https://s3.envato.com/files/405982990/screenshots/primary-1.png"></a>',
                    'title' => esc_html__('Moralis converter API', 'cryptopay')
                ),
                array(
                    'id' => 'pancakeSwapConverterApi',
                    'type' => 'content',
                    'content' => '<a href="https://1.envato.market/dod2Kj" target="_blank"><img src="https://s3.envato.com/files/385415566/screenshots/primary-1.png"></a>',
                    'title' => esc_html__('PancakeSwap converter API', 'cryptopay')
                ),
            ) 
        ));
    }

    public static function deleteLicense(): void
    {
        $settings = Plugin::$instance->setting();
        if (isset($settings['license'])) {
            unset($settings['license']);
            update_option(Plugin::$instance->settingKey, $settings);
        }
    }

    public static function getTokenDiscounts() : array
    {
        $tokenDiscounts = Plugin::$instance->setting('token_discounts');

        if (!empty(self::$tokenDiscounts) || !is_array($tokenDiscounts)) {
            return self::$tokenDiscounts;
        }

        foreach ($tokenDiscounts as $key => $token) {
            if (!$token['symbol']) continue;
            $tokenSymbol = strtoupper($token['symbol']);
            self::$tokenDiscounts[$tokenSymbol] = floatval($token['discount_rate']);
        }

        return self::$tokenDiscounts;
    }

    public static function getCustomTokens() : array
    {
        $customTokens = Plugin::$instance->setting('custom_tokens');

        if (!empty(self::$customTokens) || !is_array($customTokens)) {
            return self::$customTokens;
        }

        foreach ($customTokens as $key => $token) {
            if (!$token['symbol']) continue;
            $tokenSymbol = strtoupper($token['symbol']);
            self::$customTokens[$tokenSymbol] = [];
            foreach ($token['fiat_moneys'] as $key => $fiatMoney) {
                $fiatMoneySymbol = strtoupper($fiatMoney['symbol']);
                self::$customTokens[$tokenSymbol][$fiatMoneySymbol] = floatval($fiatMoney['value']); 
            }
        }

        return self::$customTokens;
    }

    public static function getAcceptedWallets() : array
    {
		$acceptedWallets = Plugin::$instance->setting('acceptedWallets');

        if (!empty(self::$acceptedWallets) || !$acceptedWallets) {
            return self::$acceptedWallets;
        }
		
        self::$acceptedWallets = array_filter($acceptedWallets, function($val) {
            return $val;
        });

        return array_keys(self::$acceptedWallets);
    }

    public static function getAcceptedChains() : array
    {
		$acceptedChains = Plugin::$instance->setting('accepted_chains');

        if (!empty(self::$acceptedChains) || !$acceptedChains) {
            return self::$acceptedChains;
        }

        foreach ($acceptedChains as $key => $chain) {

            // Active/Passive control
            if (isset($chain['active']) && $chain['active'] != '1') continue;

            $id = intval($chain['id']);
            $hexId = '0x' . dechex($id);

            $currencies = [];

            if (isset($chain['native_currency']['active']) && $chain['native_currency']['active'] == '1') {
                unset($chain['native_currency']['active']);
                $chain['native_currency']['address'] = trim(strtoupper($chain['native_currency']['symbol']));
                $chain['native_currency']['symbol'] = trim(strtoupper($chain['native_currency']['symbol']));
                $chain['native_currency']['decimals'] = (int) $chain['native_currency']['decimals'];
                $currencies[$chain['native_currency']['symbol']] = $chain['native_currency'];
            }
            
            foreach ($chain['currencies'] as $key => $currency) {
                if (isset($currency['active']) && $currency['active'] == '1') {
                    unset($currency['active']);
                    $currency['symbol'] = trim(strtoupper($currency['symbol']));
                    $currency['address'] = trim(strtoupper($currency['address']));
                    $currencies[$currency['address']] = $currency;
                }
            }

            self::$acceptedChains[$hexId] = [
                'id' => $id,
                'hexId' => $hexId,
                'name' => $chain['name'],
                'rpcUrl' => $chain['rpc_url'],
                'explorerUrl' => $chain['explorer_url'],
                'nativeCurrency' => $chain['native_currency'],
                'currencies' => $currencies
            ];
        }
        
        return self::$acceptedChains;
    }
}