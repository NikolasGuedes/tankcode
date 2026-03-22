import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/point-of-schools',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::index
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:17
* @route '/admin/point-of-schools'
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

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::store
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:68
* @route '/admin/point-of-schools'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/point-of-schools',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::store
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:68
* @route '/admin/point-of-schools'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::store
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:68
* @route '/admin/point-of-schools'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::store
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:68
* @route '/admin/point-of-schools'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::store
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:68
* @route '/admin/point-of-schools'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::update
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:75
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
export const update = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/point-of-schools/{pointOfSchool}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::update
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:75
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
update.url = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { pointOfSchool: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { pointOfSchool: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            pointOfSchool: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        pointOfSchool: typeof args.pointOfSchool === 'object'
        ? args.pointOfSchool.id
        : args.pointOfSchool,
    }

    return update.definition.url
            .replace('{pointOfSchool}', parsedArgs.pointOfSchool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::update
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:75
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
update.put = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::update
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:75
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
const updateForm = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::update
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:75
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
updateForm.put = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::destroy
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:82
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
export const destroy = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/point-of-schools/{pointOfSchool}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::destroy
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:82
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
destroy.url = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { pointOfSchool: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { pointOfSchool: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            pointOfSchool: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        pointOfSchool: typeof args.pointOfSchool === 'object'
        ? args.pointOfSchool.id
        : args.pointOfSchool,
    }

    return destroy.definition.url
            .replace('{pointOfSchool}', parsedArgs.pointOfSchool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::destroy
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:82
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
destroy.delete = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::destroy
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:82
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
const destroyForm = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PointOfSchoolController::destroy
* @see app/Http/Controllers/Admin/PointOfSchoolController.php:82
* @route '/admin/point-of-schools/{pointOfSchool}'
*/
destroyForm.delete = (args: { pointOfSchool: string | number | { id: string | number } } | [pointOfSchool: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const PointOfSchoolController = { index, store, update, destroy }

export default PointOfSchoolController