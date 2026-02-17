import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
export const showPasswordResetRequestForm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showPasswordResetRequestForm.url(options),
    method: 'get',
})

showPasswordResetRequestForm.definition = {
    methods: ["get","head"],
    url: '/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
showPasswordResetRequestForm.url = (options?: RouteQueryOptions) => {
    return showPasswordResetRequestForm.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
showPasswordResetRequestForm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showPasswordResetRequestForm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
showPasswordResetRequestForm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showPasswordResetRequestForm.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
const showPasswordResetRequestFormForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showPasswordResetRequestForm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
showPasswordResetRequestFormForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showPasswordResetRequestForm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetRequestForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:23
* @route '/forgot-password'
*/
showPasswordResetRequestFormForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showPasswordResetRequestForm.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showPasswordResetRequestForm.form = showPasswordResetRequestFormForm

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::sendPasswordResetEmail
* @see app/Http/Controllers/Auth/PasswordResetController.php:30
* @route '/forgot-password'
*/
export const sendPasswordResetEmail = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendPasswordResetEmail.url(options),
    method: 'post',
})

sendPasswordResetEmail.definition = {
    methods: ["post"],
    url: '/forgot-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::sendPasswordResetEmail
* @see app/Http/Controllers/Auth/PasswordResetController.php:30
* @route '/forgot-password'
*/
sendPasswordResetEmail.url = (options?: RouteQueryOptions) => {
    return sendPasswordResetEmail.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::sendPasswordResetEmail
* @see app/Http/Controllers/Auth/PasswordResetController.php:30
* @route '/forgot-password'
*/
sendPasswordResetEmail.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendPasswordResetEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::sendPasswordResetEmail
* @see app/Http/Controllers/Auth/PasswordResetController.php:30
* @route '/forgot-password'
*/
const sendPasswordResetEmailForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendPasswordResetEmail.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::sendPasswordResetEmail
* @see app/Http/Controllers/Auth/PasswordResetController.php:30
* @route '/forgot-password'
*/
sendPasswordResetEmailForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendPasswordResetEmail.url(options),
    method: 'post',
})

sendPasswordResetEmail.form = sendPasswordResetEmailForm

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
export const showPasswordResetForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showPasswordResetForm.url(args, options),
    method: 'get',
})

showPasswordResetForm.definition = {
    methods: ["get","head"],
    url: '/reset-password/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
showPasswordResetForm.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showPasswordResetForm.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
showPasswordResetForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showPasswordResetForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
showPasswordResetForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showPasswordResetForm.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
const showPasswordResetFormForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showPasswordResetForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
showPasswordResetFormForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showPasswordResetForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::showPasswordResetForm
* @see app/Http/Controllers/Auth/PasswordResetController.php:37
* @route '/reset-password/{token}'
*/
showPasswordResetFormForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showPasswordResetForm.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showPasswordResetForm.form = showPasswordResetFormForm

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::resetPassword
* @see app/Http/Controllers/Auth/PasswordResetController.php:45
* @route '/reset-password/update'
*/
export const resetPassword = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

resetPassword.definition = {
    methods: ["post"],
    url: '/reset-password/update',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::resetPassword
* @see app/Http/Controllers/Auth/PasswordResetController.php:45
* @route '/reset-password/update'
*/
resetPassword.url = (options?: RouteQueryOptions) => {
    return resetPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::resetPassword
* @see app/Http/Controllers/Auth/PasswordResetController.php:45
* @route '/reset-password/update'
*/
resetPassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::resetPassword
* @see app/Http/Controllers/Auth/PasswordResetController.php:45
* @route '/reset-password/update'
*/
const resetPasswordForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resetPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\PasswordResetController::resetPassword
* @see app/Http/Controllers/Auth/PasswordResetController.php:45
* @route '/reset-password/update'
*/
resetPasswordForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resetPassword.url(options),
    method: 'post',
})

resetPassword.form = resetPasswordForm

const PasswordResetController = { showPasswordResetRequestForm, sendPasswordResetEmail, showPasswordResetForm, resetPassword }

export default PasswordResetController