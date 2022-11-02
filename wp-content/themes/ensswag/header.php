<!DOCTYPE HTML>
<html lang="hr">
<head>

    <!-- start:global -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- end:global -->

    <!-- start:favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo TEMPLATEDIR; ?>/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo TEMPLATEDIR; ?>/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo TEMPLATEDIR; ?>/images/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo TEMPLATEDIR; ?>/site.webmanifest">
    <link rel="mask-icon" href="<?php echo TEMPLATEDIR; ?>/images/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- end:favicon -->


    <?php if ( is_front_page() ): ?>
        <title><?php bloginfo('name'); ?></title>
    <?php else: ?>
        <title><?php bloginfo('name'); ?> - <?php wp_title(''); ?></title>
    <?php endif; ?>
    <!-- end:page title -->

    <meta name="description" content="Get your customised official ENS merch.">

    <!-- start:stylesheets -->
    <link href="<?php echo TEMPLATEDIR; ?>/react/static/css/main.2b2dc94b.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo TEMPLATEDIR; ?>/style.css?ver=1.42">
    <!-- end:stylesheets -->

    <?php include 'templates/header/header-description-tags.php'; ?>

    <?php wp_head(); ?>

    <!-- start:scripts -->
    <script src="<?php echo TEMPLATEDIR; ?>/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?php echo TEMPLATEDIR; ?>/js/bootstrap-select.min.js" defer></script>
    <script src="<?php echo TEMPLATEDIR; ?>/js/dd.min.js" defer></script>
    <script src="<?php echo TEMPLATEDIR; ?>/js/lightbox.min.js" defer></script>
    <script src="<?php echo TEMPLATEDIR; ?>/js/require.js?ver=1.68" defer></script>
    <!-- end:scripts -->

    <?php include 'templates/header/header-google-head.php'; ?>

</head>

<body <?php body_class(); ?>>

    <input type="text" id="signatureInput" value="">

    <?php include 'templates/header/header-google-body.php'; ?>

    <?php include 'templates/header/header.php'; ?>