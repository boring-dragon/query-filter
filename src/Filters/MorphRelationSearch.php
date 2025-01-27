<?php

namespace Laravie\QueryFilter\Filters;

use Laravie\QueryFilter\Contracts\Filter\RequiresEloquent;
use Laravie\QueryFilter\SearchFilter;

class MorphRelationSearch extends SearchFilter implements RequiresEloquent
{
    /**
     * Relation name.
     *
     * @var string
     */
    protected $relation;

    /**
     * Related column used for search.
     *
     * @var \Illuminate\Database\Query\Expression|string
     */
    protected $column;

    /**
     * Available morph types.
     *
     * @var array
     */
    protected $types = [];

    /**
     * Construct new Morph Related Search.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  array<int, string>  $types
     */
    public function __construct(string $relation, $column, array $types = [])
    {
        $this->relation = $relation;
        $this->column = $column;
        $this->types = $types;
    }

    /**
     * Apply relation search queries.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array<int, string>  $keywords
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($query, array $keywords, string $likeOperator, string $whereOperator)
    {
        $types = ! empty($this->types) ? $this->types : '*';

        $query->{$whereOperator.'HasMorph'}($this->relation, $types, function ($query) use ($keywords, $likeOperator) {
            return (new FieldSearch($this->column))->validate($query)->apply(
                $query, $keywords, $likeOperator, 'where'
            );
        });

        return $query;
    }
}
