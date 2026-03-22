import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/director/classrooms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::index
* @see app/Http/Controllers/Director/ClassroomController.php:20
* @route '/director/classrooms'
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
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/director/classrooms/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::create
* @see app/Http/Controllers/Director/ClassroomController.php:63
* @route '/director/classrooms/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
export const edit = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/director/classrooms/{classroom}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
edit.url = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { classroom: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { classroom: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            classroom: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        classroom: typeof args.classroom === 'object'
        ? args.classroom.id
        : args.classroom,
    }

    return edit.definition.url
            .replace('{classroom}', parsedArgs.classroom.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
edit.get = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
edit.head = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
const editForm = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
editForm.get = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::edit
* @see app/Http/Controllers/Director/ClassroomController.php:71
* @route '/director/classrooms/{classroom}/edit'
*/
editForm.head = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Director\ClassroomController::store
* @see app/Http/Controllers/Director/ClassroomController.php:89
* @route '/director/classrooms'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/director/classrooms',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\ClassroomController::store
* @see app/Http/Controllers/Director/ClassroomController.php:89
* @route '/director/classrooms'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\ClassroomController::store
* @see app/Http/Controllers/Director/ClassroomController.php:89
* @route '/director/classrooms'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::store
* @see app/Http/Controllers/Director/ClassroomController.php:89
* @route '/director/classrooms'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::store
* @see app/Http/Controllers/Director/ClassroomController.php:89
* @route '/director/classrooms'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Director\ClassroomController::update
* @see app/Http/Controllers/Director/ClassroomController.php:103
* @route '/director/classrooms/{classroom}'
*/
export const update = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/director/classrooms/{classroom}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Director\ClassroomController::update
* @see app/Http/Controllers/Director/ClassroomController.php:103
* @route '/director/classrooms/{classroom}'
*/
update.url = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { classroom: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { classroom: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            classroom: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        classroom: typeof args.classroom === 'object'
        ? args.classroom.id
        : args.classroom,
    }

    return update.definition.url
            .replace('{classroom}', parsedArgs.classroom.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\ClassroomController::update
* @see app/Http/Controllers/Director/ClassroomController.php:103
* @route '/director/classrooms/{classroom}'
*/
update.put = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::update
* @see app/Http/Controllers/Director/ClassroomController.php:103
* @route '/director/classrooms/{classroom}'
*/
const updateForm = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::update
* @see app/Http/Controllers/Director/ClassroomController.php:103
* @route '/director/classrooms/{classroom}'
*/
updateForm.put = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Director\ClassroomController::destroy
* @see app/Http/Controllers/Director/ClassroomController.php:117
* @route '/director/classrooms/{classroom}'
*/
export const destroy = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/director/classrooms/{classroom}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Director\ClassroomController::destroy
* @see app/Http/Controllers/Director/ClassroomController.php:117
* @route '/director/classrooms/{classroom}'
*/
destroy.url = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { classroom: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { classroom: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            classroom: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        classroom: typeof args.classroom === 'object'
        ? args.classroom.id
        : args.classroom,
    }

    return destroy.definition.url
            .replace('{classroom}', parsedArgs.classroom.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\ClassroomController::destroy
* @see app/Http/Controllers/Director/ClassroomController.php:117
* @route '/director/classrooms/{classroom}'
*/
destroy.delete = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::destroy
* @see app/Http/Controllers/Director/ClassroomController.php:117
* @route '/director/classrooms/{classroom}'
*/
const destroyForm = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\ClassroomController::destroy
* @see app/Http/Controllers/Director/ClassroomController.php:117
* @route '/director/classrooms/{classroom}'
*/
destroyForm.delete = (args: { classroom: string | number | { id: string | number } } | [classroom: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ClassroomController = { index, create, edit, store, update, destroy }

export default ClassroomController