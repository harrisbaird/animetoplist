<?php
class SeriesReview extends AppModel {
    var $name = 'SeriesReview';
    
    var $belongsTo = array('Series', 'User');
    
    function calculateOverall($data) {
    	$inputs = array('story', 'characters', 'animation', 'sound');
    	
    	$overall = 0;
		$overallDivide = 0;
		
		foreach($inputs as $input) {
			$inputValue = $data['SeriesReview'][$input];
			if($inputValue > 0) {
				$overall += $inputValue;
				$overallDivide++;
			}
		}
		
		if($overallDivide == 0) $overallDivide = 1;

		return floor($overall / $overallDivide);
    }
}
?>