import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:165
* @route '/owner/directors/{director}/access'
*/
export const update = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/owner/directors/{director}/access',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:165
* @route '/owner/directors/{director}/access'
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
* @see app/Http/Controllers/Owner/DirectorController.php:165
* @route '/owner/directors/{director}/access'
*/
update.patch = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:165
* @route '/owner/directors/{director}/access'
*/
const updateForm = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\DirectorController::update
* @see app/Http/Controllers/Owner/DirectorController.php:165
* @route '/owner/directors/{director}/access'
*/
updateForm.patch = (args: { director: string | number | { id: string | number } } | [director: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const access = {
    update: Object.assign(update, update),
}

export default access