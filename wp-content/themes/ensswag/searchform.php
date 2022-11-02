<?php
/* Custom search form */
?>
<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url(home_url('/')); ?>"
      class="input-group mb-3">
    <div>
        <label class="screen-reader-text" for="s">Search for:</label>
        <input type="text" value="" name="s" id="s" placeholder="Search">
        <input type="submit" id="searchsubmit" value="Search">
    </div>
</form>