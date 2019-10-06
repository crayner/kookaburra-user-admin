<?php
/**
 * Created by PhpStorm.
 *
 * Gibbon-Responsive
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/12/2018
 * Time: 05:59
 */
namespace Kookaburra\UserAdmin\Manager;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AccessDeniedHandler
 * @package Kookaburra\UserAdmin\Manager
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * AccessDeniedHandler constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        // If Route is api_*
        if (strpos($request->get('_route'), 'api_') === 0){
            return new JsonResponse(
                [
                    'error' => $this->translator->trans('Your request failed because you do not have access to this action.'),
                ],
                200);
        }

        $request->getSession()->getFlashBag()->add('notice', 'Your request failed because you do not have access to this action.');
        return new RedirectResponse('/' . $request->get('_locale') . '/');
    }
}