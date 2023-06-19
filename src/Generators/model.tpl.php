/**
 * Modelo <?= $class ?>
 * 
 * @category App
 * @package Models
 */


class <?= $class ?> extends Model
{

    /** @var string name of table */
    protected static $tableName;

    /** @var string column primaryKey of table, is default id */
    protected static $primaryKey = 'uid';

    /** @var boolean true to disable insert/update/delete */
    protected static $readOnly = false;


    /** @var boolean true to skin validation */
    public $skinValidation = false;


    public function atributes(): array
    {
        return [];
    }


    /**
    *   Example:
    *   protected $validationRules = [  
    *       'email'     => 'required|email|min:5|max:30',
    *       'password'  => 'required|min:8|max:30',
    *   ];
    *
    */
    protected $validationRules = [];


    /**
     *  Example:
     *      return [
     *           'name' => $name,
     *           'email'=> $email  
     *      ];
     */
    public function labels(): array
    {
        return [];
    }


}