import RegisterController from './RegisterController'
import LoginController from './LoginController'
import PasswordResetController from './PasswordResetController'
import LogoutController from './LogoutController'
import EmailVerificationController from './EmailVerificationController'

const Auth = {
    RegisterController: Object.assign(RegisterController, RegisterController),
    LoginController: Object.assign(LoginController, LoginController),
    PasswordResetController: Object.assign(PasswordResetController, PasswordResetController),
    LogoutController: Object.assign(LogoutController, LogoutController),
    EmailVerificationController: Object.assign(EmailVerificationController, EmailVerificationController),
}

export default Auth