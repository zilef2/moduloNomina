<?php

namespace Mrdebug\Crudgen\Services;

use Illuminate\Support\Facades\File;

class PathsAndNamespacesService
{
    public function getStubPath(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'stubs';
    }

    public function getCrudgenViewsStub()
    {
        return resource_path('crudgen'.DIRECTORY_SEPARATOR.'views');
    }

    public function getCrudgenViewsStubCustom($templateViewsDirectory)
    {
        return $this->getCrudgenViewsStub().DIRECTORY_SEPARATOR.$templateViewsDirectory;
    }

    public function getRealpathBase($directory)
    {
        return realpath(base_path($directory));
    }

    /** request */

    public function getDefaultNamespaceRequest($rootNamespace): string
    {
        return $rootNamespace.'Http\Requests';
    }

    public function getRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Request.stub';
    }

    public function getRealpathBaseRequest(): string
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http').DIRECTORY_SEPARATOR.'Requests';
    }

    public function getRealpathBaseCustomRequest($namingConvention): string
    {
        return $this->getRealpathBaseRequest().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'Request.php';
    }

    /** commentable */

    public function getCommentableRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'commentable'.DIRECTORY_SEPARATOR.'Request.stub';
    }

    public function getCommentableControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'commentable'.DIRECTORY_SEPARATOR.'ControllerCommentable.stub';
    }

    public function getRealpathBaseCustomCommentableRequest($namingConvention): string
    {
        return $this->getRealpathBaseRequest().DIRECTORY_SEPARATOR.$namingConvention['model_name'].'Request.php';
    }

    public function getCommentableCommentBlockPath()
    {
        $stubPath = resource_path('crudgen/commentable/comment-block.stub');

        return File::exists($stubPath)
                ? $stubPath
                : $this->getStubPath().DIRECTORY_SEPARATOR.'commentable'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'comment-block.stub';
    }

    /** api */

    public function getDefaultNamespaceApiController($rootNamespace): string
    {
        return $rootNamespace.'Http\Controllers\API';
    }

    //api request
    public function getApiRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'request.stub';
    }

    //api controller
    public function getRealpathBaseApiController(): string
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers').DIRECTORY_SEPARATOR.'API';
    }

    public function getRealpathBaseCustomApiController($namingConvention): string
    {
        return $this->getRealpathBaseApiController().DIRECTORY_SEPARATOR.$namingConvention['plural_name'].'Controller.php';
    }

    public function getApiControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'Controller-api.stub';
    }

    //api resource
    public function getResourceStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.'resource.stub';
    }

    public function getDefaultNamespaceResource($rootNamespace): string
    {
        return $rootNamespace.'Http\Resources';
    }

    public function getRealpathBaseResource(): string
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http').DIRECTORY_SEPARATOR.'Resources';
    }

    public function getRealpathBaseCustomResource($namingConvention): string
    {
        return $this->getRealpathBaseResource().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'Resource.php';
    }

    /** controller */

    public function getDefaultNamespaceController($rootNamespace): string
    {
        return $rootNamespace.'Http\Controllers';
    }

    public function getRealpathBaseController()
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers');
    }

    public function getRealpathBaseCustomController($namingConvention): string
    {
        return $this->getRealpathBaseController().DIRECTORY_SEPARATOR.$namingConvention['plural_name'].'Controller.php';
    }

    public function getRealpathBaseCustomCommentableController($namingConvention): string
    {
        return $this->getRealpathBaseController().DIRECTORY_SEPARATOR.$namingConvention['controller_name'].'Controller.php';
    }

    public function getControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Controller.stub';
    }

    /** models */

    public function getDefaultNamespaceModel($rootNamespace): string
    {
        return $rootNamespace.'Models\\';
    }

    public function getDefaultNamespaceCustomModel($rootNamespace, $singularName): string
    {
        return $this->getDefaultNamespaceModel($rootNamespace).$singularName;
    }

    public function getRealpathBaseModel()
    {
        return $this->getRealpathBase('app').DIRECTORY_SEPARATOR.'Models';
    }

    public function getModelStubPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Model.stub';
    }

    public function getRealpathBaseCustomModel($namingConvention)
    {
        return isset($namingConvention['singular_name'])
            ? $this->getRealpathBaseModel().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'.php'
            : $this->getRealpathBaseModel().DIRECTORY_SEPARATOR.$namingConvention['model_name'].'.php';
    }

    /** migrations */

    public function getMigrationStubPath()
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'migration.stub';
    }

    public function getRealpathBaseMigration()
    {
        return database_path(DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR);
    }

    /** paths views */

    public function getRealpathBaseViews()
    {
        return $this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views');
    }

    public function getRealpathBaseCustomViews($namingConvention)
    {
        return $this->getRealpathBaseViews().DIRECTORY_SEPARATOR.$namingConvention['plural_low_name'];
    }

    // views livewire
    public function getRealpathBaseLivewireViews()
    {
        return $this->getRealpathBase('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'livewire');
    }

    public function getRealpathBaseCustomLivewireViews($namingConvention)
    {
        return $this->getRealpathBaseLivewireViews().DIRECTORY_SEPARATOR.$namingConvention['plural_low_name'];
    }

    public function getControllerLivewireStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'ControllerLivewire.stub';
    }

    /** service */

    public function getDefaultNamespaceService($rootNamespace): string
    {
        return config('crudgen.paths.service.namespace') ?? $rootNamespace.'Services';
    }

    public function getServiceStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'service'.DIRECTORY_SEPARATOR.'Service.stub';
    }

    public function getRealpathBaseService(): string
    {
        return config('crudgen.paths.service.path') ?? $this->getRealpathBase('app').DIRECTORY_SEPARATOR.'Services';
    }

    public function getRealpathBaseCustomService($namingConvention): string
    {
        return $this->getRealpathBaseService().DIRECTORY_SEPARATOR.$namingConvention['service_name'].'.php';
    }

    /** datatable */

    public function getDefaultNamespaceDatatable($rootNamespace): string
    {
        return $rootNamespace.'Livewire';
    }

    public function getRealpathBaseDatatable()
    {
        return $this->getRealpathBase('app'.DIRECTORY_SEPARATOR.'Livewire');

    }

    public function getRealpathBaseCustomDatatable($namingConvention): string
    {
        return $this->getRealpathBaseDatatable().DIRECTORY_SEPARATOR.$namingConvention['singular_name'].'Datatable.php';
    }

    public function getDatatableStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Livewire'.DIRECTORY_SEPARATOR.'Datatable.stub';
    }
}
