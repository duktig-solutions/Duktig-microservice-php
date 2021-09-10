<?php
/**
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Accounts\Profiles;

use App\Models\Accounts\BaseAccount;

/**
 * Class Profiles
 *
 * @package App\Models
 */
class Profiles extends BaseAccount
{

	/**
     * User accessible fields to return
     *
     * @access protected
     * @var array
     */
    protected $whitelist = [
        'userId',
		'displayName',
        'firstName',
        'lastName',
		'photo'
    ];

	/**
	 * Fetch profiles by ids array
	 *
	 * @param array $a_ids
	 * @return array
	 */
	public function fetchAllByIds(array $a_ids) : array {

        return $this->fetchAllAssoc(
            "select 
				".implode(',', $this->whitelist)."
            from ".$this->table."
            where userId in('".implode("','", $a_ids)."')
            order by firstName, lastName"
        );

	}

	/**
	 * Fetch User profile by Id
	 *
	 * @param int $userId
	 * @return array
	 * @throws \Exception
     * 
     * @todo check account status
	 */
	public function fetchById(int $userId) : array {
		return $this->fetchFieldsAssocByWhere($this->table, $this->whitelist, ['userId' => $userId, 'status' => USER_STATUS_ACTIVE]);
	}

}