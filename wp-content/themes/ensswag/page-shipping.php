<?php
/**
 * Template Name: Shipping
 */


?>
<?php get_header(); ?>

    <!-- start:shipping-header -->
    <div class="shipping-header">

        <div class="image-holder position-relative">

            <img src="<?php echo TEMPLATEDIR; ?>/images/shipping-bg-over.png" alt="" class="img-responsive">

            <!-- start:over -->
            <div class="over position-absolute">

                <!-- start:container -->
                <div class="container">

                    <!-- start:text -->
                    <div class="text">

                        <h1>The best shipping rates for print on demand services</h1>

                        <p>Set your own pricing strategy for your store or find out the shipping price for your personal
                            orders</p>

                    </div>
                    <!-- end:text -->

                </div>
                <!-- end:container -->

            </div>
            <!-- end:over -->

        </div>

    </div>
    <!-- end:shipping-header -->

    <!-- start:content -->
    <div class="content content-shipping pt-4">

        <!-- start:container -->
        <div class="container">

            <!-- start:text -->
            <div class="text-content">

                <h2><span>Find out the shipping rates for your region</span></h2>

                <p>
                    Enter the basic information, your address, select the country, state and enter the zip code. And for the end select product that shipping rate you want to see.
                </p>

                <?php
                global $woocommerce;
                $countries_obj = new WC_Countries();
                $countries = $countries_obj->__get('countries');

                $states_us = $countries_obj->get_states('US');
                $states_ca = $countries_obj->get_states('CA');
                $states_au = $countries_obj->get_states('AU');
                ?>

                <form id="calculate-shipping" action="">
                    <input type="hidden" id="calculate-shipping-nonce" name="calculate-shipping-nonce" value="<?php echo wp_create_nonce('calculate_shipping_cost'); ?>" autocomplete="off">
                    <input type="hidden" name="cshsp-check" id="cshsp-check" value="0" autocomplete="off">
                    <div class="row">
                        <div class="col-12 column">
                            <input type="hidden" name="address" id="address" class="form-input" value="Broadway 1">
                        </div>
                        <div class="col-12 column">
                            <select name="country" id="country" class="country_select" onchange="changeStateOptions(this.value);" data-live-search="true">
                                <?php foreach ($countries as $key => $country): ?>
                                    <?php $selectedCountry = ($key == 'US') ? 'selected="selected"' : ''; ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selectedCountry; ?>><?php echo $country; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 column column-state default-state" style="display: none;">
                            <input type="text" name="state" id="state" class="form-input" placeholder="State">
                        </div>
                        <div class="col-12 column column-state state-us">
                            <select name="state_us" id="state_us">
                                <?php foreach ($states_us as $key => $state): ?>
                                    <?php $selectedState = ($key == 'NY') ? 'selected="selected"' : ''; ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selectedState; ?>><?php echo $state; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 column column-state state-ca" style="display: none;">
                            <select name="state_ca" id="state_ca">
                                <?php foreach ($states_ca as $key => $state): ?>
                                    <?php $selectedState = ($key == 'US') ? 'selected="selected"' : ''; ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selectedState; ?>><?php echo $state; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 column column-state state-au" style="display: none;">
                            <select name="state_au" id="state_au">
                                <?php foreach ($states_au as $key => $state): ?>
                                    <?php $selectedState = ($key == 'US') ? 'selected="selected"' : ''; ?>
                                    <option value="<?php echo $key; ?>" <?php echo $selectedState; ?>><?php echo $state; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 column">
                            <input type="text" name="zip" id="zip" class="form-input" value="10012">
                        </div>
                        <div class="col-6 column">
                            <select name="product_variant" id="product_variant">
                                <?php

                                $queryArgs = array(
                                    'post_type' => 'product',
                                    'posts_per_page' => -1,
                                    'post_status' => 'publish',
                                );

                                $query = new WP_Query($queryArgs);
                                ?>

                                <?php if ($query->have_posts()): ?>
                                    <?php $queryCounter = 0; ?>

                                    <?php while ($query->have_posts()): ?>

                                        <?php
                                        // Prepare data
                                        $query->the_post();
                                        $postID = $variantID = get_the_ID();

                                        global $wpdb;

                                        // Get variation id
                                        $postMetaData = $wpdb->get_row(
                                            "
                                                SELECT id, variant_id FROM wenp_ens_product_meta
                                                WHERE post_id='{$postID}' LIMIT 1
                                        ");
                                        if (isset($postMetaData->id) && $postMetaData->id > 0) {
                                            $variantID = $postMetaData->variant_id;
                                        }
                                        ?>
                                        <option value="<?php echo $variantID; ?>"><?php the_title(); ?></option>

                                        <?php $queryCounter++; ?>

                                    <?php endwhile; ?>

                                <?php endif; ?>

                            </select>
                        </div>
                    </div>
                </form>

                <?php
                $rates = getShippingRatesForProduct(7854, 'Broadway 1', 'US', 'NY', 10012);
                ?>

                <div class="calculated">

                    <div class="row">
                        <div class="col-6">
                            <span class="title">Shipping *</span>
                        </div>
                        <div class="col-6">
                            <span class="title">Shipping Rate *</span>
                        </div>
                        <div class="col-12 line"></div>
                        <div class="col-6">
                            <span class="ship"><?php echo $rates->name; ?></span>
                        </div>
                        <div class="col-6">
                            <span class="rate">$<?php echo $rates->rate; ?></span>
                        </div>
                        <div class="col-12 line"></div>
                    </div>

                </div>

                <div class="under-form">
                    <p>
                        * These shipping times are estimates and do not constitute a guarantee. Estimates do not include
                        the time it takes to create the products. On checkout you see total cost for your cart
                        product(s).
                    </p>
                </div>

            </div>
            <!-- end:text -->

        </div>
        <!-- end:cart-inside -->

    </div>
    <!-- end:content -->

    <!-- start:content-shipping-under -->
    <div class="content-shipping-under">

        <!-- start:container -->
        <div class="container">

            <!-- start:shipping-under-inside -->
            <div class="shipping-under-inside">
                <h2 class="text-center"><span>We take your orders to their destination</span></h2>

                <div class="row g-5 justify-content-center align-items-center text-center">

                    <div class="col-12 col-md-4">
                        <div class="icon icon-management"></div>
                        <h3>Management</h3>
                        <p>
                            It takes 2-5 business days to make a product and process an order.
                        </p>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="icon icon-shipping"></div>
                        <h3>Shipping</h3>
                        <p>
                            The shipping time depends on the availability of the products and the place of delivery
                        </p>
                    </div>
                    
                    <div class="col-12 col-md-4">
                        <div class="icon icon-delivery"></div>
                        <h3>Delivery</h3>
                        <p>
                            After being processed and shipped, the orders are delivered to the final address
                        </p>
                    </div>

                </div>

            </div>
            <!-- end:shipping-under-inside -->

        </div>
        <!-- end:container -->

    </div>
    <!-- end:content-shipping-under -->

<?php get_footer(); ?>