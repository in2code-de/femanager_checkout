<?php
declare(strict_types=1);


namespace In2code\FemanagerCheckout\Controller;

use In2code\Femanager\Domain\Model\User;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
        $feUserUid = $GLOBALS['TSFE']->fe_user->user['uid'];
        if (is_null($feUserUid)) {
            $this->addFlashMessage('You have to be logged in', 'Login nessecary', AbstractMessage::ERROR);
            $this->redirect('message');
        }
        $feUser = $this->userRepository->findByUid($feUserUid);
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
        if (!$this->settings['groups']['basis'] OR !$this->settings['groups']['premium'] )
        {
            $this->addFlashMessage('TypoScript setting for user groups is missing', 'Configuration Error', AbstractMessage::ERROR);
            $this->redirect('message');
        }
        if (!$this->settings['paypal']['client']['sandbox'] AND !$this->settings['paypal']['client']['production'] )
        {
            $this->addFlashMessage('TypoScript setting for paypal environment is missing', 'Configuration Error', AbstractMessage::ERROR);
            $this->redirect('message');
        }
        if (!$this->settings['paypal']['client']['payment']['transactions']['amount']['total'] || !$this->settings['paypal']['client']['payment']['transactions']['amount']['currency'] )
        {
            $this->addFlashMessage('TypoScript setting for paypal payment is missing', 'Configuration Error', AbstractMessage::ERROR);
            $this->redirect('message');
        }
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
        $basisGroup = $this->userGroupRepository->findByUid($this->settings['groups']['basis']);
        $premiumGroup = $this->userGroupRepository->findByUid($this->settings['groups']['premium']);
        $user->removeUsergroup($basisGroup);
        $user->addUsergroup($premiumGroup);
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
        If ($arguments['token'] && $arguments['paymentId'] && $arguments['PayerID']) {
            return true;
        }

        return false;
    }
    /**
     * Initialize the controller context
     *
     * @return \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext ControllerContext to be passed to the view
     * @api
     */
    protected function buildControllerContext()
    {
        /** @var $controllerContext \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext */
        $controllerContext = $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext::class);
        $controllerContext->setRequest($this->request);
        $controllerContext->setResponse($this->response);
        if ($this->arguments !== null) {
            $controllerContext->setArguments($this->arguments);
        }
        $controllerContext->setUriBuilder($this->uriBuilder);

        return $controllerContext;
    }
}


