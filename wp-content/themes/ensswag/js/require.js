/**
 * Set up user crypto address
 *
 * @param address
 */
function setUpUserCryptoAddress(address) {
    if (address) {
        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=set_crypto_address&address=' + address + '&enshash=' + localStorage.getItem('ensHash'),
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {
                    jQuery('.connect-column').hide();
                    jQuery('.header-button').hide();
                }
            },
            error: function () {
            },
            complete: function () {
            }
        });
    }
}

/**
 * Set up user ENS domains
 *
 * @param address
 * @param domains
 */
function setUpUserENSDomains(address, domains) {

    if (address && domains) {

        // set up domains here to show it to the user because cache
        if(domains.length > 0){
            jQuery('.domain-select select').each(function () {
                const ddSelectInit = jQuery(this)[0].msDropdown;
                jQuery(this).children('option').each(function () {
                    jQuery(this).remove();
                });
                domains.forEach(domain => {
                    if(domain.hasAscii){
                        jQuery(this).append('<option value="' + domain.name + '" data-image="' + domain.avatar_url + '">' + domain.name + '</option>');
                    }
                });
                ddSelectInit.refresh();
            });
        }

        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            dataType: "json",
            data: {
                action: "set_ens_domains",
                address: address,
                domains: domains,
            },
            success: function (response_data) {

                if (response_data.total_cart !== undefined) {
                    jQuery('.btn-cart-top-count').text(response_data.total_cart);
                    // if cart is empty redirect user to homepage
                    if (response_data.total_cart == 0 && window.location.pathname === '/checkout/') {
                        window.location.href = "/";
                    }
                }

                if (response_data.status == 1) {

                    if (response_data.user_domains.length > 0) {

                        jQuery('.domain-select select').each(function () {

                            const ddSelect = jQuery(this)[0].msDropdown;

                            jQuery(this).children('option').each(function () {
                                jQuery(this).remove();
                            });

                            // Add domains to each select
                            response_data.user_domains.forEach(domain => {
                                jQuery(this).append('<option value="' + domain.name + '" data-image="' + domain.avatar_url + '">' + domain.name + '</option>');
                            });

                            ddSelect.refresh();

                        });

                        if (response_data.ascii_status && response_data.ascii_status.trim() !== '' && jQuery('.ascii_notice').length == 0) {
                            jQuery('.has-domain-product .product-cart-options').prepend('<div class="col-12 ascii_notice-over"><div class="ascii_notice">' + response_data.ascii_status + '</div></div>')
                        }

                    }

                }

                // default nick.eth domain
                if (response_data.status == 2 && response_data.default_domain?.name) {

                    if (response_data.ascii_status && response_data.ascii_status.trim() !== '' && jQuery('.ascii_notice').length == 0) {
                        jQuery('.has-domain-product .product-cart-options').prepend('<div class="col-12 ascii_notice-over"><div class="ascii_notice">' + response_data.ascii_status + '</div></div>')
                    }

                    jQuery('.domain-select select').each(function () {
                        const ddSelect = jQuery(this)[0].msDropdown;
                        jQuery(this).children('option').each(function () {
                            jQuery(this).remove();
                        });
                        jQuery(this).append('<option value="' + response_data.default_domain.value + '" data-image="' + response_data.default_domain.image + '">' + response_data.default_domain.name + '</option>');
                        ddSelect.refresh();
                    });

                }

            },
            error: function () {
            },
            complete: function (data) {
            }
        });
    }
}

jQuery(document).ready(function () {

    /**
     * Check storage change for getting user address and domains
     */
    jQuery(window).bind('storage', function (es) {
        const address = localStorage.getItem('mainAddress');

        // Set up session address
        const sessionAddressCheck =  localStorage.getItem('sessionAddress');
        if(!sessionAddressCheck){
            localStorage.setItem('sessionAddress', address);
        }

        if (address) {

            // Check if user changed address
            const sessionAddress =  localStorage.getItem('sessionAddress');
            if(sessionAddress != address){
                resetStorageValues();
                localStorage.setItem('sessionAddress', address);
            }

            setUpUserCryptoAddress(address);

        }

        const domains = localStorage.getItem('ensDomains');

        if (address && domains) {
            setUpUserENSDomains(address, jQuery.parseJSON(domains));
        }

    });

    setUpDisconnectButton();

});

