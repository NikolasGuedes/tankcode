import DashboardController from './DashboardController'
import DirectorController from './DirectorController'

const Owner = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    DirectorController: Object.assign(DirectorController, DirectorController),
}

export default Owner