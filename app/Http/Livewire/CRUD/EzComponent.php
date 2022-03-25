<?php

namespace App\Http\Livewire\CRUD;

use App\Models\Route;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class EzComponent extends \Livewire\Component
{
    /**
     * Name of the view file. The
     * @var string
     */
    protected string $view;
    public Model $model;
    public bool $crudModal = false;
    /**
     * Set array of roles as value of this array to only allow those roles to do the action
     * @var array|false[]
     */
    public array $rolesPermission = [
        'save'=>false,
        'update'=>false
    ];
    private string $deleteId;
    private bool $softDeleteModal = false;

    private array $withArray;
    public string $modelName;
    public string $pluralModelName;


    public function render()
    {

        $models =  $this->setTableModelsQuery();

        return view('livewire.CRUD.ez-crud', compact('models'));
    }

    public function __construct($id = null)
    {
        parent::__construct($id);
         $this->initComponent();
    }
    private function initComponent()
    {
        $this->setModel();
        $this->modelName = $this->modelName();
        $this->pluralModelName = \Str::plural($this->modelName);


    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    public function openCrudModal(){
        $this->crudModal = true;
    }

    public function closeCrudModal(){
        $this->crudModal = false;
    }
    public function updateModel($modelId){
        $this->openCrudModal();
        $this->model = $this->model::find($modelId);
    }
    public function addModel(){
        $this->openCrudModal();
        $this->model = new $this->model();
    }

    public function saveRouteData(){

        if(!empty($this->rolesPermission['save'])){
            if(!Auth::user()->hasRole($this->rolesPermission['save'])){
                $this->showToast('Save failed','You do not have required permissions to do this!','error');
                return;
            }
        }
        $this->validate();

        $this->model->save();
        $this->showToast('Save successful','','success');
        $this->closeCrudModal();
    }

    public function openSoftDeleteModal($id): void
    {
        $this->deleteId = $id;
        $this->softDeleteModal = true;
    }

    public function closeSoftDeleteModal(): void
    {
        $this->deleteId = '';
        $this->softDeleteModal = false;
    }

    public function softDelete(){
        if(!empty($this->rolesPermission['delete'])){
            if(!Auth::user()->hasRole($this->rolesPermission['save'])){
                $this->showToast('Delete failed','You do not have required permissions to do this!','error');
                return;
            }
        }
        $this->model::findOrFail($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->showToast('Deleting successful!','',);
    }

    /**
     * Needs to return a Model that will be used in CRUD operations
     * @return Model
     */
    abstract public function setModel():Model;


     public function setModelCollection():Builder
     {
        return  $this->model::with($this->withArray)->get()->paginate(10);
     }

    /**
     * return the array containing the attributes you want to show as key
     * and table column string as value
     * @return array
     */
    abstract public function setTableColumns():array;

    /**
     * @param array $withArray
     * @return EzComponent
     */
    public function setWithArray(array $withArray): EzComponent
    {
        $this->withArray = $withArray;
        return $this;
    }

    /**
     * Return rules array!
     * @return array
     */
    abstract protected function rules():array;


    abstract protected function modelName():string;

    /**
     * Return array with key value pair. Key being rules() array keys.
     * Value being field name
     * @return array
     */
    abstract protected function fieldRuleNames():array;

    /**
     * @param string $view
     * @return EzComponent
     */
    public function setView(string $view, $append = true): EzComponent
    {
        if($append){
            $this->view = 'livewire.CRUD.'.$view;
        }else{
            $this->view = $view;
        }
        return $this;
    }
}