/**
 * Hide disconnect button on mobile
 */
function hideMobileDisconnect() {
    const connectDiv = jQuery('#connectHeader');
    const buttonText = jQuery('#connectHeader button').text();
    if (buttonText === 'Connect Wallet') {
        jQuery('#navbarsDefault .nav-disconnect').hide();
    } else {
        jQuery('#navbarsDefault .nav-disconnect').show();
    }

    // Check if changed network
    if (buttonText === 'Switch network') {
        jQuery('#connectHeader button').trigger('click');
    }

}

/**
 * Track changes when user connect / disconnect
 */
function setUpDisconnectButton() {

    const targetNode = document.getElementById('connectHeader');
    const config = {attributes: true, childList: true, subtree: true};
    const callback = (mutationList, observer) => {
        for (const mutation of mutationList) {
            if (mutation.type === 'childList') {
                hideMobileDisconnect();
            } else if (mutation.type === 'attributes') {
            }
        }
    };
    const observer = new MutationObserver(callback);
    observer.observe(targetNode, config);

    // First setup
    hideMobileDisconnect();

}

/**
 * Change Product main image
 */
function changeProductMainImage(imageUrl, order) {
    jQuery('#main-image-link img').attr('src', imageUrl);
    jQuery('#main-image-link').attr('data-image-number', order);
}

/**
 * Start lightbox gallery
 */
function viewProductLightBox(element) {
    const order = jQuery(element).attr('data-image-number');
    var calScreenWidth = jQuery(window).width();

    if (calScreenWidth <= 600) {
        jQuery('.image_order_' + order).trigger('click');
    }
}

/**
 * Change expand button title
 */
function changeExpandTitle(element) {
    const expand = jQuery(element).attr('aria-expanded');
    if (expand === 'true') {
        jQuery(element).children('span').text('Read less');
    } else {
        jQuery(element).children('span').text('Read more');
    }
}

/**
 * Trigger click on connect button
 *
 * @returns {boolean}
 */
function clickConnectButton(){
    jQuery('#connectHeader button').trigger('click');
    return false;
}

jQuery(document).ready(function () {

    // Set up images
    setUpImages();

    setUpLoadersCart();

    setUpLoadersCheckout();

    // Add nice bootstrap select to select fields on shipping page
    jQuery('#calculate-shipping select').selectpicker();

    setUpShippingFormCheck();

    setUpNewsletterFormCheck();

    setQuantityCartUpdate();

    fixCheckmarkIssue();

    domainDropdownHandler();

    handleSignature();

    setupInitialValues();

});

jQuery(window).load(function () {
    setUpImages();
});

jQuery(document).ajaxStop(function () {
    fixCheckmarkIssue();
});

/**
 * Domain dropdown handler
 */
function domainDropdownHandler() {

    let initialSelectValue = 'nick.eth';
    const domainSelect = jQuery('.domain-select select');

    domainSelect.on('click', function () {
        initialSelectValue = jQuery(this).val();
        jQuery(this).parent().find('.ms-filter-box').show();
    });

    jQuery('.domain-select .ms-filter-box').on('focus', function () {
        jQuery('.domain-select').addClass('focused');
    });

    jQuery('.domain-select').on('click', function () {
        jQuery('.domain-select .ms-filter-box input').attr('placeholder', 'Search');
        jQuery('.domain-select').addClass('focused');
        jQuery('.domain-select .ms-filter-box input').focus();
    });

    jQuery(document).on('click', function (e) {
        if (jQuery(e.target).closest(".domain-select").length === 0) {
            if (e.target !== jQuery('.domain-select')[0]) {
                jQuery('.domain-select .ms-filter-box').hide();
                jQuery('.domain-select').removeClass('focused');
            }
        }
    });

    domainSelect.change(function () {
        const msFilterBox = jQuery('.domain-select .ms-filter-box');
        if(
            (initialSelectValue != jQuery(this).val()) ||
            !msFilterBox.is(':visible')
        ){
            initialSelectValue = jQuery(this).val();
            msFilterBox.hide();
            jQuery('.domain-select').removeClass('focused');
        }
    });

}

