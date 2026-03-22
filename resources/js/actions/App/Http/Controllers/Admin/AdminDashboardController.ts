import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
const AdminDashboardController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AdminDashboardController.url(options),
    method: 'get',
})

AdminDashboardController.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
AdminDashboardController.url = (options?: RouteQueryOptions) => {
    return AdminDashboardController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
AdminDashboardController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AdminDashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
AdminDashboardController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: AdminDashboardController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
const AdminDashboardControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AdminDashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
AdminDashboardControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AdminDashboardController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
AdminDashboardControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AdminDashboardController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

AdminDashboardController.form = AdminDashboardControllerForm

export default AdminDashboardController