<?php
/**
 * Roles management Model
 * 
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 *
 */
namespace App\Models\Accounts\AccountsManagement;

use App\Models\Accounts\BaseAccount;

/**
 * Class Roles
 *
 * @package App\Models
 */
class Roles extends BaseAccount {

	/**
	 * Each model can contain it's own database table name(s).
	 *
	 * @access protected
	 * @var string
	 */
	protected $table = 'roles';

    /**
     * @throws \Exception
     */
    public function fetchAll(): array
    {
        return $this->fetchAllAssoc("Select * from ".$this->table." order by roleName ");
    }

}	