/**
 * Fix checkmark custom radio button click option
 */
function fixCheckmarkIssue() {
    jQuery('.wc_payment_method').on('click', function (e) {
        jQuery('.wc_payment_method .payment_box').hide();
        jQuery(this).find('.input-radio').prop("checked", true);
        jQuery(this).find('.payment_box').show();
    });
}

/**
 * Set up images
 */
function setUpImages() {

    var calScreenWidth = jQuery(window).width();

    if (calScreenWidth > 600) {
        jQuery('.product-list img').removeAttr('height').removeAttr('width');
        jQuery('.best-seller .product-list img').removeAttr('height').removeAttr('width');
    } else if (calScreenWidth <= 600) {
        jQuery('.product-list img').removeAttr('height').removeAttr('width');
        jQuery('.best-seller .product-list img').removeAttr('height').removeAttr('width');
    }

}

/**
 * Set up loader when user change his data on
 * cart page
 */
function setUpLoadersCart() {

    // Open modal
    const cartModalUpdate = new bootstrap.Modal('#loaderModal', {
        keyboard: false
    });

    jQuery(document).on("ajaxSend", function (event, xhr, settings) {
        if (xhr.readyState === 1 && settings.url === "/?wc-ajax=update_order_review") {
            cartModalUpdate.show();
        }
    })
    jQuery(document).on("ajaxComplete", function (event, xhr, settings) {
        if (xhr.readyState === 4 && settings.url === "/?wc-ajax=update_order_review") {
            jQuery('#loaderModal').removeClass('show').hide().attr('aria-hidden', 'true');
            jQuery('.modal-backdrop').removeClass('show').remove();
            jQuery('body').removeClass('modal-open').css('overflow', 'auto');
            cartModalUpdate.hide();
        }
    })
}

/**
 * Set up loader when user change his data on
 * checkout page
 */
function setUpLoadersCheckout() {

    // Open modal
    const cartModalUpdate = new bootstrap.Modal('#loaderModal', {
        keyboard: false
    });

    jQuery(document).on("ajaxSend", function (event, xhr, settings) {
        if (xhr.readyState === 1 && settings.url === "/?wc-ajax=checkout") {
            cartModalUpdate.show();
        }
    })
    jQuery(document).on("ajaxComplete", function (event, xhr, settings) {
        if (xhr.readyState === 4 && settings.url === "/?wc-ajax=checkout") {
            jQuery('#loaderModal').removeClass('show').hide().attr('aria-hidden', 'true');
            jQuery('.modal-backdrop').removeClass('show').remove();
            jQuery('body').removeClass('modal-open').css('overflow', 'auto');
            cartModalUpdate.hide();
        }
    })
}

/**
 * Initialize bootstrap modal
 *
 * @param modalId
 * @param keyboard
 * @returns {*}
 */
function initializeModal({modalId, keyboard = false}){
    return new bootstrap.Modal(modalId, {
        keyboard: keyboard
    });
}

/**
 * Add content to modal
 *
 * @param modalId
 * @param modalContent
 * @returns {*|jQuery}
 */
function addModalContent(modalId, modalContent){
    return jQuery(`${modalId} .modal-content`).html(modalContent);
}

/**
 * Create mockup for selected domain
 */
