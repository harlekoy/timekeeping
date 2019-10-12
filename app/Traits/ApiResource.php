<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

trait ApiResource
{
    /**
     * API resource.
     *
     * @var string
     */
    public $resource;

    /**
     * @var integer
     */
    public $limit = 15;

    /**
     * API controller constructor.
     */
    public function __construct()
    {
        $this->apiInstances($this->init());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Model $model)
    {
        $records = $this->fetchRecords();

        return $this->apiResponse($records);
    }

    /**
     * Fire the given event for the controller.
     *
     * @param  string  $event
     * @param  bool  $halt
     * @return mixed
     */
    protected function fireControllerEvent($event, $request, &$model)
    {
        if (method_exists($this, $event)) {
            $this->$event($request, $model);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Model $model)
    {
        try {
            DB::beginTransaction();

            app(get_class($request));

            $model->fill(request()->all());

            $this->fireControllerEvent('creating', $request = request(), $model);
            $this->fireControllerEvent('saving', $request, $model);

            $model->save();

            $this->fireControllerEvent('created', $request, $model);
            $this->fireControllerEvent('saved', $request, $model);

            $response = $this->apiResponse($model);

            DB::commit();

            return $response;
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = $this->fetchModel($id);

        return $this->apiResponse($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            app(get_class($request));

            $model = $this->fetchModel($id);
            $model->fill(request()->all());

            $this->fireControllerEvent('updating', $request = request(), $model);
            $this->fireControllerEvent('saving', $request, $model);

            $model->save();

            $this->fireControllerEvent('updated', $request, $model);
            $this->fireControllerEvent('saved', $request, $model);

            $response = $this->apiResponse($model);

            DB::commit();

            return $response;
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->fetchModel($id);

            $this->fireControllerEvent('deleting', $request, $model);

            $model->delete();

            $this->fireControllerEvent('deleted', $request, $model);

            $response = $this->apiResponse($model);

            DB::commit();

            return $response;
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    /**
     * API resource response.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     *
     * @return array
     */
    public function apiResponse($model)
    {
        $apiResource = $this->resource;

        if ($model instanceOf Collection ||
            $model instanceOf LengthAwarePaginator) {
            return $apiResource::collection($model);
        }

        return new $apiResource($model);
    }

    /**
     * Fetch model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function fetchModel($id)
    {
        $model = app(Model::class)
            ->with($this->with())
            ->withCount($this->withRelationCount());

        $model = $this->applyFilters($request = request(), $model);
        $model = $this->applySorters($request = request(), $model);

        $this->fireControllerEvent('fetching', $request, $model);

        return $model->findOrFail($id);
    }

    /**
     * Return collection with additional relationship data.
     *
     * @return array
     */
    protected function with()
    {
        if ($with = request()->get('with')) {
            $relations = array_map('trim', explode(',', $with));

            if (method_exists($this, 'withRelations')) {
                $relationsWithCallback = collect($this->withRelations())
                    ->map(function ($instance) {
                        return function ($query) use ($instance) {
                            $query = $instance->apply(request(), $query);
                        };
                    })
                    ->all();

                $relations = array_merge(
                    array_diff($relations, array_keys($relationsWithCallback)),
                    $relationsWithCallback
                );
            }

            return $relations;
        }

        return [];
    }

    /**
     * Return collection with count of each relationship.
     *
     * @return array
     */
    protected function withRelationCount()
    {
        if ($with = request()->get('count')) {
            $relations = array_map('trim', explode(',', $with));

            if (method_exists($this, 'withCount')) {
                $callback = collect($this->withCount())
                    ->map(function ($instance) {
                        return function ($query) use ($instance) {
                            $query = $instance->apply(request(), $query);
                        };
                    })
                    ->all();

                $relations = array_merge(
                    array_diff($relations, array_keys($callback)),
                    $callback
                );
            }

            return $relations;
        }

        return [];
    }

    /**
     * Apply set filters.
     */
    public function applyFilters($request, $query)
    {
        if (method_exists($this, 'filters')) {
            $filters = $request->get('filters', []);
            $availableFilters = collect($this->filters())
                ->filter(function ($filter, $key) use ($filters) {
                    return array_has($filters, $key);
                });

            foreach ($availableFilters as $key => $filter) {
                $query = $filter->apply($request, $query, $filters[$key]);
            }
        }

        return $query;
    }

    /**
     * Apply set sorter.
     */
    public function applySorters($request, $query)
    {
        if (method_exists($this, 'sorters')) {
            $sorters = array_wrap($request->get('sort') ?: []);
            $available = collect($this->sorters())
                ->filter(function ($sorter, $key) use ($sorters) {
                    return array_has($sorters, $key);
                });

            foreach ($available as $key => $sorter) {
                $query = $sorter->apply($request, $query, (bool) $sorters[$key]);
            }
        }

        return $query;
    }

    /**
     * Fetch records based on query params.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchRecords()
    {
        $model = app(Model::class)
            ->with($this->with())
            ->withCount($this->withRelationCount());

        $model = $this->applyFilters($request = request(), $model);
        $model = $this->applySorters($request, $model);
        $this->fireControllerEvent('fetching', $request, $model);

        if ($page = $request->get('page')) {
            return $model->paginate($this->limit());
        }

        return $model->get();
    }

    /**
     * Get request limit or return the default if not exist.
     *
     * @return int
     */
    public function limit()
    {
        return request()->get('limit', $this->limit);
    }

    /**
     * Boot controller app instances.
     *
     * @return void
     */
    public function apiInstances($instances)
    {
        app()->instance(Request::class, new $instances['request']);
        app()->instance(Model::class, new $instances['model']);

        $this->resource = $instances['resource'];
    }
}
