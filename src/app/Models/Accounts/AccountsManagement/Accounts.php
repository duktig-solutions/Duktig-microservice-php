<?php
/**
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Accounts\AccountsManagement;

use App\Models\Accounts\BaseAccount;

/**
 * Class Accounts
 *
 * @package App\Models
 */
class Accounts extends BaseAccount
{
	
	/**
     * User accessible fields to return
     *
     * @access protected
     * @var array
     */
    protected array $whitelist = [
        'userId',
		'roleId',
		'status',
		'email',
		'firstName',
		'lastName',
		'displayName',
		'gender',
		'dob',
		'photo',
		'aboutMe',
		'country',
		'state',
		'city',
		'zip_code',
		'address_line1',
		'address_line2',
		'phone',
		'provider',
		'profileCompleteLevel',
		'adminComments',
		'website',
		'notEditableFields',
		'lastLoginIP',
		'dateRegistered',
		'dateUpdated',
		'dateLastLogin',
		'dateLastAction'
    ];
	
	/**
	 * Fetch Users
	 *
	 * @param $offset
	 * @param $limit
	 * @return array
	 * @throws \Exception
	 */
	public function fetchRows($offset, $limit): array
    {

		return $this->fetchAllAssoc(
			"select 
				".implode(',', $this->whitelist)."
	    	from ".$this->table." limit ?, ?",
			[$offset, $limit]
		);
	}

}