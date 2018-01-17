<?php
class UpdateTitlesShell extends Shell {
        var $SeriesTitle;

        function main() {
                $this->SeriesTitle = ClassRegistry::init('SeriesTitle');
		$titles = $this->SeriesTitle->find('all', array('fields' => array('contain' => false)));
		foreach($titles as $title) {
			$this->SeriesTitle->save($title);
		}

                return true;
        }
}
?>
