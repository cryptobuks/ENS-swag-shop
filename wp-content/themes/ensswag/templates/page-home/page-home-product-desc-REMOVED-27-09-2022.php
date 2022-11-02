<!-- start:text -->
<div class="text">

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
            <span class="title">See More</span>
        </button>
    </div>

    <div class="row product-cart-options g-3 align-items-center">

        <div class="col-auto domain-select column">
            <select name="domain" id="domain-<?php echo $postID; ?>" is="ms-dropdown">
                <?php if (isset($_SESSION['user_ens_domains']) && sizeof($_SESSION['user_ens_domains']) > 0): ?>
                    <?php foreach ($_SESSION['user_ens_domains'] as $ENSDomain): ?>
                        <option value="<?php echo $ENSDomain->name; ?>"
                                data-image="<?php echo $ENSDomain->avatar_url; ?>"><?php echo $ENSDomain->name; ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="0" data-image="<?php echo TEMPLATEDIR; ?>/images/default-avatar.svg">
                        nick.eth
                    </option>
                <?php endif; ?>
            </select>
        </div>

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

        <div class="col-auto column mockup-column">
            <button type="button" class="btn btn-mockup"
                    onclick="createMockup(<?php echo $postID; ?>);"><?php _e('Create Mockup', 'template'); ?></button>
        </div>

    </div>

    <?php if (isset($singleProduct) && $singleProduct): /* It is single product view list added domains */ ?>

        <?php echo getProductAlreadyItemsInCartHtml($postID); ?>

    <?php endif; ?>

    <!-- start:price-delivery -->
    <div class="price-delivery row g-3">

        <div class="col-auto column">
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

        <div class="col-auto column">
            <div class="delivery">
                <div class="title">Estimated Delivery To <strong>USA</strong></div>
                <div class="date"><?php echo $date; ?></div>
                <div class="delivery-price">$<?php echo $rate; ?></div>
                <div class="text">You can modify the shipping options at checkout</div>
            </div>
        </div>

    </div>
    <!-- end:price-delivery -->

    <!-- start:button-submit -->
    <div class="button-submit">
        <button class="btn-submit" type="submit"
                onclick="return addProductToCart(<?php echo $postID; ?>);"><?php _e('Add to your bag', 'template'); ?></button>
    </div>
    <!-- end:button-submit -->

</div>
<!-- end:text -->