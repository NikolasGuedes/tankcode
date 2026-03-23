import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import classrooms from './classrooms'
import teachers from './teachers'
import students from './students'
/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/director',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\DashboardController::__invoke
* @see app/Http/Controllers/Director/DashboardController.php:14
* @route '/director'
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

const director = {
    dashboard: Object.assign(dashboard, dashboard),
    classrooms: Object.assign(classrooms, classrooms),
    teachers: Object.assign(teachers, teachers),
    students: Object.assign(students, students),
}

export default director