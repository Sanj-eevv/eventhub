import IndexController from './IndexController'
import Auth from './Auth'
import DashboardController from './DashboardController'

const Controllers = {
    IndexController: Object.assign(IndexController, IndexController),
    Auth: Object.assign(Auth, Auth),
    DashboardController: Object.assign(DashboardController, DashboardController),
}

export default Controllers