namespace {namespace};

use Axm;
use Axm\Model;

/**
* Modelo {class}
*
* @category App
* @package Models
*/
class {class} extends Model
{

/** @var string name of DBGroup */
protected $DBGroup = '{dbGroup}';

/** @var string name of table */
protected static $tableName = '{table}';

/** @var string column primaryKey of table, is default id */
protected static $primaryKey = 'id';

/** @var boolean true to disable insert/update/delete */
protected static $readOnly = false;

protected $useAutoIncrement = true;
protected $insertID = 0;
protected $returnType = {return};
protected $useSoftDeletes = false;
protected $protectFields = true;
protected $allowedFields = [];

// Dates
protected $useTimestamps = false;
protected $dateFormat = 'datetime';
protected $createdField = 'created_at';
protected $updatedField = 'updated_at';
protected $deletedField = 'deleted_at';

// Callbacks
protected $allowCallbacks = true;
protected $beforeInsert = [];
protected $afterInsert = [];
protected $beforeUpdate = [];
protected $afterUpdate = [];
protected $beforeFind = [];
protected $afterFind = [];
protected $beforeDelete = [];
protected $afterDelete = [];

// Validation
/** @var boolean true to skin validation */
public $skipValidation = false;

/**
* Example:
* protected $validationRules = [
* 'email' => 'required|email|min:5|max:30',
* 'password' => 'required|min:8|max:30',
* ];
*
*/
protected $validationRules = [];

/** @var array validation message */
protected $validationMessages = [];

/** @var boolean true to clean validation */
protected $cleanValidationRules = true;

public function atributes(): array
{
return [];
}

/**
* Example:
* return [
* 'name' => $name,
* 'email'=> $email
* ];
*/
public function labels(): array
{
return [];
}

}