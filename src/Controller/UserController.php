<?php
namespace App\Controller;

use App\Entity\Users;
use App\Repository\CompaniesRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private function header(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        
    }

    #[Route('/user', name: 'user')]
    public function index(UsersRepository $user): Response
    {
        $this->header();
       $users =$user->findAll();
       $usersAsArray=[];
       foreach ($users as $user) {
           $companyArray = [];
           foreach ($user->getCompanyId()->toArray() as  $value) {
            $companyArray[] = [
             'id' => $value->getId(),  
             'name' =>$value->getName(),
             'type'=>$value->getType(),
            ];};
           $usersAsArray[] = [
            '_id' =>$user->getId(),
            'rut' =>$user->getRut(),
            'name' =>$user->getName(),
            'last_name' =>$user->getLastName(),
            'birthday' =>$user->getBirthday(),
            'nationality' =>$user->getNationality(),
            'companies' =>$companyArray,
           ];
        
       };
        return $this->json([
            'success' => true,
            'data' => $usersAsArray,
        ]);
    }
    #[Route('/user/create', name: 'user_create')]
    public function create(Request $request, EntityManagerInterface $em,CompaniesRepository $companiesRepository):Response
    {
        $this->header();
        $userData =$request->request;
        $user = new Users();
        $user->setRut($userData->get('rut'));
        $user->setName($userData->get('name'));
        $user->setLastName($userData->get('lastName'));
        $user->setBirthday(new \DateTime($userData->get('birthday')));
        $user->setNationality($userData->get('nationality'));
        $companies = explode(',',$userData->get('companies'));
        $em->persist($user);
        $em->flush();
        foreach ($companies as $company) {
            $id= intval($company);
            $component =$companiesRepository->find(intval($id));
            $user->addCompanyId($component);
            $em->persist($user);
            $em->flush();
        }
       
        return $this->json([
            'success' => true
        ]);
    }
   
    #[Route('/user/edit/{id}' ,name: 'user_edit')]
    public function update(Request $request,UsersRepository $usersRepository, EntityManagerInterface $em, int $id): Response
    {
        $this->header();
        $user = $usersRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $userData =$request->request;
        if($userData->get('rut'))
        $user->setRut($userData->get('rut'));
        if($userData->get('name'))
        $user->setName($userData->get('name'));
        if($userData->get('lastName'))
        $user->setLastName($userData->get('lastName'));
        if($userData->get('birthday'))
        $user->setBirthday(new \DateTime($userData->get('birthday')));
        if($userData->get('nationality'))
        $user->setNationality($userData->get('nationality'));
        $em->persist($user);
        $em->flush();
        return $this->json([
            'success' => true,
        ]);
    }
    #[Route('/user/delete/{id}', name: 'user_remove')]
    public function remove(UsersRepository $usersRepository, int $id,EntityManagerInterface $em): Response
    {
        $this->header();
        $user = $usersRepository->find($id);
        $em->remove($user);
        $em->flush();
        return $this->json([
            'success' => true,
        ]);
    }
}
