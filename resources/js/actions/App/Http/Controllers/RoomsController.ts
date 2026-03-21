import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/rooms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoomsController::index
* @see app/Http/Controllers/RoomsController.php:15
* @route '/rooms'
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
* @see \App\Http\Controllers\RoomsController::store
* @see app/Http/Controllers/RoomsController.php:33
* @route '/rooms'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/rooms',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RoomsController::store
* @see app/Http/Controllers/RoomsController.php:33
* @route '/rooms'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoomsController::store
* @see app/Http/Controllers/RoomsController.php:33
* @route '/rooms'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoomsController::store
* @see app/Http/Controllers/RoomsController.php:33
* @route '/rooms'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoomsController::store
* @see app/Http/Controllers/RoomsController.php:33
* @route '/rooms'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\RoomsController::update
* @see app/Http/Controllers/RoomsController.php:66
* @route '/rooms/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/rooms/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\RoomsController::update
* @see app/Http/Controllers/RoomsController.php:66
* @route '/rooms/{id}'
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
* @see \App\Http\Controllers\RoomsController::update
* @see app/Http/Controllers/RoomsController.php:66
* @route '/rooms/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RoomsController::update
* @see app/Http/Controllers/RoomsController.php:66
* @route '/rooms/{id}'
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
* @see \App\Http\Controllers\RoomsController::update
* @see app/Http/Controllers/RoomsController.php:66
* @route '/rooms/{id}'
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
* @see \App\Http\Controllers\RoomsController::destroy
* @see app/Http/Controllers/RoomsController.php:230
* @route '/rooms/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/rooms/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RoomsController::destroy
* @see app/Http/Controllers/RoomsController.php:230
* @route '/rooms/{id}'
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
* @see \App\Http\Controllers\RoomsController::destroy
* @see app/Http/Controllers/RoomsController.php:230
* @route '/rooms/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RoomsController::destroy
* @see app/Http/Controllers/RoomsController.php:230
* @route '/rooms/{id}'
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
* @see \App\Http\Controllers\RoomsController::destroy
* @see app/Http/Controllers/RoomsController.php:230
* @route '/rooms/{id}'
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
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
export const edit = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/rooms/{id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
edit.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
edit.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
edit.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
const editForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
editForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoomsController::edit
* @see app/Http/Controllers/RoomsController.php:94
* @route '/rooms/{id}/edit'
*/
editForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\RoomsController::addStudent
* @see app/Http/Controllers/RoomsController.php:168
* @route '/rooms/{room}/add-student'
*/
export const addStudent = (args: { room: string | number | { id: string | number } } | [room: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addStudent.url(args, options),
    method: 'post',
})

addStudent.definition = {
    methods: ["post"],
    url: '/rooms/{room}/add-student',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RoomsController::addStudent
* @see app/Http/Controllers/RoomsController.php:168
* @route '/rooms/{room}/add-student'
*/
addStudent.url = (args: { room: string | number | { id: string | number } } | [room: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { room: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { room: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            room: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        room: typeof args.room === 'object'
        ? args.room.id
        : args.room,
    }

    return addStudent.definition.url
            .replace('{room}', parsedArgs.room.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoomsController::addStudent
* @see app/Http/Controllers/RoomsController.php:168
* @route '/rooms/{room}/add-student'
*/
addStudent.post = (args: { room: string | number | { id: string | number } } | [room: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addStudent.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoomsController::addStudent
* @see app/Http/Controllers/RoomsController.php:168
* @route '/rooms/{room}/add-student'
*/
const addStudentForm = (args: { room: string | number | { id: string | number } } | [room: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addStudent.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoomsController::addStudent
* @see app/Http/Controllers/RoomsController.php:168
* @route '/rooms/{room}/add-student'
*/
addStudentForm.post = (args: { room: string | number | { id: string | number } } | [room: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addStudent.url(args, options),
    method: 'post',
})

addStudent.form = addStudentForm

/**
* @see \App\Http\Controllers\RoomsController::removeStudent
* @see app/Http/Controllers/RoomsController.php:216
* @route '/rooms/{room}/remove-student/{student}'
*/
export const removeStudent = (args: { room: string | number | { id: string | number }, student: string | number | { id: string | number } } | [room: string | number | { id: string | number }, student: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeStudent.url(args, options),
    method: 'delete',
})

removeStudent.definition = {
    methods: ["delete"],
    url: '/rooms/{room}/remove-student/{student}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RoomsController::removeStudent
* @see app/Http/Controllers/RoomsController.php:216
* @route '/rooms/{room}/remove-student/{student}'
*/
removeStudent.url = (args: { room: string | number | { id: string | number }, student: string | number | { id: string | number } } | [room: string | number | { id: string | number }, student: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            room: args[0],
            student: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        room: typeof args.room === 'object'
        ? args.room.id
        : args.room,
        student: typeof args.student === 'object'
        ? args.student.id
        : args.student,
    }

    return removeStudent.definition.url
            .replace('{room}', parsedArgs.room.toString())
            .replace('{student}', parsedArgs.student.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoomsController::removeStudent
* @see app/Http/Controllers/RoomsController.php:216
* @route '/rooms/{room}/remove-student/{student}'
*/
removeStudent.delete = (args: { room: string | number | { id: string | number }, student: string | number | { id: string | number } } | [room: string | number | { id: string | number }, student: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeStudent.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RoomsController::removeStudent
* @see app/Http/Controllers/RoomsController.php:216
* @route '/rooms/{room}/remove-student/{student}'
*/
const removeStudentForm = (args: { room: string | number | { id: string | number }, student: string | number | { id: string | number } } | [room: string | number | { id: string | number }, student: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeStudent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoomsController::removeStudent
* @see app/Http/Controllers/RoomsController.php:216
* @route '/rooms/{room}/remove-student/{student}'
*/
removeStudentForm.delete = (args: { room: string | number | { id: string | number }, student: string | number | { id: string | number } } | [room: string | number | { id: string | number }, student: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeStudent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

removeStudent.form = removeStudentForm

const RoomsController = { index, store, update, destroy, edit, addStudent, removeStudent }

export default RoomsController