<?php
declare(strict_types=1);


namespace In2code\FemanagerCheckout\Controller;

use In2code\Femanager\Domain\Model\User;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;

/**
 * Class NewController
 */
class CheckoutController extends ActionController
{
    /**
     * @var \In2code\Femanager\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * @var \In2code\Femanager\Domain\Repository\UserGroupRepository
     * @inject
     */
    protected $userGroupRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var \In2code\Femanager\Domain\Service\SendMailService
     * @inject
     */

    /**
     * Render checkout form
     *
     * @return void
     */
    public function checkoutAction()
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->settings, __FILE__ . '@' . __LINE__);

        $feUserUid = $GLOBALS['TSFE']->fe_user->user['uid'];
        if (is_null($feUserUid)) {
            $this->addFlashMessage('You have to be logged in', 'Login nessecary', AbstractMessage::ERROR);
            $this->redirect('message');
        }
        $feUser = $this->userRepository->findByUid($feUserUid);
        $this->view->assign('FeUser', $feUser);

    }

    /**
     * perform the paypal payment
     *
     * @param User $user
     *
     * @return void
     */
    public function payAction(User $user)
    {
        $feUserUid = $GLOBALS['TSFE']->fe_user->user['uid'];
        if ($feUserUid <> $user->getUid()) {
            $this->addFlashMessage('Something went wrong with your payment', 'Error', AbstractMessage::ERROR);
            $this->redirect('message');
        }

        if ($this->verifyPayment(GeneralUtility::_GET()) === false) {
            $this->addFlashMessage('Something went wrong with your payment', 'Error', AbstractMessage::ERROR);
            $this->redirect('message');
        }
        $basisGroup = $this->userGroupRepository->findByUid($this->settings['groups']['premium']);
        $premiumGroup = $this->userGroupRepository->findByUid($this->settings['groups']['premium']);
        $user->addUsergroup($premiumGroup);
        $user->removeUsergroup($basisGroup);
        $this->userRepository->update($user);
    }

    /**
     * cancel the paypal payment
     *
     * @param User $user
     *
     * @return void
     */
    public function cancelAction(User $user)
    {
        $this->addFlashMessage('Your payment was cancelled', 'Cancellation', AbstractMessage::INFO);
        $this->redirect('message');
    }


    public function messageAction()
    {

    }

    /**
     * @param array $arguments
     * @return bool
     */
    private function verifyPayment($arguments)
    {
        If ($arguments['token'] && $arguments['paymentId'] && $arguments['PayerID']){
            return true;
        }
        return false;
    }
}
