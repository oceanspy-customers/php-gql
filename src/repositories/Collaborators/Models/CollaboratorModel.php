<?php

declare(strict_types=1);

namespace Vertuoza\Repositories\Collaborators\Models;

use DateTime;
use stdClass;
use Vertuoza\Repositories\Interfaces\ModelInterface;

class CollaboratorModel implements ModelInterface
{
    public string $id;
    public ?string $name;
    public ?string $first_name;
    public ?DateTime $deleted_at;
    public ?string $tenant_id;
    public static function fromStdclass(stdClass $data): ModelInterface
    {
        $model = new CollaboratorModel();
        $model->id = $data->id;
        $model->name = $data->name;
        $model->first_name = $data->first_name;
        $model->deleted_at = $data->deleted_at;
        $model->tenant_id = $data->tenant_id;
        return $model;
    }

    public static function getPkColumnName(): string
    {
        return 'id';
    }

    public static function getTenantColumnName(): string
    {
        return 'tenant_id';
    }

    public static function getTableName(): string
    {
        return 'collaborator';
    }
}