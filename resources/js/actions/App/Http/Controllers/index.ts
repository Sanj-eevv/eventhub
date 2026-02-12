import IndexController from './IndexController'
import Auth from './Auth'

const Controllers = {
    IndexController: Object.assign(IndexController, IndexController),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers