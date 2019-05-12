<?php


namespace App\Controller;


use App\Controller\Traits\ControllerTrait;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\ReservationRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class ClientAccountController extends Controller
{
    use ControllerTrait;

    /**
     * @Route("/user/profile", name="client_profile", methods={"GET"})
     * @param ReservationRepository $repository
     * @return Response
     */
    public function clientProfile(ReservationRepository $repository): Response
    {
        $user = $this->getUser();

        return $this->render('user_settings/client_profile.html.twig', array(
            'user' => $user,
            'reservations' => $repository->getUserReservationGroupedByStatus($user->getId())
        ));
    }

    /**
     * @Route("/user/settings", name="client_settings", methods={"GET", "POST"})
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @return Response
     */
    public function setting(Request $request, UserManagerInterface $userManager): Response
    {
        $user = $this->getUser();
        $profileForm = $this->createForm(ProfileType::class, $user)->handleRequest($request);
        $changePasswordForm = $this->createForm(ChangePasswordType::class, $user)->handleRequest($request);

        if (($profileForm->isSubmitted() && $profileForm->isValid()) || ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid())) {
            $userManager->updateUser($user);
            $this->addFlash('success', $this->trans('profile.flash', [], 'FOSUserBundle'));
        }

        // parameters to template
        return $this->render('user_settings/client_settings.html.twig', [
            'user' => $user,
            'profile_form' => $profileForm->createView(),
            'change_password_form' => $changePasswordForm->createView()
        ]);
    }

}