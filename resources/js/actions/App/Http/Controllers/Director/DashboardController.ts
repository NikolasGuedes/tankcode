import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
const DashboardController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController.url(options),
    method: 'get',
})

DashboardController.definition = {
    methods: ["get","head"],
    url: '/director',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
DashboardController.url = (options?: RouteQueryOptions) => {
    return DashboardController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
DashboardController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
DashboardController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DashboardController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
const DashboardControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
DashboardControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
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