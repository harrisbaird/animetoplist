<?php
class UpdatestatsShell extends Shell {
    var $Stat;
    var $Site;

    function main($data) {
        $this->Stat = ClassRegistry::init('Stat');
        $this->Site = ClassRegistry::init('Site');
        $this->Setting = ClassRegistry::init('Setting');
        $sites = $this->Site->find('all', array('contain' => false));

        $dbDate = $this->Setting->find('first', array('conditions' => array('Setting.name' => 'App.stats.date')));
        $today = date("Y-m-d");
        $alexaTimestamp = $this->Setting->find('first', array('conditions' => array('Setting.name' => 'App.stats.alexa')));
        $twoWeeks = 60 * 60 * 24 * 14;

        if($today != $dbDate['Setting']['value']) {


            if(microtime(true) > $alexaTimestamp['Setting']['value']) {
                $this->Setting->id = $alexaTimestamp['Setting']['id'];
                $this->Setting->saveField('value', microtime(true) + $twoWeeks);

                foreach($sites as $site) {
                    $domain = parse_url($site['Site']['url']);
                    $alexaStats = $this->Stat->alexa($domain['host']);

                    $this->Site->id = $site['Site']['id'];
                    $this->Site->saveField('is_adult', $alexaStats['adult']);

                    $this->Stat->createStat($site['Site']['id'], $alexaStats);

                    sleep(1);
                }
            } else {
                foreach($sites as $site) {
                    $this->Stat->createStat($site['Site']['id']);
                }
            }

            $this->Site->updateRanks();

            //Update the data in the db
            $this->Setting->id = $dbDate['Setting']['id'];
            $this->Setting->saveField('value', $today);
        }

        return true;
    }

    function ranks() {
        $this->Site = ClassRegistry::init('Site');
        $this->Site->updateRanks();
    }

}
?>
