import RegisterController from './RegisterController'
import Organization from './Organization'
import LoginController from './LoginController'
import PasswordResetController from './PasswordResetController'
import LogoutController from './LogoutController'
import EmailVerificationController from './EmailVerificationController'

const Auth = {
    RegisterController: Object.assign(RegisterController, RegisterController),
    Organization: Object.assign(Organization, Organization),
    LoginController: Object.assign(LoginController, LoginController),
    PasswordResetController: Object.assign(PasswordResetController, PasswordResetController),
    LogoutController: Object.assign(LogoutController, LogoutController),
    EmailVerificationController: Object.assign(EmailVerificationController, EmailVerificationController),
}

export default Auth