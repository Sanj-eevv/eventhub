export interface Can {
    organization: { viewAny: boolean; create: boolean; update: boolean; delete: boolean }
    user: { viewAny: boolean; create: boolean; update: boolean; delete: boolean }
    role: { viewAny: boolean; create: boolean; update: boolean; delete: boolean }
    event: {
        viewAny: boolean
        create: boolean
        update: boolean
        delete: boolean
        publish: boolean
        cancel: boolean
        reserve: boolean
    }
    order: { viewAny: boolean; cancel: boolean }
    setting: { update: boolean }
    dashboard: { access: boolean }
}
