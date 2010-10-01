<?php
class HabtmAclBehavior extends ModelBehavior {
/**
 * Maps ACL type options to ACL models
 *
 * @var array
 * @access protected
 */
    var $__typeMaps = array('requester' => 'Aro', 'controlled' => 'Aco');
/**
 * Sets up the configuation for the model, and loads ACL models if they haven't been already
 *
 * @param mixed $config
 */
    function setup(&$model, $config = array()) {
        if (is_string($config)) {
            $config = array('type' => $config);
        }
        $this->settings[$model->name] = am(array('type' => 'requester'), $config);
        $type = $this->__typeMaps[$this->settings[$model->name]['type']];

        if (!ClassRegistry::isKeySet($type)) {
            uses('model' . DS . 'db_acl');
            $object =& new $type();
        } else {
            $object =& ClassRegistry::getObject($type);
        }
        $model->{$type} =& $object;
        if (!method_exists($model, 'parentNode')) {
            trigger_error("Callback parentNode() not defined in {$model->name}", E_USER_WARNING);
        }
    }
/**
 * Retrieves the Aro/Aco node for this model
 *
 * @param mixed $ref
 * @return array
 */
    function node(&$model, $ref = null) {
        $type = $this->__typeMaps[low($this->settings[$model->name]['type'])];
        if (empty($ref)) {
            $ref = array('model' => $model->name, 'foreign_key' => $model->id);
        }
        return $model->{$type}->node($ref);
    }
/**
 * Creates new ARO/ACO nodes bound to this record
 *
 * @param boolean $created True if this is a new record
 */
    function afterSave(&$model, $created) {
        $type = $this->__typeMaps[low($this->settings[$model->name]['type'])];
        $parents = $model->parentNode();
        $data = $model->read();
        $parentArray = array();
        if (!empty($parents)) {
            foreach ($parents as $parent)
            {
                $parentArray[] = $this->node($model, $parent);
            }

        }
        else
        {
            $parentArray = array();
        }

		if (!$created) {
			$nodes = $this->node($model);
//			$data['id'] = isset($node[0][$type]['id']) ? $node[0][$type]['id'] : null;

	        if (!empty($nodes))
	        {
	            foreach ($nodes as $node)
	            {
	                if($model->{$type}->deleteAll(array('alias' => $data[$model->name][$model->displayField])))
	                {
	                    $return = TRUE;
	                }
	            }
	        }
		}

        foreach ($parentArray as $parent)
        {
			$data = array(
				'parent_id' => isset($parent[0][$type]['id']) ? $parent[0][$type]['id'] : null,
				'model' => $model->alias,
				'foreign_key' => $model->id
			);
        	$model->{$type}->create();
        	$model->{$type}->save($data);
        }
    }
/**
 * Destroys the ARO/ACO nodes bound to the deleted record
 *
 */
    function beforeDelete(&$model) {
        $data = $model->read();
        $return = FALSE;
        $type = $this->__typeMaps[low($this->settings[$model->name]['type'])];
        $nodes = $this->node($model);
        if (!empty($nodes)) {
            foreach ($nodes as $node)
            {
                if($model->{$type}->deleteAll(array('alias' => $data[$model->name][$model->displayField])))
                {
                    $return = TRUE;
                }
            }
        }

        return $return;
    }

}
?>