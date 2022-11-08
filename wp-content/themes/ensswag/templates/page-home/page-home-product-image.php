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
               data-image-number="2">
                <img id="main-image" src="<?php echo $image[0]; ?>" alt="" class="img-responsive" loading="lazy" />
            </a>
        </div>
        <div class="thumbnails">
           <?php
            $attachmentIds = array_merge([$thumbID], $product->get_gallery_image_ids());
            $chunks = array_reduce($attachmentIds, $redfunc, array());
            ?>

            <?php if ($chunks && sizeof($chunks) > 0): ?>

                <div id="carouselProductImages" class="carousel slide" data-bs-ride="true">
                    <div class="carousel-inner">
                        <?php $chunkCounter = 1; ?>
                        <?php $imageCounter = 1; ?>
                        <?php foreach ($chunks as $chunk): ?>
                            <div class="carousel-item <?php echo(($chunkCounter == 1) ? 'active' : ''); ?>">
                                <div class="carousel-images-holder">
                                    <?php foreach ($chunk as $attachmentId): ?>
                                        <?php
                                        $image = wp_get_attachment_image_src($attachmentId, 'theme-thumb-1');
                                        $imageAlt = get_post_meta($attachmentId, '_wp_attachment_image_alt', true);

                                        $imageBig = wp_get_attachment_image_src($attachmentId, 'full');
                                        $imageThumb = wp_get_attachment_image_src($attachmentId, 'theme-thumb-2');
                                        ?>
                                        <a id="image-preview-<?php echo $imageCounter; ?>" href="javascript:void(0);"
                                           onclick="changeProductMainImage('<?php echo $image[0]; ?>', <?php echo $imageCounter + 1; ?>);"
                                           class="thumb" data-ind="<?php echo ($chunkCounter-1); ?>">
                                            <img src="<?php echo $imageThumb[0]; ?>" alt="<?php echo $imageAlt; ?>" class="img-responsive" loading="lazy" />
                                        </a>
                                        <a id="image-<?php echo $imageCounter; ?>" href="<?php echo $imageBig[0]; ?>"
                                           class="image_order_<?php echo $imageCounter + 1; ?> d-none"
                                           rel="gallery-<?php echo $queryCounter; ?>" data-lightbox="example-set"></a>
                                        <?php $imageCounter++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php $chunkCounter++; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-indicators">
                        <?php $indicatorCounter = 0; ?>
                        <?php foreach ($chunks as $chunk): ?>
                            <button type="button" data-bs-target="#carouselProductImages"
                                    data-bs-slide-to="<?php echo $indicatorCounter; ?>"
                                    class="<?php echo(($indicatorCounter == 0) ? 'active' : ''); ?>"
                                    aria-current="<?php echo(($indicatorCounter == 0) ? 'true' : ''); ?>"
                                    aria-label="Slide <?php echo($indicatorCounter + 1); ?>"></button>
                            <?php $indicatorCounter++; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>

<?php endif; ?>
