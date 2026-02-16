import RegisterController from './RegisterController'
import LoginController from './LoginController'
import LogoutController from './LogoutController'

const Auth = {
    RegisterController: Object.assign(RegisterController, RegisterController),
    LoginController: Object.assign(LoginController, LoginController),
    LogoutController: Object.assign(LogoutController, LogoutController),
}

export default Auth