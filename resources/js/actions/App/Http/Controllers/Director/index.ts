import DashboardController from './DashboardController'
import ClassroomController from './ClassroomController'
import TeacherController from './TeacherController'
import StudentController from './StudentController'

const Director = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    ClassroomController: Object.assign(ClassroomController, ClassroomController),
    TeacherController: Object.assign(TeacherController, TeacherController),
    StudentController: Object.assign(StudentController, StudentController),
}

export default Director