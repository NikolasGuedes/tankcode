import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import access from './access'
/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/director/students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::index
* @see app/Http/Controllers/Director/StudentController.php:27
* @route '/director/students'
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
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
export const template = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: template.url(options),
    method: 'get',
})

template.definition = {
    methods: ["get","head"],
    url: '/director/students/template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
template.url = (options?: RouteQueryOptions) => {
    return template.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
template.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: template.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
template.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: template.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
const templateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: template.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
templateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: template.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::template
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
templateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: template.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

template.form = templateForm

/**
* @see \App\Http\Controllers\Director\StudentController::importMethod
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/director/students/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\StudentController::importMethod
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::importMethod
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::importMethod
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
const importMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::importMethod
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
importMethodForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importMethod.url(options),
    method: 'post',
})

importMethod.form = importMethodForm

/**
* @see \App\Http\Controllers\Director\StudentController::store
* @see app/Http/Controllers/Director/StudentController.php:99
* @route '/director/students'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/director/students',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\StudentController::store
* @see app/Http/Controllers/Director/StudentController.php:99
* @route '/director/students'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::store
* @see app/Http/Controllers/Director/StudentController.php:99
* @route '/director/students'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::store
* @see app/Http/Controllers/Director/StudentController.php:99
* @route '/director/students'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::store
* @see app/Http/Controllers/Director/StudentController.php:99
* @route '/director/students'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Director\StudentController::update
* @see app/Http/Controllers/Director/StudentController.php:127
* @route '/director/students/{student}'
*/
export const update = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/director/students/{student}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Director\StudentController::update
* @see app/Http/Controllers/Director/StudentController.php:127
* @route '/director/students/{student}'
*/
update.url = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { student: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { student: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            student: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        student: typeof args.student === 'object'
        ? args.student.id
        : args.student,
    }

    return update.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::update
* @see app/Http/Controllers/Director/StudentController.php:127
* @route '/director/students/{student}'
*/
update.put = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Director\StudentController::update
* @see app/Http/Controllers/Director/StudentController.php:127
* @route '/director/students/{student}'
*/
const updateForm = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::update
* @see app/Http/Controllers/Director/StudentController.php:127
* @route '/director/students/{student}'
*/
updateForm.put = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Director\StudentController::resendInvitation
* @see app/Http/Controllers/Director/StudentController.php:179
* @route '/director/students/{student}/resend-invitation'
*/
export const resendInvitation = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.definition = {
    methods: ["post"],
    url: '/director/students/{student}/resend-invitation',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\StudentController::resendInvitation
* @see app/Http/Controllers/Director/StudentController.php:179
* @route '/director/students/{student}/resend-invitation'
*/
resendInvitation.url = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { student: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { student: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            student: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        student: typeof args.student === 'object'
        ? args.student.id
        : args.student,
    }

    return resendInvitation.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::resendInvitation
* @see app/Http/Controllers/Director/StudentController.php:179
* @route '/director/students/{student}/resend-invitation'
*/
resendInvitation.post = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::resendInvitation
* @see app/Http/Controllers/Director/StudentController.php:179
* @route '/director/students/{student}/resend-invitation'
*/
const resendInvitationForm = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::resendInvitation
* @see app/Http/Controllers/Director/StudentController.php:179
* @route '/director/students/{student}/resend-invitation'
*/
resendInvitationForm.post = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendInvitation.url(args, options),
    method: 'post',
})

resendInvitation.form = resendInvitationForm

/**
* @see \App\Http\Controllers\Director\StudentController::destroy
* @see app/Http/Controllers/Director/StudentController.php:150
* @route '/director/students/{student}'
*/
export const destroy = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/director/students/{student}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Director\StudentController::destroy
* @see app/Http/Controllers/Director/StudentController.php:150
* @route '/director/students/{student}'
*/
destroy.url = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { student: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { student: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            student: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        student: typeof args.student === 'object'
        ? args.student.id
        : args.student,
    }

    return destroy.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::destroy
* @see app/Http/Controllers/Director/StudentController.php:150
* @route '/director/students/{student}'
*/
destroy.delete = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Director\StudentController::destroy
* @see app/Http/Controllers/Director/StudentController.php:150
* @route '/director/students/{student}'
*/
const destroyForm = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::destroy
* @see app/Http/Controllers/Director/StudentController.php:150
* @route '/director/students/{student}'
*/
destroyForm.delete = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const students = {
    index: Object.assign(index, index),
    template: Object.assign(template, template),
    import: Object.assign(importMethod, importMethod),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    access: Object.assign(access, access),
    resendInvitation: Object.assign(resendInvitation, resendInvitation),
    destroy: Object.assign(destroy, destroy),
}

export default students