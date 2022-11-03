<?php
$categoryName = single_term_title("", false);
$categoryData = get_term_by('name', $categoryName, 'product_cat');
?>

<?php include 'webshop-category-products.php'; ?>
