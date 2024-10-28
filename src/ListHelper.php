<?php

namespace CrudBuilderForLaravelCoreUI;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ListHelper
{
    protected array $columns = [];
    protected array $rowActionButtons = [];
    public string $searchPlaceholder = 'Buscar...';
    public string $searchButtonLabel = '';
    public string $searchButtonIcon = 'cil-search';
    protected ?array $defaultActionButton = null;
    protected \Closure $routeFunction;

    public function __construct(string $routeFunctionNameSpace = '')
    {
        $this->routeFunction = function ($routeName, $params) use ($routeFunctionNameSpace) {
            if (function_exists($routeFunctionNameSpace . '_url')) {
                return call_user_func($routeFunctionNameSpace . '_url', $routeName, $params);
            }

            return route($routeName, $params, false);
        };
    }

    public function text(string $field, string $label): self
    {
        $this->columns[$field] = ['label' => $label, 'type' => 'text'];
        return $this;
    }

    public function number(string $field, string $label): self
    {
        $this->columns[$field] = ['label' => $label, 'type' => 'number'];
        return $this;
    }

    public function date(string $field, string $label): self
    {
        $this->columns[$field] = ['label' => $label, 'type' => 'date'];
        return $this;
    }

    public function datetime(string $field, string $label): self
    {
        $this->columns[$field] = ['label' => $label, 'type' => 'datetime'];
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $this->columns[$field] = ['label' => $label, 'type' => 'email'];
        return $this;
    }

    public function boolean(string $field, string $label): self
    {
        $this->columns[$field] = ['label' => $label, 'type' => 'boolean'];
        return $this;
    }

    public function addRowActionButton(string $label, string $routeName, string $class = 'btn btn-danger', string $method = 'DELETE', string $icon = '', bool $confirm = false): self
    {
        $this->rowActionButtons[] = ['label' => $label, 'routeName' => $routeName, 'class' => $class, 'method' => $method, 'icon' => $icon, 'confirm' => $confirm];
        return $this;
    }

    public function setDefaultActionButton(string $label, string $url, string $class = 'btn btn-success', string $icon = ''): self
    {
        $this->defaultActionButton = ['label' => $label, 'url' => $url, 'class' => $class, 'icon' => $icon];
        return $this;
    }

    public function applySearchFilters(Builder $query, Request $request): Builder
    {
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                foreach ($this->columns as $column => $details) {
                    $q->orWhere(function ($subQuery) use ($column, $search) {
                        $parts = explode('.', $column);
                        if (count($parts) > 1) {
                            $field = array_pop($parts);
                            $relation = implode('.', $parts);
                            $subQuery->orWhereHas($relation, function ($relationQuery) use ($field, $search) {
                                $relationQuery->where($field, 'like', '%' . $search . '%');
                            });
                        } else {
                            $subQuery->orWhere($column, 'like', '%' . $search . '%');
                        }
                    });
                }
            });
        }
        return $query->orderBy('id', 'desc');
    }

    protected function getColumnValue($item, string $column)
    {
        $parts = explode('.', $column);
        foreach ($parts as $part) {
            if (is_object($item) && isset($item->$part)) {
                $item = $item->$part;
            } elseif (is_array($item) && isset($item[$part])) {
                $item = $item[$part];
            } else {
                return null;
            }
        }
        return $item;
    }

    public function render($items, Request $request): string
    {
        return view('crud-builder-for-laravel-core-ui::list', [
            'columns' => $this->columns,
            'rowActionButtons' => $this->rowActionButtons,
            'searchPlaceholder' => $this->searchPlaceholder,
            'searchButtonLabel' => $this->searchButtonLabel,
            'searchButtonIcon' => $this->searchButtonIcon,
            'defaultActionButton' => $this->defaultActionButton,
            'items' => $items,
            'request' => $request,
            'getColumnValue' => function ($item, $column) {
                return $this->getColumnValue($item, $column);
            },
            'routeFunction' => $this->routeFunction,
        ])->toHtml();
    }
}
