<?php
/**
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 *
 * This is hard to make all in time, but have to complete to reach Talin.
 * @Listening Ordel (feat. Kyson & Beau Diako) - Single
 */
namespace App\Models\Accounts\Account;

use App\Models\Accounts\BaseAccount;

/**
 * Class Account
 *
 * @package App\Models
 */
class Account extends BaseAccount
{

    /**
     * User accessible fields to return
     *
     * @access protected
     * @var array
     */
    protected array $whitelist = [
        'userId',
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
		'website',
		'notEditableFields',
		'lastLoginIP',
		'dateRegistered',
		'dateUpdated',
		'dateLastLogin'
    ];

	/**
	 * Update User last authorization date
	 *
	 * @param string $userId
     * @param array|null $data
	 * @return int
	 */
	public function updateLastAuthById(string $userId, ?array $data = []) : int {

        if(!isset($dat['dateLastLogin'])) {
            $data['dateLastLogin'] = date('Y-m-d H:i:s');
        }

		return $this->update(
			$this->table,
			$data,
			['userId' => $userId]
		);

	}

}