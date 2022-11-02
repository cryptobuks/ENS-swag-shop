<?php
// Get values
$optionsSocials = get_option('wedevs_socials');
?>
<!-- start:footer -->
<footer class="footer">

    <!-- start:container -->
    <div class="container">

        <!-- start:logo-holder -->
        <div class="logo-holder">
            <a href="/" class="d-inline-block"><img src="<?php echo TEMPLATEDIR; ?>/images/logo-footer.svg" alt=""></a>
        </div>
        <!-- end:logo-holder -->

        <!-- start:menu -->
        <div class="menu">
            
            <!-- start:row -->
            <div class="row justify-content-center">

                <div class="col-12 col-md-auto">

                    <?php
                    $footerMenuArguments = array(
                        'theme_location' => 'footer',
                        'menu' => '',
                        'container' => false,
                        'container_class' => null,
                        'container_id' => null,
                        'menu_class' => '',
                        'menu_id' => '',
                        'echo' => true,
                        'fallback_cb' => 'wp_page_menu',
                        'before' => '',
                        'after' => '',
                        'link_before' => '',
                        'link_after' => '',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth' => 4,
                    );

                    wp_nav_menu($footerMenuArguments);
                    ?>

                </div>

                <div class="col-12 col-md-auto">

                    <?php
                    $footerMenuArguments = array(
                        'theme_location' => 'footer_2',
                        'menu' => '',
                        'container' => false,
                        'container_class' => null,
                        'container_id' => null,
                        'menu_class' => '',
                        'menu_id' => '',
                        'echo' => true,
                        'fallback_cb' => 'wp_page_menu',
                        'before' => '',
                        'after' => '',
                        'link_before' => '',
                        'link_after' => '',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth' => 4,
                    );

                    wp_nav_menu($footerMenuArguments);
                    ?>

                </div>

                <div class="col-12 col-md-auto">

                    <?php
                    $footerMenuArguments = array(
                        'theme_location' => 'footer_3',
                        'menu' => '',
                        'container' => false,
                        'container_class' => null,
                        'container_id' => null,
                        'menu_class' => '',
                        'menu_id' => '',
                        'echo' => true,
                        'fallback_cb' => 'wp_page_menu',
                        'before' => '',
                        'after' => '',
                        'link_before' => '',
                        'link_after' => '',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth' => 4,
                    );

                    wp_nav_menu($footerMenuArguments);
                    ?>

                </div>

            </div>
            <!-- end:row -->

        </div>
        <!-- end:menu -->

        <!-- start:socials -->
        <div class="socials">

            <?php if( isset($optionsSocials['twitter_url']) && $optionsSocials['twitter_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['twitter_url']; ?>" class="twitter" target="_blank"><?php _e('Twitter', 'template'); ?></a>
            <?php endif; ?>

            <?php if( isset($optionsSocials['github_url']) && $optionsSocials['github_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['github_url']; ?>" class="github" target="_blank"><?php _e('Github', 'template'); ?></a>
            <?php endif; ?>

            <?php if( isset($optionsSocials['discord_url']) && $optionsSocials['discord_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['discord_url']; ?>" class="discord" target="_blank"><?php _e('Discord', 'template'); ?></a>
            <?php endif; ?>

            <?php if( isset($optionsSocials['medium_url']) && $optionsSocials['medium_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['medium_url']; ?>" class="medium" target="_blank"><?php _e('Medium', 'template'); ?></a>
            <?php endif; ?>

            <?php if( isset($optionsSocials['discourse_url']) && $optionsSocials['discourse_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['discourse_url']; ?>" class="discourse" target="_blank"><?php _e('discourse_url', 'template'); ?></a>
            <?php endif; ?>

            <?php if( isset($optionsSocials['youtube_url']) && $optionsSocials['youtube_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['youtube_url']; ?>" class="youtube" target="_blank"><?php _e('Youtube', 'template'); ?></a>
            <?php endif; ?>

            <?php if( isset($optionsSocials['opensea_url']) && $optionsSocials['opensea_url'] != '' ): ?>
                <a href="<?php echo $optionsSocials['opensea_url']; ?>" class="opensea" target="_blank"><?php _e('Opensea', 'template'); ?></a>
            <?php endif; ?>

        </div>
        <!-- end:socials -->

        <div class="text-center pt-5">
            <a href="https://www.generalmagic.io/" target="_blank"><img src="<?php echo TEMPLATEDIR; ?>/images/built-by-gm.svg" alt="General Magic" loading="lazy" /></a>
        </div>

    </div>
    <!-- end:container -->

</footer>
<!-- end:footer -->