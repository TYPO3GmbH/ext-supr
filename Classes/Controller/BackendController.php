<?php
declare(strict_types=1);
namespace Supr\Supr\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\Components\MenuRegistry;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
  * The Controller for the Backend Module
  */
class BackendController extends ActionController {
 /**
     * @var ModuleTemplate
     */
    protected $moduleTemplate;

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var MenuRegistry
     */
    protected $menuRegistry;

    /**
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * Initialize view and add Css
     *
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);
        $this->moduleTemplate = $view->getModuleTemplate();
        $this->pageRenderer = $this->moduleTemplate->getPageRenderer();
        $this->pageRenderer->addCssFile('EXT:supr/Resources/Public/Css/backend.css');
        $this->menuRegistry = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry();
        $this->createMenu();
    }

    /**
     * Initialize actions
     */
    public function initializeAction()
    {
        $this->setBackendModuleTemplates();
    }

    /**
     * Set Backend Module Templates
     *
     * @return void
     */
    private function setBackendModuleTemplates()
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $viewConfiguration = [
            'view' => [
                'templateRootPaths' => ['EXT:supr/Resources/Private/Backend/Templates'],
                'partialRootPaths' => ['EXT:supr/Resources/Private/Backend/Partials'],
                'layoutRootPaths' => ['EXT:supr/Resources/Private/Backend/Layouts'],
            ],
        ];
        $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $viewConfiguration));
    }

    /**
     * create backend toolbar menu
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    private function createMenu()
    {
        $menu = $this->menuRegistry->makeMenu();
        $menu->setIdentifier('supr_module_menu');

        $menu = $this->createMenuItem($menu, 'index', 'Overview');
        $menu = $this->createMenuItem($menu, 'widgets', 'Widgets');

        $this->menuRegistry->addMenu($menu);
    }

    /**
     * @param \TYPO3\CMS\Backend\Template\Components\Menu\Menu $menu
     * @param string $action
     * @param string $title
     * @return Menu
     * @throws \InvalidArgumentException
     */
    private function createMenuItem(Menu $menu, string $action, string $title) : Menu
    {
        $menuItem = $menu->makeMenuItem();
        $isActive = $this->request->getControllerActionName() === $action;
        $uri = $this->uriBuilder->reset()->uriFor($action, [], 'Backend');
        $menuItem
            ->setTitle($title)
            ->setHref($uri)
            ->setActive($isActive);
        $menu->addMenuItem($menuItem);
        return $menu;
    }
}
