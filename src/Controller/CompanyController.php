<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Repository\CompaniesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CompanyController extends AbstractController
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
    #[Route('/company', name: 'company')]
    public function index(CompaniesRepository $companiesRepository): Response
    {
        $this->header();

       $companies =$companiesRepository->findAll();
       $companyAsArray=[];
       foreach ($companies as $company) {
           $companyAsArray[] = [
            'id' =>$company->getId(),
            'name' =>$company->getName(),
            'type' =>$company->getType(),
 
           ];
       };
        return $this->json([
            'success' => true,
            'data' => $companyAsArray,
        ]);
    }
    #[Route('/company/create', name: 'company_create')]
    public function create(Request $request, EntityManagerInterface $em):Response
    {
        $this->header();
        $companyData =$request->request;      
        $company = new Companies();
        $company->setName($companyData->get('name'));
        $company->setType($companyData->get('type'));
        $em->persist($company);
        $em->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'company' => $company->getName()
        ]);
    }
    #[Route('/company/{id}', name: 'company_show')]
    public function show(CompaniesRepository $companiesRepository, int $id): Response
    {
        $this->header();
        $company = $companiesRepository->find($id);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'company' => $company->getName()
        ]);
    }
    #[Route('/company/edit/{id}', name: 'company_edit')]
    public function update(CompaniesRepository $companiesRepository,Request $request, EntityManagerInterface $em, int $id):Response
    {
        $this->header();
        $company = $companiesRepository->find($id);
        if (!$company) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $companyData =$request->request;
        if($companyData->get('name'))
        $company->setName($companyData->get('name'));
        if($companyData->get('type'))
        $company->setType($companyData->get('type'));
        $em->persist($company);
        $em->flush();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'company' => $company->getName()
        ]);
    }
    #[Route('/company/delete/{id}', name: 'company_remove')]
    public function remove(CompaniesRepository $companiesRepository, int $id,EntityManagerInterface $em): Response
    {
        $this->header();
        $company = $companiesRepository->find($id);
        $em->remove($company);
        $em->flush();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'company' => $company->getName()
        ]);
    }
}
