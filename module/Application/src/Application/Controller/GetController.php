<?php
/**
 * @package Application
 * @author Nikolay Kovenko <nickk@templatemonster.me>
 * @date 30.11.15
 */

namespace Application\Controller;

use Application\Course\Getter;
use Application\Entity\Currency;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractConsoleController;

/**
 * Контроллер для получения курсов валют
 * @package Application\Controller
 */
class GetController extends AbstractActionController
{

    /**
     * Получает все курсы или только заданный
     * @return string
     * @throws \Exception
     */
    public function getCourseAction()
    {
        $request = $this->getRequest();

        /** @var Getter $courseGetter */
        $courseGetter = $this->getServiceLocator()->get('CourseGetter');

        $courseType = $request->getParam('courseType');
        if ($courseType) {
            $course = $courseGetter->getFromGateway($courseType);
            $courses = [$course];
        } elseif ($request->getParam('all')) {
            $courses = $courseGetter->getFromAllGateways();
        } else {
            $courses = null;
        }

        if (is_null($courses)) {
            return "You should define course type\n";
        }

        return $this->saveCourses($courses);

    }

    /**
     * Временный экшин для вызова через http, а не через консоль
     * @return string
     */
    public function getAllCoursesTempAction()
    {
        /** @var Getter $courseGetter */
        $courseGetter = $this->getServiceLocator()->get('CourseGetter');
        $courses = $courseGetter->getFromAllGateways();
        echo $this->saveCourses($courses);
        die();
    }


    /**
     * Сохраняет курсы в БД
     * @param Currency[] $courses
     * @return string
     */
    private function saveCourses(array $courses)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('EntityManager');

        foreach ($courses as $course) {
            $entityManager->persist($course);
            echo "{$course->getType()} - {$course->getValue()}\n";
        }

        $entityManager->flush();

        return "Success\n";
    }
}
