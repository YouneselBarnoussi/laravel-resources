<?php

namespace OwowAgency\LaravelResources\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use OwowAgency\LaravelResources\Requests\ResourceRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResourceController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The resource model class.
     *
     * @var string
     */
    public $resourceModelClass;

    /**
     * The resource manager.
     *
     * @var \OwowAgency\LaravelResources\Managers\ResourceManager
     */
    public $resourceManager;

    /**
     * ResourceController constructor.
     *
     * @return void
     * 
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setResourceModelClass();

        $this->setResourceManager();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', $this->resourceModelClass);

        $resourcesPaginated = $this->resourceManager->paginate();

        $resources = resource($resourcesPaginated, true);

        $resourcesPaginated->setCollection($resources->collection);

        return ok($resourcesPaginated);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request = $this->validateRequest();

        $this->authorize('create', [$this->resourceModelClass, $request->validated()]);

        $resource = $this->resourceManager->create($request->validated());

        $resource = resource($resource);

        return created($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = $this->getId($id);

        $this->authorize('view', [$this->resourceModelClass, $id]);

        $resource = $this->resourceManager->find($id);

        $resource = resource($resource);

        return ok($resource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request = $this->validateRequest();

        $id = $this->getId($id);

        $this->authorize('update', [$this->resourceModelClass, $id, $request->validated()]);

        $resource = $this->resourceManager->update($id, $request->validated());

        $resource = resource($resource);

        return ok($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = $this->getId($id);

        $this->authorize('delete', [$this->resourceModelClass, $id]);

        $this->resourceManager->delete($id);

        return no_content();
    }

    /**
     * Sets the resource model class.
     * When no request is present, like in terminal, skip.
     * Throw exception route has no specified model.
     * 
     * @throws \Exception
     *
     * @return void
     */
    public function setResourceModelClass()
    {
        $request = request();

        if (! $request || ! $request->route()) {
            return;
        }

        $resourceModelClass = $request->route()->getAction('model');

        if (is_null($resourceModelClass)) {
            throw new \Exception('Route has no specified model.');
        }

        $this->resourceModelClass = $resourceModelClass;
    }

    /**
     * Sets resource manager.
     * Do not set when no resource model class is set.
     * 
     * @return void
     */
    public function setResourceManager()
    {
        if (! $this->resourceModelClass) {
            return;
        }

        $this->resourceManager = manager($this->resourceModelClass);
    }

    /**
     * Validates request by classes specified in the route.
     * 
     * @return \Illuminate\Foundation\Http\FormRequest
     */
    public function validateRequest() : FormRequest
    {
        $requests = request()->route()->getAction('requests');

        if (is_null($requests)) {
            return app(ResourceRequest::class);
        }

        $actionMethod = request()->route()->getActionMethod();

        if (! array_key_exists($actionMethod, $requests)) {
            return app(ResourceRequest::class);
        }

        $requestClass = $requests[$actionMethod];

        return app($requestClass);
    }

    /**
     * Tries to get an identifier for the given value.
     * 
     * @param  mixed  $id
     * @return int
     */
    public function getId($id)
    {
        if ($id instanceof Model) {
            $keyName = $id->getKeyName();

            return $id->$keyName;
        }

        if (is_object($id)) {
            return $id->id;
        }

        return $id;
    }
}
