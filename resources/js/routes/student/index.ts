import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import loginDf2c2a from './login'
import password from './password'
/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/student/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
const loginForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
loginForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
loginForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

login.form = loginForm

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

logout.definition = {
    methods: ["get","head"],
    url: '/student/logout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
logout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
logout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logout.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
logoutForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::logout
* @see app/Http/Controllers/StudentAuthController.php:91
* @route '/student/logout'
*/
logoutForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logout.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logout.form = logoutForm

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/student/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::dashboard
* @see app/Http/Controllers/StudentAuthController.php:73
* @route '/student/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
export const verifyEmail = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verifyEmail.url(args, options),
    method: 'get',
})

verifyEmail.definition = {
    methods: ["get","head"],
    url: '/student/verify-email/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyEmail.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return verifyEmail.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyEmail.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verifyEmail.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyEmail.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verifyEmail.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
const verifyEmailForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verifyEmail.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyEmailForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verifyEmail.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentEmailVerificationController::verifyEmail
* @see app/Http/Controllers/StudentEmailVerificationController.php:14
* @route '/student/verify-email/{token}'
*/
verifyEmailForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verifyEmail.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verifyEmail.form = verifyEmailForm

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

const student = {
    login: Object.assign(login, loginDf2c2a),
    logout: Object.assign(logout, logout),
    password: Object.assign(password, password),
    dashboard: Object.assign(dashboard, dashboard),
    verifyEmail: Object.assign(verifyEmail, verifyEmail),
    createPassword: Object.assign(createPassword, createPassword),
}

export default student