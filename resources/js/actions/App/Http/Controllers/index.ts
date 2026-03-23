import Auth from './Auth'
import DashboardController from './DashboardController'
import Admin from './Admin'
import Director from './Director'
import Teacher from './Teacher'
import Owner from './Owner'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    DashboardController: Object.assign(DashboardController, DashboardController),
    Admin: Object.assign(Admin, Admin),
    Director: Object.assign(Director, Director),
    Teacher: Object.assign(Teacher, Teacher),
    Owner: Object.assign(Owner, Owner),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers