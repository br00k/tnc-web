<?php
class Core_Resource_Conferences extends TA_Model_Resource_Db_Table_Abstract
{

	protected $_name = 'conferences';

	protected $_primary = 'conference_id';

	protected $_rowClass = 'Core_Resource_Conference_Item';

	public function init() {}

	/**
	 * Gets conference by primary key
	 * @return object Core_Resource_Conference_Item
	 */
	public function getConferenceById($id)
	{
		return $this->find( (int)$id )->current();
	}

	public function getConferenceByHostname($hostname)
	{
		return $this->fetchRow(
					$this->select()
					->where('hostname = ?', $hostname)
				);
	}

	public function getConferences($paged=null, $order=array())
	{
		$grid = array();
		$grid['cols'] = $this->getGridColumns();
		$grid['primary'] = $this->_primary;

		$select = $this->select();

		if (!empty($order[0])) {
			$order = 'lower('.$order[0].') '.$order[1];
		} else {
			$order = 'lower(name) ASC';
		}
		$select->order($order);

		$select->from( 'conferences', array_keys($this->getGridColumns()) );

		if ($paged) {

			$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);

			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber( (int)$paged )
					  ->setItemCountPerPage(20);

			$grid['rows'] = $paginator;
			return $grid;
		}

		$grid['rows'] = $this->fetchAll($select);
		return $grid;

	}

	/**
	 * Convenience method to get grid columns
	 *
	 * @return array
	 * @todo in php5.3 I can add lambda's for modifiers
	 */
	private function getGridColumns()
	{
		return array(
			// conference_id is hidden so I don't have to provide a label
			'conference_id' => array('field' => 'conference_id', 'sortable' => true, 'hidden' => true),
			'name' => array('field' => 'name', 'label' => 'Name', 'sortable' => true),
			'abbreviation' => array('field' => 'abbreviation', 'label' => 'Abbreviation', 'sortable' => true),
			'email' => array('field' => 'email', 'label' => 'Email', 'sortable' => true),
			'hostname' => array('field' => 'hostname', 'label' => 'Hostname', 'sortable' => false, 'hidden' => true)
		);
	}

	/**
	 * Mockup, just for demo
	 * @todo: check or remove
	 */
	public function createTimeslots($conferenceId)
	{
		$db = $this->getAdapter();

$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 15:30:00+02', '2011-05-19 16:00:00+02', 0, 2, 1);
EOD;
$db->query($query);

$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 16:00:00+02', '2011-05-19 17:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 09:00:00+02', '2011-05-16 10:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 10:30:00+02', '2011-05-16 11:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 11:00:00+02', '2011-05-16 12:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 12:30:00+02', '2011-05-16 14:00:00+02', 0, 3, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 14:00:00+02', '2011-05-16 15:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 15:30:00+02', '2011-05-16 16:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-16 16:00:00+02', '2011-05-16 17:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 09:00:00+02', '2011-05-17 10:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 10:30:00+02', '2011-05-17 11:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 11:00:00+02', '2011-05-17 12:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 12:30:00+0',  '2011-05-17 14:00:00+02', 0, 3, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 14:00:00+02', '2011-05-17 15:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 15:30:00+02', '2011-05-17 16:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-17 16:00:00+02', '2011-05-17 17:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 09:00:00+02', '2011-05-18 10:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 10:30:00+02', '2011-05-18 11:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 11:00:00+02', '2011-05-18 12:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 12:30:00+02', '2011-05-18 14:00:00+02', 0, 3, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 14:00:00+02', '2011-05-18 15:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 15:30:00+02', '2011-05-18 16:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-18 16:00:00+02', '2011-05-18 17:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 09:00:00+02', '2011-05-19 10:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 10:30:00+02', '2011-05-19 11:00:00+02', 0, 2, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 11:00:00+02', '2011-05-19 12:30:00+02', 0, 1, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 12:30:00+02', '2011-05-19 14:00:00+02', 0, 3, 1);
EOD;
$db->query($query);
$query = <<<'EOD'
INSERT INTO "public"."timeslots" ("tstart", "tend", "number", "type", "conference_id")
VALUES ('2011-05-19 14:00:00+02', '2011-05-19 15:30:00+02', 0, 1, 1);
EOD;
$db->query($query);




		return true;
	}

}

