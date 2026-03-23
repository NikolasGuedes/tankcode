import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
export const downloadTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadTemplate.url(options),
    method: 'get',
})

downloadTemplate.definition = {
    methods: ["get","head"],
    url: '/director/students/template',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
downloadTemplate.url = (options?: RouteQueryOptions) => {
    return downloadTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
downloadTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
downloadTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadTemplate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
const downloadTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
*/
downloadTemplateForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadTemplate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Director\StudentController::downloadTemplate
* @see app/Http/Controllers/Director/StudentController.php:303
* @route '/director/students/template'
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
* @see \App\Http\Controllers\Director\StudentController::importStudents
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
export const importStudents = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importStudents.url(options),
    method: 'post',
})

importStudents.definition = {
    methods: ["post"],
    url: '/director/students/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Director\StudentController::importStudents
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
importStudents.url = (options?: RouteQueryOptions) => {
    return importStudents.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::importStudents
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
importStudents.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importStudents.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::importStudents
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
const importStudentsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importStudents.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::importStudents
* @see app/Http/Controllers/Director/StudentController.php:192
* @route '/director/students/import'
*/
importStudentsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: importStudents.url(options),
    method: 'post',
})

importStudents.form = importStudentsForm

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
* @see \App\Http\Controllers\Director\StudentController::updateAccess
* @see app/Http/Controllers/Director/StudentController.php:161
* @route '/director/students/{student}/access'
*/
export const updateAccess = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateAccess.url(args, options),
    method: 'patch',
})

updateAccess.definition = {
    methods: ["patch"],
    url: '/director/students/{student}/access',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Director\StudentController::updateAccess
* @see app/Http/Controllers/Director/StudentController.php:161
* @route '/director/students/{student}/access'
*/
updateAccess.url = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return updateAccess.definition.url
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\StudentController::updateAccess
* @see app/Http/Controllers/Director/StudentController.php:161
* @route '/director/students/{student}/access'
*/
updateAccess.patch = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateAccess.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Director\StudentController::updateAccess
* @see app/Http/Controllers/Director/StudentController.php:161
* @route '/director/students/{student}/access'
*/
const updateAccessForm = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateAccess.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\StudentController::updateAccess
* @see app/Http/Controllers/Director/StudentController.php:161
* @route '/director/students/{student}/access'
*/
updateAccessForm.patch = (args: { student: string | number | { id: string | number } } | [student: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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

const StudentController = { index, downloadTemplate, importStudents, store, update, updateAccess, resendInvitation, destroy }

export default StudentController