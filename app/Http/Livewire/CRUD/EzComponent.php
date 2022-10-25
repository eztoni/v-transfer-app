<?php

namespace App\Http\Livewire\CRUD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

abstract class EzComponent extends \Livewire\Component
{

    use WithPagination;
    use Actions;
    const FIELD_TYPE_TEXT = 1;
    const FIELD_TYPE_SELECT = 2;

    public bool $enableDelete = false;

    protected string $view;
    public Model $model;
    public string $modelClass;
    public bool $crudModal = false;

    public bool $isUpdate = false;


    /**
     * Set array of roles as value of this array to only allow those roles to do the action
     * @var array|false[]
     */
    public array $rolesPermission = [
        'save' => false,
        'update' => false
    ];
    private string $deleteId;
    public bool $softDeleteModal = false;


    public string $modelName;
    public string $pluralModelName;
    public array $fieldRuleNames =[];


    public function render()
    {

        $models = $this->tableModelsQuery();

        return view('livewire.CRUD.ez-crud', compact('models'));
    }

    public function mount()
    {
        $this->model = app($this->modelClass);
    }

    public function getModelPropertyBasedOnDottedString($model, $dottedString)
    {
        $value = $model;
        foreach (explode('.',$dottedString) as $property) {
            $value  = $this->getNestedProperty($property, $value);
        }
        return $value;
    }

    private function getNestedProperty($property, $object)
    {
        return $object->{$property};
    }

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->initComponent();
    }

    private function initComponent()
    {
        $this->modelClass = $this->setModelClass();
        $this->modelName = $this->modelName();
        $this->pluralModelName = \Str::plural($this->modelName);
    }



    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, null, [], $this->fieldRuleNames);
    }

    public function openCrudModal()
    {
        $this->crudModal = true;
    }

    public function closeCrudModal()
    {
        $this->crudModal = false;
    }

    public function updateModel($modelId)
    {
        $this->openCrudModal();
        $this->isUpdate = true;
        $this->model = $this->modelClass::find($modelId);
    }

    public function addModel()
    {
        $this->openCrudModal();
        $this->isUpdate = false;
        $this->model = app($this->modelClass);
    }



    public function saveModelData()
    {
        if (!empty($this->rolesPermission['save'])) {
            if (!Auth::user()->hasRole($this->rolesPermission['save'])) {
                $this->notification()->error('Save failed', 'You do not have required permissions to do this!');
                return;
            }
        }
        $this->validate(null, [], $this->fieldRuleNames);

        $beforeSaveBool = $this->beforeSave();

        if(!$beforeSaveBool) {
            return;
        }

        $this->model->save();

        $this->afterSave();


        $this->notification()->success(
            'Saved',
            "$this->modelName  was successfull saved"
        );
        $this->closeCrudModal();
    }

    // RETURN FALSE IF YOU WANT TO STOP THE SAVE
    protected function beforeSave(){
        return true;
    }
    protected function afterSave(){

    }


    public function openSoftDeleteModal($id): void
    {
        if(!$this->enableDelete) {
            return;
        }
        $this->deleteId = $id;
        $this->softDeleteModal = true;
    }

    public function closeSoftDeleteModal(): void
    {
        if(!$this->enableDelete) {
            return;
        }
        $this->deleteId = '';
        $this->softDeleteModal = false;
    }

    public function softDelete()
    {
        if(!$this->enableDelete) {
            return;
        }

        if (!empty($this->rolesPermission['delete'])) {
            if (!Auth::user()->hasRole($this->rolesPermission['save'])) {
                $this->notification()->error('Delete failed', 'You do not have required permissions to do this!');
                return;
            }
        }
        $this->modelClass::findOrFail($this->deleteId)->delete();
        $this->closeSoftDeleteModal();
        $this->notification()->success('Deleting successful!', '');
    }

    /**
     * Needs to return a Model class that will be used in CRUD operations
     * @return string
     */
    abstract public function setModelClass(): string;


    /**
     * return the array containing the attributes you want to show as key
     * and table column string as value
     * @return array
     */
    abstract public function tableColumns(): array;

    /**
     * @param array $withArray
     * @return EzComponent
     */
    protected function withArray(): array
    {
        return [];
    }

    protected function tableModelsQuery()
    {
        return $this->modelClass::with($this->withArray())->paginate(15);
    }


    abstract protected function modelName(): string;
    abstract protected function rules(): array;


    /**
     *  Return collection of fields
     * @return Collection
     */
    abstract public function formBladeViewName(): string;

    /**
     * @param string $view
     * @return EzComponent
     */
    public function setView(string $view, $append = true): EzComponent
    {
        if ($append) {
            $this->view = 'livewire.CRUD.' . $view;
        } else {
            $this->view = $view;
        }
        return $this;
    }
}
