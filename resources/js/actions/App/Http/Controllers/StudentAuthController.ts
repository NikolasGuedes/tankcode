import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
export const showLogin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showLogin.url(options),
    method: 'get',
})

showLogin.definition = {
    methods: ["get","head"],
    url: '/student/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
showLogin.url = (options?: RouteQueryOptions) => {
    return showLogin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
showLogin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showLogin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
showLogin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showLogin.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
const showLoginForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showLogin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
showLoginForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showLogin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StudentAuthController::showLogin
* @see app/Http/Controllers/StudentAuthController.php:14
* @route '/student/login'
*/
showLoginForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showLogin.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showLogin.form = showLoginForm

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:24
* @route '/student/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

login.definition = {
    methods: ["post"],
    url: '/student/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:24
* @route '/student/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:24
* @route '/student/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:24
* @route '/student/login'
*/
const loginForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: login.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StudentAuthController::login
* @see app/Http/Controllers/StudentAuthController.php:24
* @route '/student/login'
*/
loginForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: login.url(options),
    method: 'post',
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

const StudentAuthController = { showLogin, login, logout, dashboard }

export default StudentAuthController