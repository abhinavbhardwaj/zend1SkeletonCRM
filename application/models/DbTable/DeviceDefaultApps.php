<?php

/**
 * DeviceDefaultApps model class used to handel bal_child_device_default_apps
 * table where we perform task like get all default aps data 
 * @category   balance
 * @package    Device
 * @subpackage Device default aps
 * @copyright  Copyright (c) A3logics. (http://www.a3logics.in)
 * @version    Release: 1.0
 *
 */
class Application_Model_DbTable_DeviceDefaultApps extends Zend_Db_Table_Abstract
{

    protected $_name = 'bal_child_device_default_apps'; //table name;

    /*
     * function for get All data from table bal_child_device_default_apps
     */

    public function getAllDefultAps($where)
    {
        return $this->fetchAll($where);
    }

    public function getAllApps()
    {
        return $this->fetchAll();
    }

    public function isProductiveApp($package_name)
    {
        $result = $this->fetchRow(array('package_name = ?' => $package_name, 'is_productive = 1'));
        if ($result) {
            return TRUE;
        }

        return FALSE;
    }

}
