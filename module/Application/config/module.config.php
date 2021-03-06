<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'user-get-course' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'get-all-courses-secret-route-777',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Get',
                                'action' => 'getAllCoursesTemp'
                            ),
                        ),
                    ),
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'CourseGetter' => 'Application\FactoryService\CourseGetter',
            'CourseGraphPresenter' => 'Application\FactoryService\Repository\CourseGraphPresenter',
            'DiffCoursesGraphPresenter' => 'Application\FactoryService\Repository\DiffCoursesGraphPresenter',
            'Application\Course\Gateway\KursComUaBlackMarket' => 'Application\FactoryService\Gateway\KursComUaBlackMarket',
        ),
        'aliases' => array(
            'EntityManager' => 'Doctrine\ORM\EntityManager',
        ),
        'invokables' => array(
            'Application\Course\Gateway\Interbank' => \Application\Course\Gateway\Interbank::class,
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\Get' => Controller\GetController::class,
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.twig',
            'application/index/index' => __DIR__ . '/../view/application/index/index.twig',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'user-get-course' => array(
                    'options' => array(
                        'route'    => 'get-course [<courseType>] [--all]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Get',
                            'action'     => 'getCourse'
                        ),
                    ),
                ),
            ),
        ),
    ),

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),

    'zfctwig' => array(
        'environment_options' => array(
            'strict_variables' => true,
        ),
        'disable_zf_model' => false,
    ),
);
