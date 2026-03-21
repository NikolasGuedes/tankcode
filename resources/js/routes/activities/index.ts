import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/atividades',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ActivitiesController::index
* @see app/Http/Controllers/ActivitiesController.php:9
* @route '/atividades'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const activities = {
    index: Object.assign(index, index),
}

export default activities