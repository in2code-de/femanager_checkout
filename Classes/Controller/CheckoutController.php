<?php
declare(strict_types=1);


namespace In2code\FemanagerCheckout\Controller;

    use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class NewController
 */
class CheckoutController extends ActionController
{
    /**
     * @var FrontendUserRepository
     *
     */
    protected $userRepository;

    /**
     * @var FrontendUserGroupRepository
     *
     */
    protected $userGroupRepository;

    /**
     * @var PersistenceManager
     *
     */
    protected $persistenceManager;

    /**
     * Render checkout form
     *
     * @return void
     */
    public function checkoutAction()
    {
        $feUserUid = $GLOBALS['TSFE']->fe_user->user['uid'];
        if ($feUserUid === null) {
            $this->addFlashMessage('You have to be logged in', 'Login nessecary', AbstractMessage::ERROR);
            $this->redirect('message');
        }
        $feUser = $this->userRepository->findByUid($feUserUid);

        //check if current user is already premium user
        if ($this->checkPremiumGroup($GLOBALS['TSFE']->fe_user->user['usergroup'],
                $this->settings['groups']['premium']) === true) {
            $this->addFlashMessage('You have already premium access', 'title,');
            $this->redirect('message');
        }
        $this->view->assign('FeUser', $feUser);
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function initializeCheckoutAction()
    {
        $this->controllerContext = $this->buildControllerContext();

        // check settings
        if (!$this->settings['groups']['basis'] OR !$this->settings['groups']['premium']) {
            $this->addFlashMessage('TypoScript setting for user groups is missing', 'Configuration Error',
                AbstractMessage::ERROR);
            $this->redirect('message');
        }
        if (!$this->settings['paypal']['client']['sandbox'] AND !$this->settings['paypal']['client']['production']) {
            $this->addFlashMessage('TypoScript setting for paypal environment is missing', 'Configuration Error',
                AbstractMessage::ERROR);
            $this->redirect('message');
        }
        if (!$this->settings['paypal']['client']['payment']['transactions']['amount']['total'] || !$this->settings['paypal']['client']['payment']['transactions']['amount']['currency']) {
            $this->addFlashMessage('TypoScript setting for paypal payment is missing', 'Configuration Error',
                AbstractMessage::ERROR);
            $this->redirect('message');
        }
    }

    /**
     * @param PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(
        PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param FrontendUserGroupRepository $userGroupRepository
     */
    public function injectUserGroupRepository(
        FrontendUserGroupRepository $userGroupRepository
    ) {
        $this->userGroupRepository = $userGroupRepository;
    }

    /**
     * @param FrontendUserRepository $userRepository
     */
    public function injectUserRepository(FrontendUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * perform the paypal payment
     *
     * @param FrontendUser $user
     *
     * @return void
     */
    public function payAction(FrontendUser $user)
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
        $basisGroup = $this->userGroupRepository->findByUid($this->settings['groups']['basis']);
        $premiumGroup = $this->userGroupRepository->findByUid($this->settings['groups']['premium']);
        $user->addUsergroup($premiumGroup);
        $user->removeUsergroup($basisGroup);
        $this->userRepository->update($user);
    }

    /**
     * cancel the paypal payment
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function cancelAction()
    {
        $this->addFlashMessage('Your payment was cancelled', 'Cancellation', AbstractMessage::INFO);
        $this->redirect('message');
    }

    /**
     *
     */
    public function messageAction()
    {

    }

    /**
     * @param array $arguments
     * @return bool
     */
    private function verifyPayment($arguments)
    {
        return $arguments['token'] && $arguments['paymentId'] && $arguments['PayerID'];
    }

    /**
     * @param array $usergroups
     * @param int $premium
     * @return bool
     */
    private function checkPremiumGroup($usergroups, $premium)
    {
        $usergroupsArray = explode(',', $usergroups);
        if (in_array($premium, $usergroupsArray, true)) {
            return true;
        }

        return false;
    }
}


