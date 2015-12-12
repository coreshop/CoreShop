<?php if($this->pageCount > 1) { ?>
    <!-- Pagination & Results Starts -->
    <div class="row">
        <!-- Pagination Starts -->
        <div class="col-sm-6 pagination-block">
            <ul class="pagination">
                <?php if (isset($this->previous)) { ?>
                    <li><a href="<?= substr($this->url(array('page' => $this->first)), 1); ?>">«</a></li>
                <?php } ?>
                <?php foreach ($this->pagesInRange as $page) { ?>
                    <li class="<?=$page == $this->current ? "active" : ""?>"><a href="<?= substr($this->url(array('page' => $page)), 1); ?>"><?=$page?></a></li>
                <?php } ?>

                <?php if (isset($this->next)) { ?>
                    <li><a href="<?= substr($this->url(array('page' => $this->last)), 1); ?>">»</a></li>
                <?php } ?>
            </ul>
        </div>
        <!-- Pagination Ends -->
        <!-- Results Starts -->
        <div class="col-sm-6 results">
            <?php
                echo sprintf("Showing %d to %d of %d (%d Pages)", $this->firstItemNumber, $this->lastItemNumber, $this->totalItemCount, $this->pageCount);
            ?>
        </div>
        <!-- Results Ends -->
    </div>

    <!-- Pagination & Results Ends -->
<?php } ?>
