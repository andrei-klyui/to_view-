<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\backpack\operations\ReorderOperation;
use App\Http\Requests\Admin\CommissionRightsRequest;
use App\Models\CommissionRight;
use App\Models\Companies\Company;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Exception;

/**
 * Class CommissionRightCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CommissionRightCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use ReorderOperation;

    /**
     * @return void
     * @throws Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(CommissionRight::class);
        $this->crud->setRoute(backpack_url('commission-rights'));
        $this->crud->setEntityNameStrings('Commission Right', 'Commission Rights');

        $this->crud->addFilter(
            [
                'name'  => 'company_id',
                'type'  => 'select2',
                'label' => 'Company',
            ],
            function () {
                return Company::all()->pluck('name', 'id')->toArray();
            },
            function ($value) {
                $this->crud->addClause('where', 'company_id', $value);
            }
        );
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        if (session()->has('company_id')) {
            session()->forget('company_id');
        }
        session()->put(
            'company_id',
            Company::whereId($this->request->get('company_id'))
                ->pluck('name', 'id')
                ->toArray()
        );
        $this->crud->denyAccess('show');
        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => 'Name',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('name', 'ilike', '%'.$searchTerm.'%');
                },
            ],
            [
                'name'  => 'file_name',
                'type'     => 'closure',
                'function' => function (CommissionRight $entry) {
                    return $entry->file_name;
                },
            ],
            [
                'name'     => 'company',
                'type'     => 'closure',
                'function' => function (CommissionRight $entry) {
                    return $entry->company->name;
                },
                'orderable'  => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->join('companies', 'companies.id', '=', 'commission_rights.company_id')
                        ->orderBy('companies.name', $columnDirection)->select('commission_rights.*');
                },
            ],
        ]);
        $this->crud->orderBy('lft');
        $this->crud->setDefaultPageLength(25);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation(CommissionRightsRequest::class);
        $this->crud->setRequiredFields(CommissionRightsRequest::class);
        $companyType = empty(session()->get('company_id')) ? 'select' : 'select_new';
        $isDisabled = $companyType === 'select_new';
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'label' => 'Company',
                'type' => $companyType,
                'name' => 'company_id',
                'entity' => 'company',
                'model' => Company::class,
                'attribute' => 'name',
                'attributes' => $isDisabled ? ['disabled' => 'disabled',] : [],
            ],
            [
                'label' => 'Right',
                'name' => 'file_name',
                'type' => 'upload',
                'upload' => true,
                'disk' => env('AWS_BUCKET'),
            ],
        ]);
    }

    /**
     * Setup update operation
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->crud->setValidation(CommissionRightsRequest::class);
        $this->crud->setRequiredFields(CommissionRightsRequest::class);
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'label' => 'Company',
                'type' => 'select',
                'name' => 'company_id',
                'entity' => 'company',
                'model' => Company::class,
                'attribute' => 'name',
            ],
            [
                'label' => 'Right',
                'name' => 'file_name',
                'type' => 'upload',
                'upload' => true,
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupReorderOperation(): void
    {
        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 0);
    }

    /**
     * @return void
     */
    protected function setupReorderDefaults(): void
    {
        $this->crud->set('reorder.enabled', true);
        $this->crud->allowAccess('reorder');

        $this->crud->operation('reorder', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButtonFromView('top', 'reorder', 'reorder.commission_rights');
        });
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');
        if (!empty(session()->get('company_id'))) {
            $this->crud->request->request->add(['company_id' => key(session()->get('company_id'))]);
        }
        $request = $this->crud->validateRequest();
        $item = $this->crud->create($this->crud->getStrippedSaveRequest());
        $this->data['entry'] = $this->crud->entry = $item;
        \Alert::success(trans('backpack::crud.insert_success'))->flash();
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
