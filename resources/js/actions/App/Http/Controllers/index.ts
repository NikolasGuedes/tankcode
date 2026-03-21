import StudentsController from './StudentsController'
import StudentAuthController from './StudentAuthController'
import StudentPasswordResetController from './StudentPasswordResetController'
import RoomsController from './RoomsController'
import StudentEmailVerificationController from './StudentEmailVerificationController'
import ActivitiesController from './ActivitiesController'
import Settings from './Settings'
import Auth from './Auth'

const Controllers = {
    StudentsController: Object.assign(StudentsController, StudentsController),
    StudentAuthController: Object.assign(StudentAuthController, StudentAuthController),
    StudentPasswordResetController: Object.assign(StudentPasswordResetController, StudentPasswordResetController),
    RoomsController: Object.assign(RoomsController, RoomsController),
    StudentEmailVerificationController: Object.assign(StudentEmailVerificationController, StudentEmailVerificationController),
    ActivitiesController: Object.assign(ActivitiesController, ActivitiesController),
    Settings: Object.assign(Settings, Settings),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers