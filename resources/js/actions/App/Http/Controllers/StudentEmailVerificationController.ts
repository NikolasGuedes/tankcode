import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
export const verify = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/student/verify-email/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verify.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return verify.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verify.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verify.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
const verifyForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verify
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify.form = verifyForm

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::createPassword
* @see app/Http/Controllers/StudentEmailVerificationController.php:74
* @route '/student/create-password'
*/
export const createPassword = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPassword.url(options),
    method: 'post',
})

createPassword.definition = {
    methods: ["post"],
    url: '/student/create-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::createPassword
* @see app/Http/Controllers/StudentEmailVerificationController.php:74
* @route '/student/create-password'
*/
createPassword.url = (options?: RouteQueryOptions) => {
    return createPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::createPassword
* @see app/Http/Controllers/StudentEmailVerificationController.php:74
* @route '/student/create-password'
*/
createPassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::createPassword
* @see app/Http/Controllers/StudentEmailVerificationController.php:74
* @route '/student/create-password'
*/
const createPasswordForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::createPassword
* @see app/Http/Controllers/StudentEmailVerificationController.php:74
* @route '/student/create-password'
*/
createPasswordForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPassword.url(options),
    method: 'post',
})

createPassword.form = createPasswordForm

const StudentEmailVerificationController = { verify, createPassword }

export default StudentEmailVerificationController