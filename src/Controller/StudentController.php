<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 3/12/2019
 * Time: 08:07
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Container\ContainerManager;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use Kookaburra\UserAdmin\Form\NoteCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StudentController
 * @package Kookaburra\UserAdmin\Controller
*/
class StudentController extends AbstractController
{
    /**
     * noteCategory
     * @param Request $request
     * @param ContainerManager $manager
     * @param StudentNoteCategory|null $category
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/student/note/0/category/", name="student_note_category_add")
     * @Route("/student/note/{category}/category/", name="student_note_category")
     * @IsGranted("ROLE_ROUTE")
     */
    public function noteCategoryEdit(Request $request, ContainerManager $manager, ?StudentNoteCategory $category = null)
    {
        $category = $category ?: new StudentNoteCategory();

        $route = intval($category->getId()) > 0 ? $this->generateUrl('user_admin__student_note_category', ['category' => $category->getId()]) : $this->generateUrl('user_admin__student_note_category_add');

        $form = $this->createForm(NoteCategoryType::class, $category, ['action' => $route]);

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $id = $category->getId();
                $provider = ProviderFactory::create(StudentNoteCategory::class);
                $data = $provider->persistFlush($category, $data);
                if ($id !== $category->getId() && $data['status'] === 'success')
                    $form = $this->createForm(NoteCategoryType::class, $category, ['action' => $this->generateUrl('user_admin__student_note_category', ['category' => $category->getId()])]);
            } else {
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                $data['status'] = 'error';
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());

        return $this->render('@KookaburraUserAdmin/student/category.html.twig');
    }

    /**
     * noteCategoryDelete
     * @param StudentNoteCategory $category
     * @param FlashBagInterface $flashBag
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/student/note/{category}/category/delete/", name="student_note_category_delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function noteCategoryDelete(StudentNoteCategory $category, FlashBagInterface $flashBag)
    {
        if ($category instanceof StudentNoteCategory) {
            try {
                $em = ProviderFactory::getEntityManager();
                $em->remove($category);
                $em->flush();
                $flashBag->add('success', ['return.success.0', [], 'messages']);
            } catch (\PDOException|PDOException $e) {
                $flashBag->add('error', ['return.error.2', [], 'messages']);
            }
        } else {
            $flashBag->add('warning', ['return.error.1', [], 'messages']);
        }

        return $this->redirectToRoute('user_admin__students_settings');
    }
}