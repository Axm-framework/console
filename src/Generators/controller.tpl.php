/**
* Controlador <?= $class ?>
*
* @category App
* @package Controllers
*/

class <?= $class ?>Controller extends BaseController
{
public function __construct(){
parent::__construct();
$this->model = new User();
}


public function index(){

if ($this->request->isPost()) {

$this->model->setRules([
'code' => 'required|alpanum|size:6',
'email' => 'required|email'
]);

/** Si la request es por AJAX,
* validar el campo */
if (Axm::app()->request->isAjax()) {
if(!$this->model->validate() ){
return $this->model->validateOne();
}
};

if( $this->model->validate() ):
if( $this->model->iniLogin() ) {

/** Code */

}else {

/** Code */
};

else:
setFlash( 'error', $this->model->getFirstError() );
endif;
}

view( 'pages/login/login', ['model' => $this->model] );
}


}