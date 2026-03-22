import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:71
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
* @see app/Http/Controllers/Director/TeacherController.php:71
* @route '/director/teachers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:71
* @route '/director/teachers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:71
* @route '/director/teachers'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::store
* @see app/Http/Controllers/Director/TeacherController.php:71
* @route '/director/teachers'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:92
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
* @see app/Http/Controllers/Director/TeacherController.php:92
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
* @see app/Http/Controllers/Director/TeacherController.php:92
* @route '/director/teachers/{teacher}'
*/
update.put = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:92
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
* @see app/Http/Controllers/Director/TeacherController.php:92
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
* @see \App\Http\Controllers\Director\TeacherController::updateAccess
* @see app/Http/Controllers/Director/TeacherController.php:120
* @route '/director/teachers/{teacher}/access'
*/
export const updateAccess = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateAccess.url(args, options),
    method: 'patch',
})

updateAccess.definition = {
    methods: ["patch"],
    url: '/director/teachers/{teacher}/access',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::updateAccess
* @see app/Http/Controllers/Director/TeacherController.php:120
* @route '/director/teachers/{teacher}/access'
*/
updateAccess.url = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return updateAccess.definition.url
            .replace('{teacher}', parsedArgs.teacher.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::updateAccess
* @see app/Http/Controllers/Director/TeacherController.php:120
* @route '/director/teachers/{teacher}/access'
*/
updateAccess.patch = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateAccess.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::updateAccess
* @see app/Http/Controllers/Director/TeacherController.php:120
* @route '/director/teachers/{teacher}/access'
*/
const updateAccessForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateAccess.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::updateAccess
* @see app/Http/Controllers/Director/TeacherController.php:120
* @route '/director/teachers/{teacher}/access'
*/
updateAccessForm.patch = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateAccess.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateAccess.form = updateAccessForm

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:138
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
* @see app/Http/Controllers/Director/TeacherController.php:138
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
* @see app/Http/Controllers/Director/TeacherController.php:138
* @route '/director/teachers/{teacher}/resend-invitation'
*/
resendInvitation.post = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:138
* @route '/director/teachers/{teacher}/resend-invitation'
*/
const resendInvitationForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::resendInvitation
* @see app/Http/Controllers/Director/TeacherController.php:138
* @route '/director/teachers/{teacher}/resend-invitation'
*/
resendInvitationForm.post = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.form = resendInvitationForm

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:109
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
* @see app/Http/Controllers/Director/TeacherController.php:109
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
* @see app/Http/Controllers/Director/TeacherController.php:109
* @route '/director/teachers/{teacher}'
*/
destroy.delete = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::destroy
* @see app/Http/Controllers/Director/TeacherController.php:109
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
* @see app/Http/Controllers/Director/TeacherController.php:109
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

const TeacherController = { index, store, update, updateAccess, resendInvitation, destroy }

export default TeacherController