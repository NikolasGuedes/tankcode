import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import access from './access'
/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/director/teachers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::index
* @see app/Http/Controllers/Director/TeacherController.php:22
* @route '/director/teachers'
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
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/director/teachers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::create
* @see app/Http/Controllers/Director/TeacherController.php:88
* @route '/director/teachers/create'
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
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
export const edit = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/director/teachers/{teacher}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
edit.url = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { teacher: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { teacher: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            teacher: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        teacher: typeof args.teacher === 'object'
        ? args.teacher.id
        : args.teacher,
    }

    return edit.definition.url
            .replace('{teacher}', parsedArgs.teacher.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
edit.get = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
edit.head = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
const editForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
editForm.get = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::edit
* @see app/Http/Controllers/Director/TeacherController.php:96
* @route '/director/teachers/{teacher}/edit'
*/
editForm.head = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:119
* @route '/director/teachers'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/director/teachers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:119
* @route '/director/teachers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:119
* @route '/director/teachers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:119
* @route '/director/teachers'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:119
* @route '/director/teachers'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:140
* @route '/director/teachers/{teacher}'
*/
export const update = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/director/teachers/{teacher}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:140
* @route '/director/teachers/{teacher}'
*/
update.url = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { teacher: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { teacher: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            teacher: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        teacher: typeof args.teacher === 'object'
        ? args.teacher.id
        : args.teacher,
    }

    return update.definition.url
            .replace('{teacher}', parsedArgs.teacher.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:140
* @route '/director/teachers/{teacher}'
*/
update.put = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:140
* @route '/director/teachers/{teacher}'
*/
const updateForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:140
* @route '/director/teachers/{teacher}'
*/
updateForm.put = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:186
* @route '/director/teachers/{teacher}/resend-invitation'
*/
export const resendInvitation = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.definition = {
    methods: ["post"],
    url: '/director/teachers/{teacher}/resend-invitation',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:186
* @route '/director/teachers/{teacher}/resend-invitation'
*/
resendInvitation.url = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { teacher: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { teacher: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            teacher: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        teacher: typeof args.teacher === 'object'
        ? args.teacher.id
        : args.teacher,
    }

    return resendInvitation.definition.url
            .replace('{teacher}', parsedArgs.teacher.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:186
* @route '/director/teachers/{teacher}/resend-invitation'
*/
resendInvitation.post = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:186
* @route '/director/teachers/{teacher}/resend-invitation'
*/
const resendInvitationForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:186
* @route '/director/teachers/{teacher}/resend-invitation'
*/
resendInvitationForm.post = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.form = resendInvitationForm

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}'
*/
export const destroy = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/director/teachers/{teacher}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}'
*/
destroy.url = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { teacher: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { teacher: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            teacher: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        teacher: typeof args.teacher === 'object'
        ? args.teacher.id
        : args.teacher,
    }

    return destroy.definition.url
            .replace('{teacher}', parsedArgs.teacher.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}'
*/
destroy.delete = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}'
*/
const destroyForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}'
*/
destroyForm.delete = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const teachers = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    edit: Object.assign(edit, edit),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    access: Object.assign(access, access),
    resendInvitation: Object.assign(resendInvitation, resendInvitation),
    destroy: Object.assign(destroy, destroy),
}

export default teachers