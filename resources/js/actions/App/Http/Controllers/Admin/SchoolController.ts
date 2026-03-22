import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/schools',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::index
* @see app/Http/Controllers/Admin/SchoolController.php:19
* @route '/admin/schools'
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
* @see \App\Http\Controllers\Admin\SchoolController::store
* @see app/Http/Controllers/Admin/SchoolController.php:55
* @route '/admin/schools'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/schools',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\SchoolController::store
* @see app/Http/Controllers/Admin/SchoolController.php:55
* @route '/admin/schools'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\SchoolController::store
* @see app/Http/Controllers/Admin/SchoolController.php:55
* @route '/admin/schools'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::store
* @see app/Http/Controllers/Admin/SchoolController.php:55
* @route '/admin/schools'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::store
* @see app/Http/Controllers/Admin/SchoolController.php:55
* @route '/admin/schools'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\SchoolController::update
* @see app/Http/Controllers/Admin/SchoolController.php:68
* @route '/admin/schools/{school}'
*/
export const update = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/schools/{school}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\SchoolController::update
* @see app/Http/Controllers/Admin/SchoolController.php:68
* @route '/admin/schools/{school}'
*/
update.url = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { school: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { school: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            school: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        school: typeof args.school === 'object'
        ? args.school.id
        : args.school,
    }

    return update.definition.url
            .replace('{school}', parsedArgs.school.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\SchoolController::update
* @see app/Http/Controllers/Admin/SchoolController.php:68
* @route '/admin/schools/{school}'
*/
update.put = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::update
* @see app/Http/Controllers/Admin/SchoolController.php:68
* @route '/admin/schools/{school}'
*/
const updateForm = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::update
* @see app/Http/Controllers/Admin/SchoolController.php:68
* @route '/admin/schools/{school}'
*/
updateForm.put = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Admin\SchoolController::destroy
* @see app/Http/Controllers/Admin/SchoolController.php:85
* @route '/admin/schools/{school}'
*/
export const destroy = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/schools/{school}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\SchoolController::destroy
* @see app/Http/Controllers/Admin/SchoolController.php:85
* @route '/admin/schools/{school}'
*/
destroy.url = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { school: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { school: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            school: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        school: typeof args.school === 'object'
        ? args.school.id
        : args.school,
    }

    return destroy.definition.url
            .replace('{school}', parsedArgs.school.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\SchoolController::destroy
* @see app/Http/Controllers/Admin/SchoolController.php:85
* @route '/admin/schools/{school}'
*/
destroy.delete = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::destroy
* @see app/Http/Controllers/Admin/SchoolController.php:85
* @route '/admin/schools/{school}'
*/
const destroyForm = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\SchoolController::destroy
* @see app/Http/Controllers/Admin/SchoolController.php:85
* @route '/admin/schools/{school}'
*/
destroyForm.delete = (args: { school: string | number | { id: string | number } } | [school: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const SchoolController = { index, store, update, destroy }

export default SchoolController