function createMockup(postID) {

    const domain = jQuery('#domain-' + postID).val();

    // Open modal
    const mockupModal = initializeModal({modalId: '#mockupModal'});
    mockupModal.show();

    // Default domain - error
    if (domain === '0') {
        addModalContent('#mockupModal', 'Select some domain names different of the default :)');
        setTimeout(function () { mockupModal.hide(); }, 3000);
        return false;
    }

    addModalContent('#mockupModal', 'We are creating mockups, it will take some time, we must use special magic for that ;)');

    if (postID && postID > 0 && domain !== '0') {
        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=set_new_mockups&postID=' + postID + '&domain=' + jQuery('#domain-' + postID).val(),
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {

                    // Add images
                    if (response_data.images.length > 0) {

                        if (response_data.images[0].image_url) {
                            jQuery('#main-image').attr('src', response_data.images[0].image_big_url);
                            jQuery('#image-preview-0 img').attr('src', response_data.images[0].image_small_url);
                            jQuery('#image-preview-0').attr('onClick', "changeProductMainImage('" + response_data.images[0].image_big_url + "', 1);");
                            jQuery('#image-0').attr('href', response_data.images[0].image_big_url);
                            jQuery('#image-0').click(() => {
                                changeProductMainImage(response_data.images[0].image_url, 1);
                            });
                            jQuery('#image-0 img').attr('src', response_data.images[0].image_big_url);
                        }
                        if (response_data.images[1].image_url) {
                            jQuery('#image-preview-1 img').attr('src', response_data.images[1].image_small_url);
                            jQuery('#image-preview-1').attr('onClick', "changeProductMainImage('" + response_data.images[1].image_big_url + "', 2);");
                            jQuery('#image-1').attr('href', response_data.images[1].image_url);
                            jQuery('#image-1 img').attr('src', response_data.images[1].image_big_url);
                        }
                        if (response_data.images[2].image_url) {
                            jQuery('#image-preview-2 img').attr('src', response_data.images[2].image_small_url);
                            jQuery('#image-preview-2').attr('onClick', "changeProductMainImage('" + response_data.images[2].image_big_url + "', 3);");
                            jQuery('#image-2').attr('href', response_data.images[2].image_url);
                            jQuery('#image-2 img').attr('src', response_data.images[2].image_big_url);
                        }
                    }

                    addModalContent('#mockupModal', 'We are done, magic happen :)');
                    setTimeout(function () { mockupModal.hide(); }, 2500);

                } else if (response_data.status == 3 || response_data.status == 4 || response_data.status == 5) {
                    addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                    setTimeout(function () { mockupModal.hide(); }, 2500);
                } else {
                    addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                    setTimeout(function () { mockupModal.hide(); }, 2500);
                }
            },
            error: function () {
                addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                setTimeout(function () { mockupModal.hide(); }, 2500);
            },
            complete: function () {}
        });
    } else {
        addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
        setTimeout(function () { mockupModal.hide(); }, 2500);
    }

    return false;
}

/**
 * Add product to cart
 */
function addProductToCart(postID) {

    if (postID > 0) {

        // Open modal
        const addModal = initializeModal({modalId: '#mockupModal'});
        addModal.show();
        addModalContent('#mockupModal', 'Adding product to your bag, please wait ;)');

        // Get data
        const productID = jQuery('#product_id-' + postID).val();
        const domain = jQuery('#domain-' + postID).val();
        const ensSign = localStorage.getItem('ensSign');
        const signMessage = localStorage.getItem('ensHash');

        let error = false;

        if (jQuery.trim(productID) == '') { error = true; }
        if (jQuery.trim(domain) == '') { error = true; }

        if (!error) {

            jQuery.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                data: 'action=product_add_cart&' + jQuery("#addProductForm" + postID).serialize()
                    + '&sign=' + ensSign + '&signMessage=' +signMessage
                ,
                success: function (data) {
                    var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                    if (response_data.status == 1) {

                        jQuery('.btn-cart-top-count').text(response_data.total_cart);

                        if (jQuery('body.single-product').length > 0) {
                            jQuery('.already-in-cart').remove();
                            if (response_data.cart_already_html) {
                                jQuery('.already-in-cart').remove();
                                jQuery('.product-cart-options').after(response_data.cart_already_html);
                            }
                        }

                        if (jQuery('.button-submit').length > 0 && jQuery('.btn-view-cart').length == 0) {
                            jQuery('.button-submit .btn-submit').after('<a href="/checkout" title="View Cart" class="btn-view-cart">View Cart</a>');
                        }

                        addModalContent('#mockupModal', 'Product added to your bag ;)');
                        setTimeout(function () { addModal.hide(); }, 2000);
                    } else if (response_data.status == 3) { // ens domain with user doesn't match
                        addModalContent('#mockupModal', 'You can\'t buy hat with domain that you don\'t own :(');
                        setTimeout(function () { addModal.hide(); }, 2000);
                    } else { // we have some error
                        addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                        setTimeout(function () { addModal.hide(); }, 2000);
                    }
                },
                error: function () {
                    addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                    setTimeout(function () { addModal.hide(); }, 2000);
                }
            });

        } else {
            addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
            setTimeout(function () { addModal.hide(); }, 2000);
        }

    }

    return false;
}

