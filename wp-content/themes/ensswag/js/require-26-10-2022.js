let askingSignature = false;

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
            data: 'action=set_crypto_address&address=' + address,
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {
                    jQuery('#wa_hash_footer').val(response_data.aw_hash);
                    console.log('pitaj signature');
                    if(jQuery('#aw_signature_footer').val() === '' && localStorage.getItem("wagmi.connected") === 'true' && !askingSignature){
                        askingSignature = true;
                        handleSignature();
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
 * Set up user ENS domains
 *
 * @param address
 * @param domains
 */
function setUpUserENSDomains(address, domains) {

    if (address && domains) {

        // Set up notification for loading
        const ddSelectLoading = jQuery('.domain-select select')[0].msDropdown;

        jQuery('.domain-select select').children('option').each(function () {
            jQuery(this).remove();
        });
        jQuery(this).append('<option value="0" data-image="https://ensmerchshop.xyz/wp-content/themes/ensswag/images/default-avatar.svg">loading...</option>');

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

                if(response_data.total_cart !== undefined){
                    jQuery('.btn-cart-top-count').text(response_data.total_cart);
                    // if cart is empty redirect user to homepage
                    if(response_data.total_cart  == 0 && window.location.pathname === '/checkout/'){
                        window.location.href = "/";
                    }
                }

                if(response_data.aw_hash !== undefined){
                    // TODO
                }

                if(response_data.aw_signature !== undefined){
                    // TODO
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

                        if( response_data.ascii_status && response_data.ascii_status.trim() !== '' && jQuery('.ascii_notice').length == 0 ){
                            jQuery('.product-cart-options').prepend('<div class="col-12 ascii_notice-over"><div class="ascii_notice">'+response_data.ascii_status+'</div></div>')
                        }

                    }

                }

                if (response_data.status == 2 && response_data.default_domain?.name) {

                    jQuery('.domain-select select').each(function () {

                        const ddSelect = jQuery(this)[0].msDropdown;

                        jQuery(this).children('option').each(function () {
                            jQuery(this).remove();
                        });

                        // Add domains to each select
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

        if (address) {
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

    if ( connectDiv.has('.imageHolder').length > 0 ) {
        if(jQuery('#aw_signature_footer').val() === '' && localStorage.getItem("wagmi.connected") === 'true'){

         // handleSignature();
        }
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
        jQuery(element).children('span').text('See Less');
    } else {
        jQuery(element).children('span').text('See More');
    }
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

});

jQuery(window).load(function () {

    // Set up images
    setUpImages();

});

/*
* Set up on scroll sticky menu
*
* @param ev
*/
window.onscroll = function (ev) {
};

jQuery(document).ajaxStop(function () {
    fixCheckmarkIssue();
});

/**
 * Domain dropdown handler
 */
function domainDropdownHandler(){
    jQuery('.domain-select select').on('click', function () {
        jQuery(this).parent().find('.ms-filter-box').show();
    });

    jQuery('.domain-select .ms-filter-box').on('focus', function (){
        jQuery('.domain-select').addClass('focused');
    });

    jQuery('.domain-select').on('click', function (){
        jQuery('.domain-select .ms-filter-box input').attr('placeholder', 'Search');
        jQuery('.domain-select').addClass('focused');
        jQuery('.domain-select .ms-filter-box input').focus();
    });

    jQuery(document).on('click', function (e) {
        if ( jQuery(e.target).closest(".domain-select").length === 0 ) {
            if( e.target !== jQuery('.domain-select')[0] ){
                jQuery('.domain-select .ms-filter-box').hide();
                jQuery('.domain-select').removeClass('focused');
            }
        }
    });

    jQuery('.domain-select select').change(function (){
        jQuery('.domain-select .ms-filter-box').hide();
        jQuery('.domain-select').removeClass('focused');
    });

}

/**
 * Fix checkmark custom radio button click option
 */
function fixCheckmarkIssue() {
    jQuery('.checkmark').click(function () {
        jQuery(this).siblings('label').trigger('click');
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
 * cart
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
 * checkout
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
 * Create mockup for selected domain
 */
function createMockup(postID) {

    const domain = jQuery('#domain-' + postID).val();

    // Open modal
    const mockupModal = new bootstrap.Modal('#mockupModal', {
        keyboard: false
    });
    mockupModal.show();

    // Default domain error
    if (domain === '0') {
        jQuery('#mockupModal .modal-content').text('Select some domain names different of the default :)');
        setTimeout(function () {
            mockupModal.hide();
        }, 3000);

        return false;
    }

    jQuery('#mockupModal .modal-content').text('We are creating mockups, it will take some time, we must use special magic for that ;)');

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

                    jQuery('#mockupModal .modal-content').text('We are done, magic happen :)');
                    setTimeout(function () {
                        mockupModal.hide();
                    }, 2500);

                } else if (response_data.status == 3 || response_data.status == 4 || response_data.status == 5) {
                    jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                    setTimeout(function () {
                        mockupModal.hide();
                    }, 2500);
                } else {
                    jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                    setTimeout(function () {
                        mockupModal.hide();
                    }, 2500);
                }
            },
            error: function () {
                jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                setTimeout(function () {
                    mockupModal.hide();
                }, 2500);
            },
            complete: function () {

            }
        });
    } else {
        jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
        setTimeout(function () {
            mockupModal.hide();
        }, 2500);
    }

    return false;

}

/**
 * Add product to cart
 */
async function addProductToCart(postID) {

    if (postID > 0) {

        // Open modal
        const mockupModal = new bootstrap.Modal('#mockupModal', {
            keyboard: false
        });
        mockupModal.show();

        jQuery('#mockupModal .modal-content').text('Adding product to your bag, please wait ;)');

        var productID = jQuery('#product_id-' + postID).val();
        var domain = jQuery('#domain-' + postID).val();
        var nonce = jQuery('#add-mockup-nonce-' + postID).val();

        var error = false;

        if (jQuery.trim(productID) == '') {
            error = true;
        }
        if (jQuery.trim(domain) == '') {
            error = true;
        }
        if (jQuery.trim(nonce) == '') {
            error = true;
        }

        if (!error) {

            let signatureCheck = false;
            const sigantureText = jQuery('#aw_signature_footer').val();
            if(sigantureText.trim() !== ''){
                signatureCheck = true;
            }
            else{
                // const signatureRequest = await handleSignature();
                if(signatureRequest){
                    signatureCheck = true;
                }
            }

            signatureCheck = true;

            if(signatureCheck){

                jQuery.ajax({
                    type: "POST",
                    url: "/wp-admin/admin-ajax.php",
                    data: 'action=product_add_cart&' + jQuery("#addProductForm" + postID).serialize(),
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

                            jQuery('#mockupModal .modal-content').text('Product added to your bag ;)');
                            setTimeout(function () {
                                mockupModal.hide();
                            }, 2500);
                        } else if (response_data.status == 3) {
                            jQuery('#mockupModal .modal-content').text('You can\'t buy hat with domain that you don\'t own :(');
                            setTimeout(function () {
                                mockupModal.hide();
                            }, 2500);
                        } else {
                            jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                            setTimeout(function () {
                                mockupModal.hide();
                            }, 2500);
                        }
                    },
                    error: function () {
                        jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                        setTimeout(function () {
                            mockupModal.hide();
                        }, 2500);
                    },
                    complete: function () {

                    }
                });

            }

        } else {
            jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
            setTimeout(function () {
                mockupModal.hide();
            }, 2500);
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
        const mockupModal = new bootstrap.Modal('#mockupModal', {
            keyboard: false
        });
        mockupModal.show();

        jQuery('#mockupModal .modal-content').text('Removing product from your bag :(');

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

                        jQuery('#mockupModal .modal-content').text('Product removed from your bag :(');
                        setTimeout(function () {
                            mockupModal.hide();
                        }, 2500);

                        // Redirect to cart if it is empty
                        if (jQuery('.woocommerce-checkout').length > 0) {
                            if (jQuery('.woocommerce-cart-form__cart-item').length == 0) {
                                window.location.href = '/cart';
                            }
                        }

                    } else {
                        jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                        setTimeout(function () {
                            mockupModal.hide();
                        }, 2500);
                    }
                },
                error: function () {
                    jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                    setTimeout(function () {
                        mockupModal.hide();
                    }, 2500);
                },
                complete: function () {

                }
            });
        } else {
            jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
            setTimeout(function () {
                mockupModal.hide();
            }, 2500);
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

    const mockupModal = new bootstrap.Modal('#mockupModal', {
        keyboard: false
    });

    let error = false;

    if (jQuery.trim(jQuery('#email').val()) == '') {
        console.log('1');
        error = true;
    }
    if (!isValidEmailAddress(jQuery('#email').val())) {
        console.log('2');
        error = true;
    }
    if (jQuery.trim(jQuery('#newsletter-form-nonce').val()) === '') {
        console.log('3');
        error = true;
    }
    if (jQuery.trim(jQuery('#newfrch-check').val()) !== '1') {
        console.log('4');
        error = true;
    }

    if (!error) {

        mockupModal.show();

        jQuery('#addCartModal .modal-content').text('Subscribing...');

        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=newsletter_form&' + jQuery("#newsletter-form").serialize(),
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

                if (response_data.status == 1) {
                    jQuery('#email').val('');
                    jQuery('#mockupModal .modal-content').text('Subscribed');
                    setTimeout(function () {
                        mockupModal.hide();
                    }, 2500);
                }
            },
            error: function () {
                jQuery('#mockupModal .modal-content').text('Sorry we found error, please try later :(');
                setTimeout(function () {
                    mockupModal.hide();
                }, 2500);
            },
            complete: function () {
            }
        });
    }

    return false;
}

