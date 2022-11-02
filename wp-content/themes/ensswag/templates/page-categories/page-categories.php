<!-- start:categories-list -->
<div class="categories-list">

    <!-- start:container -->
    <div class="container">

        <!-- start:section-title -->
        <div class="section-title">
            <h2><?php echo strip_tags(get_the_content()); ?></h2>
            <h3><?php echo strip_tags(get_the_excerpt()); ?></h3>
        </div>
        <!-- end:section-title -->

        <?php
        // Get categories
        $taxonomies = get_terms(
            array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'don_show_on_categories_list',
                        'value' => '0',
                    ),
                ),
                'meta_key' => 'order',
                'orderby' => 'meta_value_num',
                'order' => 'ASC'
            )
        );
        ?>

        <?php if ($taxonomies): ?>

            <!-- start:categories-list-over -->
            <div class="categories-list-over">

                <!-- start:row -->
                <div class="row">

                    <?php foreach ($taxonomies as $tax): ?>

                        <?php
                        $categoryImage = get_field('image', $tax);
                        ?>

                        <?php if (isset($categoryImage['sizes']['theme-thumb-3'])): ?>
                            <div class="col-12 col-md-6 mb-4">
                                <a href="<?php echo get_term_link($tax); ?>" class="image-holder">
                                    <img src="<?php echo $categoryImage['sizes']['theme-thumb-4']; ?>"
                                         alt="<?php echo $tax->name; ?>">
                                    <span><?php echo $tax->name; ?></span>
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>
                <!-- end:row -->

            </div>
            <!-- end:categories-list-over -->

        <?php endif; ?>

    </div>
    <!-- end:container -->

</div>
<!-- end:categories-list -->


<?php wp_reset_postdata(); ?>