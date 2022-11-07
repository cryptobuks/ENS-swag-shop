<!-- start:text -->
<div class="text <?php echo ( (has_term(27, 'product_cat'))? 'has-domain-product' : 'no-domain-product' );?>" id="product-desc-<?php echo $postID; ?>">

    <div class="intro">
        <h2><?php the_title(); ?></h2>
        <?php the_excerpt(); ?>
    </div>

    <div class="collapse" id="collapseDesc">
        <div class="full-desc">
            <?php the_content(); ?>
        </div>
    </div>

    <div class="button-more-holder">
        <button onclick="changeExpandTitle(this);" class="btn btn-expand" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseDesc" aria-expanded="false" aria-controls="collapseDesc">
            <span class="title">Read more</span>
        </button>
    </div>

    <div class="product-cart-options-holder">

        <div class="row product-cart-options g-0">

            <?php if( isset($_SESSION['ascii_status']) && trim($_SESSION['ascii_status']) != '' && has_term(27, 'product_cat') ): ?>
                <div class="col-12 ascii_notice-over"><div class="ascii_notice">Only names shorter than 13 characters (excl. “.eth”) and ASCII characters supported.</div></div>
            <?php endif; ?>

            <?php if( has_term(26, 'product_cat') && has_term(27, 'product_cat') ): ?>
                <div class="col-auto domain-select column me-2">
                    <select name="domain" id="domain-<?php echo $postID; ?>" is="ms-dropdown" data-enable-auto-filter="true" data-child-height="340">
                        <option value="0" data-image="<?php echo TEMPLATEDIR; ?>/images/default-avatar.svg">
                            nick.eth
                        </option>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="domain" id="domain-<?php echo $postID; ?>" value="no-domain">
            <?php endif; ?>

            <div class="col-auto quantity-select column">
                <select name="quantity" id="quantity" is="ms-dropdown">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>
            </div>

            <div class="col-auto connect-column column">
                <button onclick="return clickConnectButton();" type="button"><?php _e('Connect Wallet', 'template'); ?></button>
            </div>

        </div>

        <?php if (isset($singleProduct) && $singleProduct): /* It is single product view list added domains */ ?>

            <?php echo getProductAlreadyItemsInCartHtml($postID); ?>

        <?php endif; ?>

    </div>

    <!-- start:price-delivery -->
    <div class="price-delivery row g-0">

        <div class="col-12 col-md-4 column">
            <div class="product-price">
                <?php $product = wc_get_product($postID); ?>
                $<?php echo number_format($product->get_price(), '2', '.', ','); ?>
            </div>
        </div>

        <?php
        global $wpdb;

        // Get variation id
        $variantID = 7854;
        $postMetaData = $wpdb->get_row(
            "
                SELECT id, variant_id FROM wenp_ens_product_meta
                WHERE post_id='{$postID}' LIMIT 1
        ");
        if (isset($postMetaData->id) && $postMetaData->id > 0) {
            $variantID = $postMetaData->variant_id;
        }

        $date = date('F') . ' ' . date("d", strtotime("+7days")) . '-' . date("d", strtotime("+13days"));
        $rate = '6.99';
        $rates = getShippingRatesForProduct($variantID, 'Broadway 1', 'US', 'NY', 10012);

        if($rates){
            $dateArray = explode(':', $rates->name);
            if(sizeof($dateArray) > 0){
                $date = ltrim($dateArray[1]);
                $date = substr($date, 0, -2);
            }
            $rate = $rates->rate;
        }
        ?>

        <div class="col-12 col-md-8 column">
            <div class="ps-3">
                <div class="delivery">
                    <div class="title">Estimated Delivery To <strong>USA</strong></div>
                    <div class="date"><?php echo $date; ?></div>
                    <div class="delivery-price">$<?php echo $rate; ?></div>
                    <div class="text">You can modify the shipping options at checkout</div>
                </div>
            </div>
        </div>

    </div>
    <!-- end:price-delivery -->

    <!-- start:button-submit -->
    <div class="button-submit">
        <button class="btn-submit" type="submit"
                onclick="return addProductToCart(<?php echo $postID; ?>);"><?php _e('Add to cart', 'template'); ?></button>
    </div>
    <!-- end:button-submit -->

</div>
<!-- end:text -->