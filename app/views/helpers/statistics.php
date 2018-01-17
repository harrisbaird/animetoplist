<?php
class StatisticsHelper extends AppHelper {
	var $helpers = array('Html', 'Time', 'Number');

	var $fieldNames = array('unique_views' => 'Unique Pageviews', 'total_views' => 'Total Pageviews', 'unique_in' => 'Unique In', 'total_in' => 'Total In', 'unique_out' => 'Unique Out', 'total_out' => 'Total Out', 'rank' => 'Rank');

	var $types = array(
		//'pv' => array('text' => 'Pageviews', 'fields' => array('unique_views', 'total_views')),
		'in' => array('text' => 'Users in', 'fields' => array('unique_in', 'total_in')),
		'out' => array('text' => 'Users out', 'fields' => array('unique_out', 'total_out')),
		'rank' => array('text' => 'Rank', 'fields' => array('rank'))
	);
	var $durations = array(
		'7d' => array('text' => '7 days'),
		'1m' => array('text' => '1 month'),
		'3m' => array('text' => '3 months'),
		'6m' => array('text' => '6 months'),
		'1y' => array('text' => '1 year'),
		'max' => array('text' => 'All time', 'header' => 'from the beginning')
	);

	var $data = array();

	function setData($data) {
		$this->data = $data;
	}

	/**
	 * Generate a chart using the Google Charts API
	 *
	 * @param array $data
	 * @return string
	 */
	function chart($data) {
		$date = Set::extract('/Stat/date', $data);

		$chartData = array();
		$highest = 0;

		//Fetch the data and find the highest value
		foreach($this->types[$this->data['type']]['fields'] as $field) {
			$chartData[$field] = Set::extract('/Stat/'.$field, $data);

			$max = @max($chartData[$field]);
			if($max > $highest) $highest = $max;
		}

		$divider = $highest / 100;

		//Divide all values so they fit within the correct range
		$dataText = array();
		foreach($this->types[$this->data['type']]['fields'] as $field) {
			for($i = 0; $i < count($chartData[$field]); $i++) {
				$chartData[$field][$i] = @round($chartData[$field][$i] / $divider);
				if($this->data['type'] == 'rank') $chartData[$field][$i] = $chartData[$field][$i];
			}

			//If only one entry, add an extra one so the chart can be generated
			if(count($chartData[$field]) == 1) {
				$chartData[$field][1] = $chartData[$field][0];
			}

			$dataText[] = implode(',', $chartData[$field]);
		}

		//Combine the data into a string
		$data = implode('|', $dataText);

		//Only show legend for charts with multiple series
		$legend = $this->data['type'] == 'rank' ? '' : '&chdl=Unique|Total';

		//Y axis values
		$range = $this->__distributedRange(0, $highest, 6);
		$previous = '';
		foreach($range as $value) {
			$value = $this->__groupNumbers($value);
			if($value !== $previous) {
				$yData[] = $value;
			} else {
				$yData[] = ' ';
			}
			$previous = $value;
		}
		$yData = implode('|', $yData);

		//X axis values
		$range = $this->__distributedRange(0, count($date), 4, true);
		$xData = array();
		foreach($range as $value) {
			$timestamp = strtotime($date[$value]);
			$xData['top'][] = date('M j', $timestamp);
			$xData['bottom'][] = date('Y', $timestamp);
		}

		$xData['top'] = implode('|', $xData['top']);
		$xData['bottom'] = implode('|', $xData['bottom']);

		return $this->Html->image('http://chart.apis.google.com/chart?cht=lc&chs=460x220&chd=t:' . $data . '&chco=99d82d,194976&chdlp=b' . $legend . '&chxt=x,x,y&chxl=0:|' . $xData['top'] .  '|1:|' . $xData['bottom'] . '|2:|' . $yData, array('class' => 'no-lazyload'));
	}

	/**
	 * Generate a series of links
	 * The currently active type will remain as text
	 *
	 * @param string $activeSlug
	 * @return string
	 */
	function links($activeSlug) {
		if(in_array($activeSlug, array_keys($this->types))) {
			$type = 'types';
		} else {
			$type = 'durations';
		}

		$links = array();

		foreach($this->{$type} as $slug => $data) {
			if($type == 'types') {
				$data['duration'] = $this->data['duration'];
				$data['type'] = $slug;
			} else {
				$data['duration'] = $slug;
				$data['type'] = $this->data['type'];
			}

			if($activeSlug == $slug) {
				$links[] = $data['text'];
			} else {
				$links[] = $this->Html->link($data['text'], array('controller' => 'stats', 'action' => 'view', $this->data['siteId'], $data['type'], $data['duration']));
			}
		}

		return implode(', ', $links);
	}

	/**
	 * Generate a header containing the type, duration
	 * and how the data is grouped
	 *
	 * @return string
	 */
	function header() {
		if(!empty($this->durations[$this->data['duration']]['header'])) {
			$header = $this->types[$this->data['type']]['text'] . ' ' . $this->durations[$this->data['duration']]['header'];
		} else {
			$header = $this->types[$this->data['type']]['text'] . ' during the past ' . $this->durations[$this->data['duration']]['text'];
		}

		if(!empty($this->data['group'])) {
			$header .= ', grouped by ' . $this->data['group'];
		}

		return $header;
	}

	/**
	 * Generate a table, fields will be automatically
	 * selected using the type setting
	 *
	 * @param array $data
	 * @return string
	 */
	function table($data) {
		$table = '';

		//Generate table headers
		$headerText = array('Date');
		foreach($this->types[$this->data['type']]['fields'] as $field) {
			$headerText[] = $this->fieldNames[$field];
		}

		$table .= '<thead>' . $this->Html->tableHeaders($headerText) . '</thead>';

		$table .= '<tbody>';

		//Generate table rows
		foreach($data['data'] as $stat) {
			$rowData = array($stat['Stat']['date']);
			foreach($this->types[$this->data['type']]['fields'] as $field) {
				$rowData[] = $this->Number->format($stat['Stat'][$field]);
			}

			$table .= $this->Html->tableCells($rowData);
		}

		$table .= '</tbody>';

		return $this->Html->tag('table', $table);
	}

	/**
	 * Find a equally spaced group of numbers between a
	 * certain range
	 *
	 * @param integer $start
	 * @param integer $end
	 * @param integer $count
	 * @return array
	 */
	function __distributedRange($start, $end, $count, $timestamp = false) {
	    $increment = ($end - $start) / ($count + 1);

	    $result = array(round($start));
	    for ($i = $start + $increment; $i < $end ; $i += $increment) {
	        $result[] = floor($i);
		}

		if($timestamp) $end = $end - 1;

		$result[] = round($end);

	    return $result;
	}

	/**
	 * Group number by thousands
	 *
	 * @param integer $number
	 * @param boolean $extended
	 * @return string
	 */
	function __groupNumbers($number, $extended = false) {
		$units = array('', 'K', 'M', 'B');

		if($number < 1000) {
			return $number;
		}

		$pow = floor(($number ? log($number) : 0) / log(1000));
		$pow = min($pow, count($units) - 1);

		$number /= pow(1000, $pow);

		if($units[$pow] == 'M' && $extended) {
			return round($number, 1) . ' ' . $units[$pow];
		} else {
			return round($number) . ' ' . $units[$pow];
		}
	}
}
?>