/**
 * Remove product from cart
 */
function removeProductFromCart(cartProductKey, productID) {

    if (jQuery.trim(cartProductKey) !== '' && productID > 0) {

        // Open modal
        const removeModal = initializeModal({modalId: '#mockupModal'});
        removeModal.show();
        addModalContent('#mockupModal', 'Removing product from your bag :(');

        if (cartProductKey && productID) {
            jQuery.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                data: 'action=product_remove_cart&cartProductKey=' + cartProductKey + '&productID=' + productID,
                success: function (data) {
                    var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                    if (response_data.status == 1) {

                        jQuery('.btn-cart-top-count').text(response_data.total_cart);

                        jQuery('#al-' + cartProductKey).remove();
                        jQuery('#cart-item-' + productID).remove();

                        jQuery('div[data-cart-item="' + cartProductKey + '"]').remove();

                        if (response_data.subtotal_cart) {
                            jQuery('.subtotal-title div[data-title="Subtotal"]').html(response_data.subtotal_cart);
                        }

                        addModalContent('#mockupModal', 'Product removed from your bag :(');
                        setTimeout(function () { removeModal.hide(); }, 2500);

                        // Redirect to cart if it is empty
                        if (jQuery('.woocommerce-checkout').length > 0) {
                            if (jQuery('.woocommerce-cart-form__cart-item').length == 0) {
                                window.location.href = '/cart';
                            }
                        }

                    } else {
                        addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                        setTimeout(function () { removeModal.hide(); }, 2500);
                    }
                },
                error: function () {
                    addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                    setTimeout(function () { removeModal.hide(); }, 2500);
                },
                complete: function () {

                }
            });
        } else {
            addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
            setTimeout(function () { removeModal.hide(); }, 2500);
        }

    }

    return false;
}

/**
 * Display state options based on selected current
 * country value
 *
 * @param currentCountry
 */
function changeStateOptions(currentCountry) {

    jQuery('.column-state').hide();

    // Show state for specific country
    if (currentCountry === 'US' || currentCountry === 'AU' || currentCountry === 'CA') {
        jQuery('.state-' + currentCountry.toLowerCase()).show();
    }
    // For all others show input field
    else {
        jQuery('.default-state').show();
    }
}

/**
 * Set up shipping form check
 */
function setUpShippingFormCheck() {
    const calculateShipping = jQuery('#calculate-shipping');
    calculateShipping.on('click', function () {
        jQuery('#calculate-shipping #cshsp-check').val("1");
    });
    calculateShipping.on('keyup change paste', 'input, select, textarea', function () {
        calculateShippingPageCost();
    });
}

/**
 * Set up newsletter form check
 */
function setUpNewsletterFormCheck() {
    jQuery('#newsletter-form').on('click', function () {
        jQuery('#newsletter-form #newfrch-check').val("1");
    });
}

/**
 * Calculate shipping cost
 *
 */
function calculateShippingPageCost() {

    let error = false;

    if (jQuery.trim(jQuery('#address').val()) === '') {
        error = true;
    }
    if (jQuery.trim(jQuery('#zip').val()) === '') {
        error = true;
    }
    if (jQuery.trim(jQuery('#product_variant').val()) === '') {
        error = true;
    }
    if (jQuery.trim(jQuery('#country').val()) === '') {
        error = true;
    }
    if (jQuery.trim(jQuery('#calculate-shipping-nonce').val()) === '') {
        error = true;
    }
    if (jQuery.trim(jQuery('#cshsp-check').val()) !== '1') {
        error = true;
    }

    if (!error) {
        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=calculate_shipping_page_cost&' + jQuery("#calculate-shipping").serialize(),
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {
                    if (response_data.rate?.name) {
                        jQuery('.calculated .ship').text(response_data.rate?.name);
                        jQuery('.calculated .rate').text('$' + response_data.rate?.rate);
                    }
                }
            },
            error: function () {
            },
            complete: function () {
            }
        });
    }
}

