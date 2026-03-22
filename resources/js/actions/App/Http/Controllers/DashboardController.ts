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
* @route '/owner'
*/
const DashboardControllercad30db1f60378b5d578214a41b1c51b = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardControllercad30db1f60378b5d578214a41b1c51b.url(options),
    method: 'get',
})

DashboardControllercad30db1f60378b5d578214a41b1c51b.definition = {
    methods: ["get","head"],
    url: '/owner',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/owner'
*/
DashboardControllercad30db1f60378b5d578214a41b1c51b.url = (options?: RouteQueryOptions) => {
    return DashboardControllercad30db1f60378b5d578214a41b1c51b.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/owner'
*/
DashboardControllercad30db1f60378b5d578214a41b1c51b.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardControllercad30db1f60378b5d578214a41b1c51b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/owner'
*/
DashboardControllercad30db1f60378b5d578214a41b1c51b.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DashboardControllercad30db1f60378b5d578214a41b1c51b.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/owner'
*/
const DashboardControllercad30db1f60378b5d578214a41b1c51bForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardControllercad30db1f60378b5d578214a41b1c51b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/owner'
*/
DashboardControllercad30db1f60378b5d578214a41b1c51bForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardControllercad30db1f60378b5d578214a41b1c51b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/owner'
*/
DashboardControllercad30db1f60378b5d578214a41b1c51bForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardControllercad30db1f60378b5d578214a41b1c51b.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DashboardControllercad30db1f60378b5d578214a41b1c51b.form = DashboardControllercad30db1f60378b5d578214a41b1c51bForm
/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
const DashboardControllercf6c8862b72e9807168cdec948a3d610 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardControllercf6c8862b72e9807168cdec948a3d610.url(options),
    method: 'get',
})

DashboardControllercf6c8862b72e9807168cdec948a3d610.definition = {
    methods: ["get","head"],
    url: '/teacher',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
DashboardControllercf6c8862b72e9807168cdec948a3d610.url = (options?: RouteQueryOptions) => {
    return DashboardControllercf6c8862b72e9807168cdec948a3d610.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
DashboardControllercf6c8862b72e9807168cdec948a3d610.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DashboardControllercf6c8862b72e9807168cdec948a3d610.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
DashboardControllercf6c8862b72e9807168cdec948a3d610.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DashboardControllercf6c8862b72e9807168cdec948a3d610.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
const DashboardControllercf6c8862b72e9807168cdec948a3d610Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardControllercf6c8862b72e9807168cdec948a3d610.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
DashboardControllercf6c8862b72e9807168cdec948a3d610Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardControllercf6c8862b72e9807168cdec948a3d610.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::__invoke
* @see app/Http/Controllers/DashboardController.php:9
* @route '/teacher'
*/
DashboardControllercf6c8862b72e9807168cdec948a3d610Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DashboardControllercf6c8862b72e9807168cdec948a3d610.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

DashboardControllercf6c8862b72e9807168cdec948a3d610.form = DashboardControllercf6c8862b72e9807168cdec948a3d610Form
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
    '/owner': DashboardControllercad30db1f60378b5d578214a41b1c51b,
    '/teacher': DashboardControllercf6c8862b72e9807168cdec948a3d610,
    '/student': DashboardController750319cbfe81e0c4644d439731acf91e,
}

export default DashboardController