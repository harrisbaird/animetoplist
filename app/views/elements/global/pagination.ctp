<div class="pagination">
	<div class="right">
		<?php
		if($this->Paginator->hasPrev()) {
			echo $this->Paginator->prev('« Previous');
		}
		echo $this->Paginator->numbers(array('separator' => ''));
		if($this->Paginator->hasNext()) {
			echo $this->Paginator->next('Next »');
		}
		?>
	</div>
	
	<strong>Page <?php echo $this->Paginator->counter(); ?></strong>
</div>