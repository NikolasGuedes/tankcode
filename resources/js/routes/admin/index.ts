import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import schools from './schools'
import pointOfSchools from './point-of-schools'
import users from './users'
/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminDashboardController::__invoke
* @see app/Http/Controllers/Admin/AdminDashboardController.php:14
* @route '/admin'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    schools: Object.assign(schools, schools),
    pointOfSchools: Object.assign(pointOfSchools, pointOfSchools),
    users: Object.assign(users, users),
}

export default admin