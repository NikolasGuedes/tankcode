import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
const DashboardController42a740574ecbfbac32f8cc353fc32db9 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

DashboardController42a740574ecbfbac32f8cc353fc32db9.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
DashboardController42a740574ecbfbac32f8cc353fc32db9.url = (options?: RouteQueryOptions) => {
    return DashboardController42a740574ecbfbac32f8cc353fc32db9.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
DashboardController42a740574ecbfbac32f8cc353fc32db9.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
DashboardController42a740574ecbfbac32f8cc353fc32db9.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DashboardController42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
const DashboardController42a740574ecbfbac32f8cc353fc32db9Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
DashboardController42a740574ecbfbac32f8cc353fc32db9Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/dashboard'
*/
DashboardController42a740574ecbfbac32f8cc353fc32db9Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController42a740574ecbfbac32f8cc353fc32db9.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DashboardController42a740574ecbfbac32f8cc353fc32db9.form = DashboardController42a740574ecbfbac32f8cc353fc32db9Form
/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
const DashboardController750319cbfe81e0c4644d439731acf91e = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController750319cbfe81e0c4644d439731acf91e.url(options),
    method: 'get',
})

DashboardController750319cbfe81e0c4644d439731acf91e.definition = {
    methods: ["get","head"],
    url: '/student',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
DashboardController750319cbfe81e0c4644d439731acf91e.url = (options?: RouteQueryOptions) => {
    return DashboardController750319cbfe81e0c4644d439731acf91e.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
DashboardController750319cbfe81e0c4644d439731acf91e.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardController750319cbfe81e0c4644d439731acf91e.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
DashboardController750319cbfe81e0c4644d439731acf91e.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DashboardController750319cbfe81e0c4644d439731acf91e.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
const DashboardController750319cbfe81e0c4644d439731acf91eForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController750319cbfe81e0c4644d439731acf91e.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
DashboardController750319cbfe81e0c4644d439731acf91eForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController750319cbfe81e0c4644d439731acf91e.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/student'
*/
DashboardController750319cbfe81e0c4644d439731acf91eForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardController750319cbfe81e0c4644d439731acf91e.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DashboardController750319cbfe81e0c4644d439731acf91e.form = DashboardController750319cbfe81e0c4644d439731acf91eForm

const DashboardController = {
    '/dashboard': DashboardController42a740574ecbfbac32f8cc353fc32db9,
    '/student': DashboardController750319cbfe81e0c4644d439731acf91e,
}

export default DashboardController