<?php

namespace App\Http\Controllers;

use App\Models\ResourceModel;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\ResourcePivot;

class ResourceController extends Controller
{
    use Filterable;
    protected static $modelName;
    protected static $modelNamespace = 'App\\Models\\';

    /**
     * @var ResourceModel
     */
    protected $model;

    protected static $middlewareCustom;
    protected static $middlewareExcept;

    public function __construct()
    {
        static::$middlewareCustom = static::$middlewareCustom ?? config('app.nextlvl.resource_middleware');
        static::$middlewareExcept = static::$middlewareExcept ?? config('app.nextlvl.resource_middleware_except');
        $this->middleware(static::$middlewareCustom)->except(static::$middlewareExcept);
        $this->model = self::getModelByName(static::$modelName);
    }

    public static function getModelByName($name)
    {
        return static::$modelNamespace . $name;
    }

    public function index(Request $request)
    {
        $query = $this->model::query();

//        $this->model::checkPolicy('viewAny', $this->model);
        $this->model::index($request, $query);
        return $this->indexReturn($request, $query);
    }

    public function indexRelation(Request $request, $id, $relation)
    {
        $item = $this->model::findOrFail($id);
//        $this->model::checkPolicy('view', $item);

        $query = $item->$relation();

        $item->$relation()->getModel()::index($request, $query);

        return $this->indexReturn($request, $query);
    }

    public function indexReturn(Request $request, $query)
    {
        if ($request->has('filter') || $request->has('filterRelation')) {
            $query->filter($request->filter ?? [], $request->filterRelation ?? []);
        }

        if ($request->has('column') || $request->has('value')|| $request->has('relation')) {
            $this->scopeMagija($query, $request->column ?? null, $request->value ?? null, $request->relation ?? null);
        }

        $this->sortResource($request, $query);
        $this->withRelations($request, $query);

        if ($request->limit)
            $query->limit($request->limit);

        $data = $request->perPage ? $query->paginate($request->perPage) : $query->get();

        if ($request->append)
            foreach ($data as &$element) {
                $element->append($request->append);
            }

        return $data;
    }

    public function store(Request $request)
    {
        return $this->model::manageResource($request);
    }

    public function update(Request $request, $id)
    {
        return $this->model::manageResource($request, $id);
    }

    public function show(Request $request, $id)
    {
        $query = $this->model::query();
        $this->withRelations($request, $query);

        $item = $query->findOrFail($id);
        //$this->model::checkPolicy('view', $item);

        if ($request->append)
            $item->append($request->append);

        return $item;
    }

    public function destroy(Request $request, $id)
    {
        // Forbid deleting for now
        //abort(403);

        $item = $this->model::findOrFail($id);
        $item->delete();

        return response('Success', 204);
    }

    public function manageRelation(Request $request, $id, string $relation, array $availableMethods = [])
    {
        $availableMethods = !empty($availableMethods) ? $availableMethods : ['attach', 'detach', 'sync', 'toggle', 'syncWithoutDetaching', 'create', 'createMany', 'associate', 'dissociate'];
        $request->validate([
            'method' => ['required', 'string', Rule::in($availableMethods)],
            'data' => 'present'
        ]);

        $method = $request->get('method');

        $item = $this->model::findOrFail($id);

        $data = (array)$request->get('data');

        return $item->manageRelation($relation, $method, $data);
    }

    public function withRelations(Request $request, &$query)
    {
        $requestWith = (array)$request->with;
//        $query->with($requestWith);

        foreach ($requestWith as $relation) {
            $relatedModel = $query->getModel();
            //fix ako se radi o 'dot' notaciji za duboke relacije. Npr. with('user.parentOne')
            $explodedRelation = explode('.', $relation);
            $relationshipModel = $query->getModel();
            foreach ($explodedRelation as $relKey => $relValue) {
                // Stripaj select constraintove
                $relValue = Str::before($relValue, ':');

//            // Ako se radi o belongsToMany treba ovo da dohvati ispravan model
                if (method_exists($relatedModel, $relValue) && method_exists($relatedModel->$relValue(), 'getPivotClass')) {
                    $pivotClass = $relatedModel->$relValue()->getModel(); //getPivotClass() ne radi za belongsToMany
                    $relationshipModel = new $pivotClass;
                } else
                    $relationshipModel = $relationshipModel->$relValue()->getModel();
            }

            // Ne more više selectat nakon ovog updatea, smislit kako to izvest
            $relationWithoutSelectConstraints = Str::before($relation, ':');
            $query->with([$relationWithoutSelectConstraints => function ($query2) use ($request, $relationshipModel) {
                $relationshipModel::index($request, $query2);
            }]);
        }

        $requestWithCount = (array)$request->withCount;
        $query->withCount($requestWithCount);
    }

    protected function sortResource(Request $request, &$query)
    {
        if (!$request->sortBy)
            return;

        $sorts = (array)$request->sortBy;
        $sortDesc = (array)$request->sortDesc;

        foreach ($sorts as $i => $sort) {
            $desc = filter_var($sortDesc[$i], FILTER_VALIDATE_BOOLEAN);
                $query->orderBy($sort, $desc ? 'desc' : 'asc');
        }
    }

    public function getFormData()
    {
        return $this->model::getFormData();
    }

    public function hasTrait($trait)
    {
        return in_array($trait, class_uses($this->model));
    }
}