/**
 * Check if email valid
 *
 * @param string emailAddress
 *
 * @returns {boolean}
 */
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);

    return pattern.test(emailAddress);
}

/**
 * Newsletter subscribe
 */
function newslettelSubscribe() {

    const newslettelModal = initializeModal({modalId: '#mockupModal'});

    let error = false;

    if (jQuery.trim(jQuery('#email').val()) == '') {
        error = true;
    }
    if (!isValidEmailAddress(jQuery('#email').val())) {
        error = true;
    }
    if (jQuery.trim(jQuery('#newsletter-form-nonce').val()) === '') {
        error = true;
    }
    if (jQuery.trim(jQuery('#newfrch-check').val()) !== '1') {
        error = true;
    }

    if (!error) {

        newslettelModal.show();
        addModalContent('#mockupModal', 'Subscribing...');

        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=newsletter_form&' + jQuery("#newsletter-form").serialize(),
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {
                    jQuery('#email').val('');
                    addModalContent('#mockupModal', 'Subscribed');
                    setTimeout(function () { newslettelModal.hide(); }, 2500);
                }
            },
            error: function () {
                addModalContent('#mockupModal', 'Sorry we found error, please try later :(');
                setTimeout(function () { newslettelModal.hide(); }, 2500);
            },
            complete: function () {}
        });
    }

    return false;
}

/**
 * Set up cart quantity update when user change it
 */
function setQuantityCartUpdate() {

    jQuery('.cw_qty').on('change', function () {

        const qty = jQuery(this).val();
        const itemKey = jQuery(this).closest('.cart_item').attr('data-cart-item');

        if (qty && itemKey) {
            jQuery.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                data: 'action=update_cart_quantity&qty=' + qty + '&itemKey=' + itemKey,
                success: function (data) {
                    var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                    if (response_data.status == 1) {
                        jQuery('.btn-cart-top-count').text(response_data.total_cart);

                        if (response_data.subtotal_cart) {
                            jQuery('.subtotal-title div[data-title="Subtotal"]').html(response_data.subtotal_cart);
                        }

                        if (response_data.product_updated) {
                            jQuery('div[data-cart-item="' + itemKey + '"] div[data-title="Subtotal"]').html(response_data.product_updated);
                        }

                        if (response_data.shipping_amount) {
                            jQuery('#shipping_method .method-item-right').html(response_data.shipping_amount);
                        }
                    }
                }
            });
        }

    });
}


/**
 * Check if email valid
 *
 * @param emailAddress
 * @returns {boolean}
 */
function isValidEmailAddress(emailAddress) {
    let pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

/**
 * Reset form elements values
 */
function _resetFormElementsValues() {
    jQuery('input,textarea').not('input[type=submit]').not('input[name=contact-nonce]').val('');
    jQuery('input:checkbox').removeAttr('checked');
    return false;
}

/**
 * Remove errors from form elements
 */
function _removeErrorsClass() {
    jQuery('input,textarea,label').removeClass('error-element');
    return false;
}

/**
 * Send message via ajax - contact form
 */
function sendContactMessage() {

    _removeErrorsClass();

    jQuery('#contact-form .contact-alert-warning').hide();

    const name = jQuery('#contact-name').val();
    const email = jQuery('#contact-email').val();
    const message = jQuery('#contact-message').val();
    const nonce = jQuery('#contact-nonce').val();

    let error = false;

    if (jQuery.trim(name) == '') {
        jQuery('input#contact-name').addClass('error-element');
        error = true;
    }
    if (jQuery.trim(email) == '') {
        jQuery('input#contact-email').addClass('error-element');
        error = true;
    }
    if (!isValidEmailAddress(email)) {
        jQuery('input#contact-email').addClass('error-element');
        error = true;
    }
    if (jQuery.trim(message) == '') {
        jQuery('textarea#contact-message').addClass('error-element');
        error = true;
    }
    if (jQuery.trim(nonce) == '') {
        error = true;
    }

    if (!error) {
        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=contact_form&' + jQuery("#contact-form").serialize(),
            success: function (data) {
                const response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {
                    jQuery('#contact-form .contact-alert-warning').slideUp();
                    jQuery('#contact-form .contact-alert-success').slideDown();
                    _resetFormElementsValues();
                } else {
                    jQuery('#contact-form .contact-alert-warning').slideDown();
                }
            },
            error: function () {
                jQuery('#contact-form .contact-alert-warning').slideDown();
            }
        });
    } else {
        jQuery('#contact-form .contact-alert-warning').slideDown();
    }

    return false;
}

