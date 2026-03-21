import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
export const showRequestForm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showRequestForm.url(options),
    method: 'get',
})

showRequestForm.definition = {
    methods: ["get","head"],
    url: '/student/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
showRequestForm.url = (options?: RouteQueryOptions) => {
    return showRequestForm.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
showRequestForm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showRequestForm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
showRequestForm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showRequestForm.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
const showRequestFormForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showRequestForm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
showRequestFormForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showRequestForm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showRequestForm
* @see app/Http/Controllers/StudentPasswordResetController.php:17
* @route '/student/forgot-password'
*/
showRequestFormForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showRequestForm.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showRequestForm.form = showRequestFormForm

/**
* @see \App\Http\Controllers\StudentPasswordResetController::sendResetLinkEmail
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
export const sendResetLinkEmail = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendResetLinkEmail.url(options),
    method: 'post',
})

sendResetLinkEmail.definition = {
    methods: ["post"],
    url: '/student/forgot-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::sendResetLinkEmail
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
sendResetLinkEmail.url = (options?: RouteQueryOptions) => {
    return sendResetLinkEmail.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::sendResetLinkEmail
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
sendResetLinkEmail.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendResetLinkEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::sendResetLinkEmail
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
const sendResetLinkEmailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendResetLinkEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::sendResetLinkEmail
* @see app/Http/Controllers/StudentPasswordResetController.php:22
* @route '/student/forgot-password'
*/
sendResetLinkEmailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendResetLinkEmail.url(options),
    method: 'post',
})

sendResetLinkEmail.form = sendResetLinkEmailForm

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
export const showResetForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showResetForm.url(args, options),
    method: 'get',
})

showResetForm.definition = {
    methods: ["get","head"],
    url: '/student/reset-password/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
showResetForm.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showResetForm.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
showResetForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showResetForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
showResetForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showResetForm.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
const showResetFormForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showResetForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
showResetFormForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showResetForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::showResetForm
* @see app/Http/Controllers/StudentPasswordResetController.php:74
* @route '/student/reset-password/{token}'
*/
showResetFormForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showResetForm.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showResetForm.form = showResetFormForm

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
export const reset = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(options),
    method: 'post',
})

reset.definition = {
    methods: ["post"],
    url: '/student/reset-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
reset.url = (options?: RouteQueryOptions) => {
    return reset.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
reset.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
const resetForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reset.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentPasswordResetController::reset
* @see app/Http/Controllers/StudentPasswordResetController.php:98
* @route '/student/reset-password'
*/
resetForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reset.url(options),
    method: 'post',
})

reset.form = resetForm

const StudentPasswordResetController = { showRequestForm, sendResetLinkEmail, showResetForm, reset }

export default StudentPasswordResetController