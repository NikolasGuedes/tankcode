import AdminDashboardController from './AdminDashboardController'
import SchoolController from './SchoolController'
import PointOfSchoolController from './PointOfSchoolController'
import UserController from './UserController'

const Admin = {
    AdminDashboardController: Object.assign(AdminDashboardController, AdminDashboardController),
    SchoolController: Object.assign(SchoolController, SchoolController),
    PointOfSchoolController: Object.assign(PointOfSchoolController, PointOfSchoolController),
    UserController: Object.assign(UserController, UserController),
}

export default Admin