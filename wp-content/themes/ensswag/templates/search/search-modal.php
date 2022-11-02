<div class="modal fade"
     id="searchModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="/" method="get">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel"><?php _e('Search Products', 'template'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php _e('Close', 'template'); ?>"></button>
                </div>
                <div class="modal-body">
                    <div class="input-holders">
                        <input type="text" name="s" id="search" value="<?php the_search_query(); ?>" placeholder="<?php _e('search', 'template'); ?>" />
                        <input type="submit" value="<?php _e('Search', 'template'); ?>" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>