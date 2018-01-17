<div class="review">
   <div class="review-header">
        <div class="grid_4 alpha">
            <ul class="info">
                <li><strong class="author"><?php echo $review['User']['username']; ?></strong></li>
                <li>Added: <?php echo $this->Time->niceShort($review['created']); ?></li>
            </ul>
        </div>
        
        <div class="grid_2 omega">
            <ul class="ratings module">
                <li><strong>Story:</strong> <span><?php echo $this->At->reviewText($review['story']); ?></span></li>
                <li><strong>Characters:</strong> <span><?php echo $this->At->reviewText($review['characters']); ?></span></li>
                <li><strong>Animation:</strong> <span><?php echo $this->At->reviewText($review['animation']); ?></span></li>
                <li><strong>Sound:</strong> <span><?php echo $this->At->reviewText($review['sound']); ?></span></li>
                <li class="overall"><strong>Overall:</strong> <span><?php echo $this->At->reviewText($review['overall']); ?></span></li>
            </ul>
        </div>

   

   </div>
   
    <p><?php echo nl2br(h($review['body'])); ?></p>
</div>