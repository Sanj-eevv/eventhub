import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
import organization578ad3 from './organization'
/**
* @see \App\Http\Controllers\Auth\RegisterController::store
* @see app/Http/Controllers/Auth/RegisterController.php:24
* @route '/register'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\RegisterController::store
* @see app/Http/Controllers/Auth/RegisterController.php:24
* @route '/register'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegisterController::store
* @see app/Http/Controllers/Auth/RegisterController.php:24
* @route '/register'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\RegisterController::store
* @see app/Http/Controllers/Auth/RegisterController.php:24
* @route '/register'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\RegisterController::store
* @see app/Http/Controllers/Auth/RegisterController.php:24
* @route '/register'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
export const organization = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: organization.url(options),
    method: 'get',
})

organization.definition = {
    methods: ["get","head"],
    url: '/register/organization',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
organization.url = (options?: RouteQueryOptions) => {
    return organization.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
organization.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: organization.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
organization.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: organization.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
const organizationForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: organization.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
organizationForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: organization.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\Organization\RegisterController::organization
* @see app/Http/Controllers/Auth/Organization/RegisterController.php:21
* @route '/register/organization'
*/
organizationForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: organization.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

organization.form = organizationForm

const register = {
    store: Object.assign(store, store),
    organization: Object.assign(organization, organization578ad3),
}

export default register