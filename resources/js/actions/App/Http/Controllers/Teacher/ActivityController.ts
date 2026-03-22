import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/teacher/activities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Teacher\ActivityController::index
* @see app/Http/Controllers/Teacher/ActivityController.php:11
* @route '/teacher/activities'
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

const ActivityController = { index }

export default ActivityController