/**
 * Request sign in message again
 */
function signMessageAgain() {
    jQuery('#connectHeader .hidden').trigger('click');
}

/**
 * This function will handle signature from
 * React app wallet connection
 */
function handleSignature() {

    let signinStarted = false;

    const signatureModal = initializeModal({modalId: '#mockupModal', keyboard: true});

    // Check if sign started
    jQuery(window).bind('storage.hash', function (es) {
        const connectDiv = jQuery('#connectHeader #myBtn').text();
        const signValue = localStorage.getItem('ensSign');
        if (connectDiv !== 'Connect Wallet' && signValue.trim() === '') {
            signatureModal.show();
            if(window.location.pathname === '/checkout/') {
                addModalContent('#mockupModal', "We need to authenticate your account. Please sign a message with your wallet.<div id='send-again' class='d-block'><button onclick='signMessageAgain()'>SIGN IN</button></div>");
            }else{
                addModalContent('#mockupModal', "We need to authenticate your account. Please sign a message with your wallet.<div id='send-again'><button onclick='signMessageAgain()'>SIGN IN</button></div>");
            }
            signinStarted = true;
        }
    });

    // Check if hash validated
    jQuery(window).bind('storage.hashvalidate', function (es) {
        const connectDiv = jQuery('#connectHeader button').text();
        const signValue = localStorage.getItem('ensSign');
        if (signValue.trim() !== '' && signinStarted && connectDiv !== 'Connect Wallet') {

            if(jQuery('#wa_signature').length > 0){
                jQuery('#wa_signature').val(localStorage.getItem('ensSign'));
                jQuery('#wa_hash').val(localStorage.getItem('ensHash'));
            }

            if (signValue === 'rejected') {
                addModalContent('#mockupModal', "To use ENS Merch Shop you will need to reconnect your wallet and sign a message with your wallet.");
                setTimeout(function () {
                    signatureModal.hide();
                    signinStarted = false;
                }, 5000);
            } else {
                addModalContent('#mockupModal', "You have successfully authenticated.");
                setTimeout(function () {
                    signatureModal.hide();
                    signinStarted = false;
                }, 2000);
            }
        }
    });

    // Checkout signature handle
    if(window.location.pathname === '/checkout/'){
        const connectDiv = jQuery('#connectHeader button').text();
        const signValue = localStorage.getItem('ensSign');
        if (signValue === 'rejected' || signValue.trim() === '' && connectDiv !== 'Connect Wallet') {
            localStorage.setItem('ensSign', '');
        }
    }
}

/**
 * Set up initial values related to user wallet and
 * web shop data
 */
function setupInitialValues() {
    const connectDiv = jQuery('#connectHeader button').text();
    const mainAddress = localStorage.getItem('mainAddress');
    if (connectDiv === 'Connect Wallet' && (mainAddress.trim() === '' || mainAddress === 'undefined')) {
        resetStorageValues();
    }

    // Add hash values to the checkout form
    if (window.location.pathname === '/checkout/') {
        const checkoutSignValue = localStorage.getItem('ensSign');
        jQuery('#wa_signature').val(checkoutSignValue);
        const checkoutHashValue = localStorage.getItem('ensHash');
        jQuery('#wa_hash').val(checkoutHashValue);
    }

    // Check connect link notification
    if (connectDiv === 'Connect Wallet'){
        jQuery('.connect-column').show();
        jQuery('.header-button').show();
    }
}

/**
 * Reset website storage values
 */
function resetStorageValues() {
    localStorage.setItem('ensHash', '');
    localStorage.setItem('ensSign', '');
    localStorage.setItem('ensDomains', '');
}