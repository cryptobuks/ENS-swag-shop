
        <?php include 'templates/footer/footer.php'; ?>

        <?php wp_footer(); ?>

        <script src="<?php echo TEMPLATEDIR; ?>/react/build/static/js/main.c9bafb47.js"></script></body></html>

        <!-- Mockup Modal -->
        <div class="modal fade" id="mockupModal" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">&nbsp;</div>
            </div>
        </div>

        <!-- Add Cart Modal -->
        <div class="modal fade" id="addCartModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">&nbsp;</div>
            </div>
        </div>

        <!-- Loader Cart Modal -->
        <div class="modal fade" id="loaderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="spinner-border text-light text-center" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>