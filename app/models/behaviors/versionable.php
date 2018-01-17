<?php
App::import('Model', 'Revision');

/**
 * Versionable behavior
 *
 * Saves sets of changes like a version control.
 */
class VersionableBehavior extends ModelBehavior {

	public $Revision;
	/** Reference to the model calling the behavior */
	private $_model;
	/** Lower case model name in plural */
	private $_type;
	private $_versionedFields = array();
	private $_noFieldsError = 'Specify fields to version as behavior settings. \'Versionable\' => array(\'field1\', \'field2\')';
	private $_doVersion = false;
    private $_rollback;

	/**
	 * When object is initialized
	 *
	 * @param $model Model object reference
	 * @param $versionedFields Array of field names to keep under version control
	 * @return void
	 */
	function setup($model, $versionedFields = array()) {
		if (empty($versionedFields)) {
			trigger_error($this->_noFieldsError);
		}

		$this->Revision = ClassRegistry::init('Revision');
		$this->_model = $model;
		$this->_type = low($this->_model->name);
        $this->_versionedFields = $versionedFields;
	}

    /**
     * After save callback. Save a new revision here.
     *
     */
    function afterSave() {
    	if ($this->_doVersion) {
    		$this->saveRevision();
    	}
    	return true;
    }

	function currentRevision($model, $nodeId) {
		$conditions = "{$this->Revision->name}.node_id = $nodeId AND {$this->Revision->name}.type = '{$this->_type}'";
		$revision = $this->Revision->find('first', array('conditions' => $conditions, 'order' => "{$this->Revision->name}.revision_number DESC", 'limit' => '0,1'));
		
		return $this->getRevision($model, $nodeId, $revision['Revision']['revision_number']);
	}

    function getRevision($model, $nodeId, $versionNumber) {
        $revision = $this->getRevisionData($model, $nodeId, $versionNumber);
        //var_dump($revision);
        $revContent = $revision['Revision']['content'];
        $revData = unserialize($revContent);

        $data = $model->findById($nodeId);

        // Unset the versioned fields from data
        foreach ($this->_versionedFields as $field) {
            unset($data[$model->name][$field]);
        }

        $data['Revision'] = $revision['Revision'];

        $data[$model->name] = am($data[$model->name], $revData[$model->name]);
        return $data;
    }

    /**
     * Get revisions for an item
     *
     * @param Model $model
     * @param int $id
     * @param int $limit
     * @return array
     */
    function getRevisions($model, $id, $limit = null) {
        $id = intval($id);
        $conditions = "{$this->Revision->name}.node_id = $id AND {$this->Revision->name}.type = '{$this->_type}'";
        $order = "{$this->Revision->name}.revision_number DESC";
    	$revisions = $this->Revision->find('all', compact('conditions', 'order', 'limit'));
    	return $revisions;
    }

    /**
     * Turn on versioning for current model
     *
     * @return void
     */
    function doVersion() {
    	$this->_doVersion = true;
    }

    /**
     * Stores a copy of choosen fields from the current model $data property in
     * the revisions table. (Only if something has changed from the last revision.)
     *
     * @return mixed boolen 'false' or saved data on success
     */
    function saveRevision($rollback_number = null) {
        $nodeId = intval($this->_model->id);

        $latestRev = $this->Revision->find("{$this->Revision->name}.node_id = $nodeId AND {$this->Revision->name}.type = '{$this->_type}'", null, "{$this->Revision->name}.revision_number DESC");
        $revNo = 1;
        if (!empty($latestRev)) {
            $revNo = intval($latestRev['Revision']['revision_number']) + 1;
        }

        // Archive only the choosed fields
        $archive = array();
        foreach ($this->_versionedFields as $field) {
            $modelFields = explode('.', $field);
        	if (isset($this->_model->data[$modelFields['0']][$modelFields[1]])) {
                $archive[$modelFields[0]][$modelFields[1]] = $this->_model->data[$modelFields[0]][$modelFields[1]];
        	}
        }

        // Get the user_id and ip address
		$userId = Set::extract($_SESSION, 'Auth.User.id');
		$ipAddress = $_SERVER['REMOTE_ADDR'];

        // Encode content field
        $archive = serialize($archive);
        $revData['Revision'] = array(
            'type' => $this->_type,
            'node_id' => $nodeId,
            'content' => $archive,
            'revision_number' => $revNo,
            'user_id' => $userId,
            'ip_address' => $ipAddress
        );

        if($rollback_number != null) {
            $revData['Revision'] = am(
                $revData['Revision'],
                array(
                    'is_rollback' => 1,
                    'rollback_number' => $rollback_number
                )

            );
        }

        // Save only if the content has changed
        if (!isset($latestRev['Revision']['content'])
            or $latestRev['Revision']['content'] !== $revData['Revision']['content']
            or $rollback_number != null) {
            return $this->Revision->save($revData);
        }

        return false;
    }

    function rollback($model, $node_id, $revision_number, $reason = null) {
        //Create a new revision
        $revision = $this->Revision->find('first', array('conditions' => array('type' => $this->_type, 'node_id' => $node_id, 'revision_number' => $revision_number)));
        $data = unserialize($revision['Revision']['content']);

		//Set the reason
		if($reason) {
			$data['Series']['reason'] = String::insert('Rollback to revision :revision - :reason', array('revision' => h($revision_number), 'reason' => $reason));
		} else {
			$data['Series']['reason'] = String::insert('Rollback to revision :revision', array('revision' => h($revision_number)));
		}

		$this->_model->data = $data;

        $this->saveRevision($revision_number);

        //Update the original model & any HABTM data
        $this->_model->save();
    }

    /**
     * Find a specific revision
     *
     * @param Model $model
     * @param int $id
     */
    private function getRevisionData($model, $node_id, $revision_number) {
        $revision = $this->Revision->find('first', array('conditions' => array('node_id' => $node_id, 'revision_number' => $revision_number, "{$this->Revision->name}.type" => $this->_type), 'recursive' => -1));
        return $revision;
    }
}
