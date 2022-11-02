<?php

/**
 * Show product tag
 */
function display_product_tag()
{

    global $product;

    $tagTitle = '';
    $tags = $product->tag_ids;
    foreach ($tags as $tag) {
        $tagTitle = get_term($tag)->name;
    }

    if ($tagTitle) {
        echo '
            <span class="product-tag">' . $tagTitle . '</span>
        ';
    }

}

add_action('woocommerce_after_shop_loop_item_title', 'display_product_tag', 5);

function getPagination($currentUrl, $queryString, $numberOfPages, $page, $mid_size = 1, $end_size = 2, $dots = false, $print = true)
{

    $html = '<div id="pagination-div" class="pagination text-center">';

    if ($numberOfPages > 1 && $page > 1) {
        $html .= '<a class="prev page-numbers" href="' . $currentUrl . '/?' . substr($queryString, 1) . '" data-str="' . ($page - 1) . '">«</a>';
    }

    for ($i = 1; $i <= $numberOfPages; $i++) {

        if ($i == $page) { /* Current page */
            $html .= '<span aria-current="page" class="page-numbers current">' . $page . '</span>';
            $dots = TRUE;
        } else {

            if ($i <= $end_size || ($page && $i >= $page - $mid_size && $i <= $page + $mid_size) || $i > $numberOfPages - $end_size) {
                if ($i == 1) {
                    $html .= '<a class="page-numbers" href="' . $currentUrl . '/?' . substr($queryString, 1) . '" data-str="1">' . $i . '</a>';
                } else {
                    $html .= '<a class="page-numbers" href="' . $currentUrl . '/?str=' . $i . $queryString . '" data-str="' . $i . '">' . $i . '</a>';
                }
                $dots = TRUE;
            } elseif ($dots) {
                $html .= '<span class="page-numbers dots">…</span>';
                $dots = FALSE;
            }

        }

    }

    if ($page < $numberOfPages) {
        $html .= '<a class="next page-numbers" href="' . $currentUrl . '/?str=' . ($page + 1) . $queryString . '" data-str="' . ($page + 1) . '">»</a>';
    }

    $html .= '</div>';

    if ($print) {
        echo $html;
    } else {
        return $html;
    }

}