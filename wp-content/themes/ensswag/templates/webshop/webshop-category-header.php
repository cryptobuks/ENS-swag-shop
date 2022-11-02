<?php if (isset($categoryData)): ?>

    <?php
    $categoryImage = get_field('header_image', $categoryData);
    ?>

    <?php if (isset($categoryImage['sizes']['theme-thumb-1'])): ?>

        <!-- start:home-header -->
        <div class="home-header">

            <!-- start:container -->
            <div class="container">
                <div class="text">
                    <div class="text-inside">
                        apparel for impact - apparel for impact -apparel for impact - apparel for impact -apparel for
                        impact
                        - apparel for impact - apparel for impact - apparel for impact - apparel for impact - apparel
                        for
                        impact - apparel for impact - apparel for impact - apparel for impact - apparel for impact -
                        apparel
                        for impact
                    </div>
                </div>

                <img src="<?php echo $categoryImage['sizes']['theme-thumb-1']; ?>" alt="<?php echo $categoryImage['alt']; ?>" class="d-block w-100">

                <div class="text">
                    <div class="text-inside">
                        impact -apparel for impact - apparel for impact - apparel for impact - apparel for impact -
                        apparel
                        for impact - apparel for impact - apparel for impact - apparel for impact - apparel for impact -
                        apparel for impact - apparel for impact - apparel for impact - apparel for impact - apparel for
                        impact - apparel for impact - apparel for impact
                    </div>
                </div>
            </div>
            <!-- end:container -->

        </div>
        <!-- end:home-header -->

    <?php endif; ?>

<?php endif; ?>