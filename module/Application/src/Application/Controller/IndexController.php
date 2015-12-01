<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{

    /**
     * Главная страница приложения
     * @return ViewModel
     */
    public function indexAction()
    {
        $coursePresenter = $this->getServiceLocator()->get('CourseGraphPresenter');

        return new ViewModel([
                'coursePresenter' => $coursePresenter,
            ]
        );
    }
}
