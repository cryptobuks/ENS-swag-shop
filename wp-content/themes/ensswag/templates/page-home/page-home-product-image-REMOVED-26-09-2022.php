<?php
$thumbID = get_post_thumbnail_id();

$image = wp_get_attachment_image_src($thumbID, 'theme-thumb-1');
$imageAlt = get_post_meta($thumbID, '_wp_attachment_image_alt', true);

$imageBig = wp_get_attachment_image_src($thumbID, 'full');
$imageThumb = wp_get_attachment_image_src($thumbID, 'theme-thumb-2');
?>
<?php if (sizeof($image) > 0): ?>

    <div class="gallery">
        <div class="main-image-box">
            <a id="main-image-link" href="javascript:void(0);" onclick="viewProductLightBox(this);"
               data-image-number="1">
                <img id="main-image" src="<?php echo $image[0]; ?>" alt="" class="img-responsive">
            </a>
        </div>
        <div class="thumbnails lightbox">
            <a id="image-preview-0" href="javascript:void(0);"
               onclick="changeProductMainImage('<?php echo $image[0]; ?>', 1);" class="thumb">
                <img src="<?php echo $imageThumb[0]; ?>" alt="" class="img-responsive">
            </a>
            <a id="image-0" href="<?php echo $imageBig[0]; ?>" class="fancybox image_order_1 d-none"
               rel="gallery-<?php echo $queryCounter; ?>"></a>

            <?php

            $attachmentIds = $product->get_gallery_image_ids();
            ?>

            <?php $imageCounter = 1; ?>
            <?php foreach ($attachmentIds as $attachmentId): ?>
                <?php
                $image = wp_get_attachment_image_src($attachmentId, 'theme-thumb-1');
                $imageAlt = get_post_meta($attachmentId, '_wp_attachment_image_alt', true);

                $imageBig = wp_get_attachment_image_src($attachmentId, 'full');
                $imageThumb = wp_get_attachment_image_src($attachmentId, 'theme-thumb-2');
                ?>
                <a id="image-preview-<?php echo $imageCounter; ?>" href="javascript:void(0);"
                   onclick="changeProductMainImage('<?php echo $image[0]; ?>', <?php echo $imageCounter + 1; ?>);"
                   class="thumb">
                    <img src="<?php echo $imageThumb[0]; ?>" alt="" class="img-responsive">
                </a>
                <a id="image-<?php echo $imageCounter; ?>" href="<?php echo $imageBig[0]; ?>"
                   class="fancybox image_order_<?php echo $imageCounter + 1; ?> d-none" rel="gallery-<?php echo $queryCounter; ?>"></a>
                <?php $imageCounter++; ?>
            <?php endforeach; ?>

        </div>
    </div>

<?php endif; ?>
