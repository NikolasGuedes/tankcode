import Auth from './Auth'
import DashboardController from './DashboardController'
import Admin from './Admin'
import Director from './Director'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    DashboardController: Object.assign(DashboardController, DashboardController),
    Admin: Object.assign(Admin, Admin),
    Director: Object.assign(Director, Director),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers