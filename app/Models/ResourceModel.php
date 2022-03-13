<?php

namespace App\Models;

use App\Models\Relations\HasManySyncable;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Venturecraft\Revisionable\RevisionableTrait;
/**
 * @mixin IdeHelperResourceModel
 */
class ResourceModel extends Model
{
    use Filterable, RevisionableTrait;

    protected static $cannotUpdate = [];
    protected static $data = [];

    public static function getData()
    {
        return static::$data;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            static::checkPolicy('create', $model);
        });

        static::updating(function ($model) {
            static::checkPolicy('update', $model);
        });

        static::deleting(function ($model) {
            static::checkPolicy('delete', $model);
        });
    }

    public static function index(Request $request, &$query)
    {
    }

    public static function manageResource(Request $request, $id = null, $save = true)
    {
        if ($id) {
            $validated = static::validateModel($request->all(), true);
            $model = static::findOrFail($id);
            $model->update($validated);
        } else {
            $validated = static::validateModel($request->all());
            $model = static::create($validated);
        }

        if ($request->relations) {
            $relations = (array)$request->relations;
            foreach ($relations as $relation => $options) {
                $model->manageRelation($relation, $options['method'], (array)$options['data']);
            }
        }

        if ($save)
            $model->save();

        return $model;
    }

    public function manageRelation(string $relation, string $method, array $data)
    {
        $relatedModel = $this->$relation()->getModel();
        if (method_exists($this->$relation(), 'getPivotClass')) {
            $relatedModel = $this->$relation()->getPivotClass();
        }

        if ($method != 'detach') {
            foreach ($data as $item) {
                if (!is_int($item)) {
                    $relatedModel::validateModel($item);
                }
            }
        }

        switch ($method) {
            case 'associate':
                $relData = $relatedModel::create($data[0])->id;
                break;
            case 'create':
                $relData = $data[0];
                break;
            default:
                $relData = $data;
        }

        $this->$relation()->$method($relData);
        return $this->load($relation);
    }

    public static function validateModel($data, bool $update = false)
    {
        $data = (array)$data;
        if ($update)
            return Validator::make($data, static::getUpdateValidation())->validate();
        else
            return Validator::make($data, static::getValidation())->validate();
    }

    public static function getValidation()
    {
        return static::getFromDataByKey('validation');
    }

    public static function getUpdateValidation()
    {
        return array_diff_key(static::getValidation(), array_flip(static::$cannotUpdate));
    }

    public static function getHeaders()
    {
        return static::getFromDataByKey('headers');
    }

    public static function getFormData()
    {
        $fields = static::getFromDataByKey('form');
        $validation = static::getFromDataByKey('validation');

        foreach ($fields as $key => &$field) {
            if (array_key_exists($key, $validation)) {
                $field['validation'] = $validation[$key];
            }
        }

        return $fields;
    }

    public static function getFromDataByKey($key, $keysOnly = false)
    {
        $items = [];
        foreach (static::$data as $item => $value) {
            if (!array_key_exists($key, (array)$value))
                continue;
            if ($value[$key])
                $items[$item] = $value[$key];
        }

        if ($keysOnly)
            return array_keys($items);

        return $items;
    }


    public static function checkPolicy($policyPermission, $model = null)
    {
        if (Auth::user()->can($policyPermission, $model))
            return true;

        abort(403, 'You don\'t have permission to access this resource or action!'
            . 'Required permission is ' . $policyPermission . ' ' . static::class);
    }
}
