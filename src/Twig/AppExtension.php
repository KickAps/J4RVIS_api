<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension {
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
    }

    public function getFilters(): array {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('set_active_route', [$this, 'setActiveRoute'])
        ];
    }

    public function setActiveRoute(string $route): string {
        $class = "nav-item mx-0 mx-lg-1 rounded";
        $currentRoute = $this->requestStack->getCurrentRequest()->attributes->get('_route');

        return $currentRoute === $route ? $class . ' active' : $class;
    }
}
