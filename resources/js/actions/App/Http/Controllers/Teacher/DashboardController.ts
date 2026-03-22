import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
const DashboardController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController.url(options),
    method: 'get',
})

DashboardController.definition = {
    methods: ["get","head"],
    url: '/teacher',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
DashboardController.url = (options?: RouteQueryOptions) => {
    return DashboardController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
DashboardController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
DashboardController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DashboardController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
const DashboardControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
DashboardControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Teacher\DashboardController::__invoke
* @see app/Http/Controllers/Teacher/DashboardController.php:12
* @route '/teacher'
*/
DashboardControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DashboardController.form = DashboardControllerForm

export default DashboardController