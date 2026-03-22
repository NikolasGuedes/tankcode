import DashboardController from './DashboardController'
import ActivityController from './ActivityController'

const Teacher = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    ActivityController: Object.assign(ActivityController, ActivityController),
}

export default Teacher