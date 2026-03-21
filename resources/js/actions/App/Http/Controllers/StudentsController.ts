import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/students',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentsController::index
* @see app/Http/Controllers/StudentsController.php:21
* @route '/students'
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
* @see \App\Http\Controllers\StudentsController::store
* @see app/Http/Controllers/StudentsController.php:43
* @route '/students'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/students',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentsController::store
* @see app/Http/Controllers/StudentsController.php:43
* @route '/students'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::store
* @see app/Http/Controllers/StudentsController.php:43
* @route '/students'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::store
* @see app/Http/Controllers/StudentsController.php:43
* @route '/students'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::store
* @see app/Http/Controllers/StudentsController.php:43
* @route '/students'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\StudentsController::importStudents
* @see app/Http/Controllers/StudentsController.php:129
* @route '/students/import'
*/
export const importStudents = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importStudents.url(options),
    method: 'post',
})

importStudents.definition = {
    methods: ["post"],
    url: '/students/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentsController::importStudents
* @see app/Http/Controllers/StudentsController.php:129
* @route '/students/import'
*/
importStudents.url = (options?: RouteQueryOptions) => {
    return importStudents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::importStudents
* @see app/Http/Controllers/StudentsController.php:129
* @route '/students/import'
*/
importStudents.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importStudents.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::importStudents
* @see app/Http/Controllers/StudentsController.php:129
* @route '/students/import'
*/
const importStudentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importStudents.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::importStudents
* @see app/Http/Controllers/StudentsController.php:129
* @route '/students/import'
*/
importStudentsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importStudents.url(options),
    method: 'post',
})

importStudents.form = importStudentsForm

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
export const downloadTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadTemplate.url(options),
    method: 'get',
})

downloadTemplate.definition = {
    methods: ["get","head"],
    url: '/students/download-template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
downloadTemplate.url = (options?: RouteQueryOptions) => {
    return downloadTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
downloadTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
downloadTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadTemplate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
const downloadTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
downloadTemplateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentsController::downloadTemplate
* @see app/Http/Controllers/StudentsController.php:318
* @route '/students/download-template'
*/
downloadTemplateForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

downloadTemplate.form = downloadTemplateForm

/**
* @see \App\Http\Controllers\StudentsController::update
* @see app/Http/Controllers/StudentsController.php:82
* @route '/students/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/students/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\StudentsController::update
* @see app/Http/Controllers/StudentsController.php:82
* @route '/students/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::update
* @see app/Http/Controllers/StudentsController.php:82
* @route '/students/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\StudentsController::update
* @see app/Http/Controllers/StudentsController.php:82
* @route '/students/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::update
* @see app/Http/Controllers/StudentsController.php:82
* @route '/students/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\StudentsController::destroy
* @see app/Http/Controllers/StudentsController.php:110
* @route '/students/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/students/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\StudentsController::destroy
* @see app/Http/Controllers/StudentsController.php:110
* @route '/students/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::destroy
* @see app/Http/Controllers/StudentsController.php:110
* @route '/students/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\StudentsController::destroy
* @see app/Http/Controllers/StudentsController.php:110
* @route '/students/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::destroy
* @see app/Http/Controllers/StudentsController.php:110
* @route '/students/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\StudentsController::togglePlatformAccess
* @see app/Http/Controllers/StudentsController.php:269
* @route '/students/{id}/toggle-access'
*/
export const togglePlatformAccess = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: togglePlatformAccess.url(args, options),
    method: 'post',
})

togglePlatformAccess.definition = {
    methods: ["post"],
    url: '/students/{id}/toggle-access',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentsController::togglePlatformAccess
* @see app/Http/Controllers/StudentsController.php:269
* @route '/students/{id}/toggle-access'
*/
togglePlatformAccess.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return togglePlatformAccess.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::togglePlatformAccess
* @see app/Http/Controllers/StudentsController.php:269
* @route '/students/{id}/toggle-access'
*/
togglePlatformAccess.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: togglePlatformAccess.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::togglePlatformAccess
* @see app/Http/Controllers/StudentsController.php:269
* @route '/students/{id}/toggle-access'
*/
const togglePlatformAccessForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: togglePlatformAccess.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::togglePlatformAccess
* @see app/Http/Controllers/StudentsController.php:269
* @route '/students/{id}/toggle-access'
*/
togglePlatformAccessForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: togglePlatformAccess.url(args, options),
    method: 'post',
})

togglePlatformAccess.form = togglePlatformAccessForm

/**
* @see \App\Http\Controllers\StudentsController::resendVerificationEmail
* @see app/Http/Controllers/StudentsController.php:329
* @route '/students/{id}/resend-verification'
*/
export const resendVerificationEmail = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendVerificationEmail.url(args, options),
    method: 'post',
})

resendVerificationEmail.definition = {
    methods: ["post"],
    url: '/students/{id}/resend-verification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentsController::resendVerificationEmail
* @see app/Http/Controllers/StudentsController.php:329
* @route '/students/{id}/resend-verification'
*/
resendVerificationEmail.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return resendVerificationEmail.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentsController::resendVerificationEmail
* @see app/Http/Controllers/StudentsController.php:329
* @route '/students/{id}/resend-verification'
*/
resendVerificationEmail.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendVerificationEmail.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::resendVerificationEmail
* @see app/Http/Controllers/StudentsController.php:329
* @route '/students/{id}/resend-verification'
*/
const resendVerificationEmailForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendVerificationEmail.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentsController::resendVerificationEmail
* @see app/Http/Controllers/StudentsController.php:329
* @route '/students/{id}/resend-verification'
*/
resendVerificationEmailForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendVerificationEmail.url(args, options),
    method: 'post',
})

resendVerificationEmail.form = resendVerificationEmailForm

const StudentsController = { index, store, importStudents, downloadTemplate, update, destroy, togglePlatformAccess, resendVerificationEmail }

export default StudentsController