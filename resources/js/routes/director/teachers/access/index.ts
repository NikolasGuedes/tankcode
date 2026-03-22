import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}/access'
*/
export const update = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/director/teachers/{teacher}/access',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}/access'
*/
update.url = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { teacher: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { teacher: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            teacher: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        teacher: typeof args.teacher === 'object'
        ? args.teacher.id
        : args.teacher,
    }

    return update.definition.url
            .replace('{teacher}', parsedArgs.teacher.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}/access'
*/
update.patch = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}/access'
*/
const updateForm = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Director\TeacherController::update
* @see app/Http/Controllers/Director/TeacherController.php:157
* @route '/director/teachers/{teacher}/access'
*/
updateForm.patch = (args: { teacher: string | number | { id: string | number } } | [teacher: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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