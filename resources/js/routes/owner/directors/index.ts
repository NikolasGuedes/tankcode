import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import access from './access'
/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/directors',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::index
* @see app/Http/Controllers/Owner/DirectorController.php:22
* @route '/owner/directors'
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
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/owner/directors/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::create
* @see app/Http/Controllers/Owner/DirectorController.php:86
* @route '/owner/directors/create'
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
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
export const edit = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/owner/directors/{director}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
edit.url = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { director: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { director: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            director: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        director: typeof args.director === 'object'
        ? args.director.id
        : args.director,
    }

    return edit.definition.url
            .replace('{director}', parsedArgs.director.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
edit.get = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
edit.head = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
const editForm = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
editForm.get = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::edit
* @see app/Http/Controllers/Owner/DirectorController.php:94
* @route '/owner/directors/{director}/edit'
*/
editForm.head = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Owner\DirectorController::store
* @see app/Http/Controllers/Owner/DirectorController.php:117
* @route '/owner/directors'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/directors',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::store
* @see app/Http/Controllers/Owner/DirectorController.php:117
* @route '/owner/directors'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::store
* @see app/Http/Controllers/Owner/DirectorController.php:117
* @route '/owner/directors'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::store
* @see app/Http/Controllers/Owner/DirectorController.php:117
* @route '/owner/directors'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::store
* @see app/Http/Controllers/Owner/DirectorController.php:117
* @route '/owner/directors'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:138
* @route '/owner/directors/{director}'
*/
export const update = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/owner/directors/{director}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:138
* @route '/owner/directors/{director}'
*/
update.url = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { director: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { director: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            director: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        director: typeof args.director === 'object'
        ? args.director.id
        : args.director,
    }

    return update.definition.url
            .replace('{director}', parsedArgs.director.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:138
* @route '/owner/directors/{director}'
*/
update.put = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:138
* @route '/owner/directors/{director}'
*/
const updateForm = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:138
* @route '/owner/directors/{director}'
*/
updateForm.put = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Owner\DirectorController::resendInvitation
* @see app/Http/Controllers/Owner/DirectorController.php:183
* @route '/owner/directors/{director}/resend-invitation'
*/
export const resendInvitation = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.definition = {
    methods: ["post"],
    url: '/owner/directors/{director}/resend-invitation',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::resendInvitation
* @see app/Http/Controllers/Owner/DirectorController.php:183
* @route '/owner/directors/{director}/resend-invitation'
*/
resendInvitation.url = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { director: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { director: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            director: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        director: typeof args.director === 'object'
        ? args.director.id
        : args.director,
    }

    return resendInvitation.definition.url
            .replace('{director}', parsedArgs.director.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::resendInvitation
* @see app/Http/Controllers/Owner/DirectorController.php:183
* @route '/owner/directors/{director}/resend-invitation'
*/
resendInvitation.post = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::resendInvitation
* @see app/Http/Controllers/Owner/DirectorController.php:183
* @route '/owner/directors/{director}/resend-invitation'
*/
const resendInvitationForm = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::resendInvitation
* @see app/Http/Controllers/Owner/DirectorController.php:183
* @route '/owner/directors/{director}/resend-invitation'
*/
resendInvitationForm.post = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.form = resendInvitationForm

/**
* @see \App\Http\Controllers\Owner\DirectorController::destroy
* @see app/Http/Controllers/Owner/DirectorController.php:155
* @route '/owner/directors/{director}'
*/
export const destroy = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/directors/{director}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::destroy
* @see app/Http/Controllers/Owner/DirectorController.php:155
* @route '/owner/directors/{director}'
*/
destroy.url = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { director: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { director: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            director: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        director: typeof args.director === 'object'
        ? args.director.id
        : args.director,
    }

    return destroy.definition.url
            .replace('{director}', parsedArgs.director.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DirectorController::destroy
* @see app/Http/Controllers/Owner/DirectorController.php:155
* @route '/owner/directors/{director}'
*/
destroy.delete = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::destroy
* @see app/Http/Controllers/Owner/DirectorController.php:155
* @route '/owner/directors/{director}'
*/
const destroyForm = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::destroy
* @see app/Http/Controllers/Owner/DirectorController.php:155
* @route '/owner/directors/{director}'
*/
destroyForm.delete = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const directors = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    edit: Object.assign(edit, edit),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    access: Object.assign(access, access),
    resendInvitation: Object.assign(resendInvitation, resendInvitation),
    destroy: Object.assign(destroy, destroy),
}

export default directors