import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
export const show = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/activate-account/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
show.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
show.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
show.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
const showForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
showForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\UserActivationController::show
* @see app/Http/Controllers/Auth/UserActivationController.php:17
* @route '/activate-account/{token}'
*/
showForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Auth\UserActivationController::store
* @see app/Http/Controllers/Auth/UserActivationController.php:47
* @route '/activate-account'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/activate-account',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\UserActivationController::store
* @see app/Http/Controllers/Auth/UserActivationController.php:47
* @route '/activate-account'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\UserActivationController::store
* @see app/Http/Controllers/Auth/UserActivationController.php:47
* @route '/activate-account'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\UserActivationController::store
* @see app/Http/Controllers/Auth/UserActivationController.php:47
* @route '/activate-account'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\UserActivationController::store
* @see app/Http/Controllers/Auth/UserActivationController.php:47
* @route '/activate-account'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const activation = {
    show: Object.assign(show, show),
    store: Object.assign(store, store),
}

export default activation