import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
export const request = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: request.url(options),
    method: 'get',
})

request.definition = {
    methods: ["get","head"],
    url: '/student/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
request.url = (options?: RouteQueryOptions) => {
    return request.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
request.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: request.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
request.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: request.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
const requestForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: request.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
requestForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: request.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::request
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
requestForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: request.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

request.form = requestForm

/**
* @see \App\Http\Controllers\StudentPasswordResetController::email
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
export const email = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: email.url(options),
    method: 'post',
})

email.definition = {
    methods: ["post"],
    url: '/student/forgot-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::email
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
email.url = (options?: RouteQueryOptions) => {
    return email.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::email
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
email.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: email.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::email
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
const emailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: email.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::email
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
emailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: email.url(options),
    method: 'post',
})

email.form = emailForm

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
export const reset = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

reset.definition = {
    methods: ["get","head"],
    url: '/student/reset-password/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
reset.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return reset.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
reset.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
reset.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reset.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
const resetForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reset.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
resetForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reset.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
resetForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reset.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

reset.form = resetForm

/**
* @see \App\Http\Controllers\StudentPasswordResetController::update
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/student/reset-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::update
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::update
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::update
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::update
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const password = {
    request: Object.assign(request, request),
    email: Object.assign(email, email),
    reset: Object.assign(reset, reset),
    update: Object.assign(update, update),
}

export default password