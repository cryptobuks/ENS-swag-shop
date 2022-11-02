<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}

// Main Menu
register_nav_menu('header', 'Main menu in site header');

// Footer Menu One
register_nav_menu('footer', 'Footer menu one');

// Footer Menu Two
register_nav_menu('footer_2', 'Footer menu two');

// Footer Menu Three
register_nav_menu('footer_3', 'Footer menu three');