function changeCartQuantity(selectValue) {
    // let ddSelect = selectValue.msDropdown;
    //
    // console.log(ddSelect.selectedIndex);

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
                    }
                },
                error: function () {
                },
                complete: function () {
                }
            });
        }

    });
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

    var name = jQuery('#contact-name').val();
    var email = jQuery('#contact-email').val();
    var message = jQuery('#contact-message').val();
    // var accept		=	jQuery('#contact-accept').is(':checked');
    var nonce = jQuery('#contact-nonce').val();

    var error = false;

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
    // if( !accept ){
    //     jQuery('#contact-accept').parent().addClass('error-element');
    //     error = true;
    // }
    if (jQuery.trim(nonce) == '') {
        error = true;
    }

    if (!error) {
        jQuery.ajax({
            type: "POST",
            url: "/wp-admin/admin-ajax.php",
            data: 'action=contact_form&' + jQuery("#contact-form").serialize(),
            success: function (data) {
                var response_data = jQuery.parseJSON(data.substring(0, data.length - 1));

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
            },
            complete: function () {

            }
        });
    } else {
        jQuery('#contact-form .contact-alert-warning').slideDown();
    }

    return false;
}

async function handleSignature(){

    // Open modal
    jQuery('#signModal .modal-content').text("We need to authenticate your account. Please sign a message with your wallet.");
    jQuery('#signModal').show();

    console.log('window ethereum');
    console.log(window.ethereum);

    if (window.ethereum) {

        console.log('window ethereum');
        console.log(window.ethereum);

        try {

            const accounts = await window.ethereum.request({method: 'eth_requestAccounts'});

            async function signMessage() {
                const message = jQuery('#wa_hash_footer').val();
                try {

                    const from = accounts[0];
                    const sign = await ethereum.request({
                        method: 'personal_sign',
                        params: [message, from, 'Random text'],
                    });

                    if(sign){

                        jQuery.ajax({
                            type: "POST",
                            url: "/wp-admin/admin-ajax.php",
                            dataType: "json",
                            data: {
                                action: "validate_signature",
                                sign: sign,
                            },
                            success: function (response_data) {

                                askingSignature = false;

                                if(response_data.status === 1){
                                    jQuery('#wa_signature').val(sign);
                                    jQuery('#aw_signature_footer').val(sign);
                                    jQuery('#signModal .modal-content').text("You have successfully authenticated.");
                                    setTimeout(function () {
                                        jQuery('#signModal').hide();
                                    }, 2500);

                                    return true;
                                }

                                if(response_data.status === 2){
                                    jQuery('#wa_signature').val("");
                                    jQuery('#aw_signature_footer').val("");
                                    jQuery('#signModal .modal-content').text("Looks like your signature is incorrect. Are you sure you own this address?");
                                    setTimeout(function () {
                                        jQuery('#signModal').hide();
                                    }, 2500);

                                    return false;
                                }

                            },
                            error: function () {
                            },
                            complete: function (data) {
                            }
                        });
                    }

                } catch (err) {
                    jQuery('#wa_signature').val("");
                    jQuery('#aw_signature_footer').val("");
                    jQuery('#signModal .modal-content').text("Looks like your signature is incorrect. Are you sure you own this address?");
                    setTimeout(function () {
                        jQuery('#signModal').hide();
                    }, 2500);
                    console.error(err);

                    return false;
                }
            }

            signMessage();

        } catch (error) {
            jQuery('#wa_signature').val("");
            jQuery('#aw_signature_footer').val("");
            jQuery('#signModal .modal-content').text("Looks like your signature is incorrect. Are you sure you own this address?");
            setTimeout(function () {
                jQuery('#signModal').hide();
            }, 2500);
            if (error.code === 4001) {}

            console.log(error);

            return false;

        }
    }else{
        console.log('not etherum object')
    }

    return false;

}

// jQuery(document).ready( async function () {
//
//     if (window.location.pathname === '/checkout/') {
//         if (jQuery("#wa_signature").val() === '') {
//             await handleSignature();
//         }
//
//     }
//
// });