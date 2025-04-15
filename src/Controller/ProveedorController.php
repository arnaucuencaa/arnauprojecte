<?php

namespace App\Controller;

use App\Entity\Proveedor;
use App\Form\ProveedorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProveedorController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function home(): Response
    {
        return $this->redirectToRoute('proveedor_index');
    }

    /**
     * @Route("/proveedor", name="proveedor_index")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $proveedores = $em->getRepository(Proveedor::class)->findAll();

        return $this->render('proveedor/index.html.twig', [
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * @Route("/proveedor/nuevo", name="proveedor_nuevo")
     */
    public function nuevo(Request $request, EntityManagerInterface $em): Response
    {
        $proveedor = new Proveedor();
        $form = $this->createForm(ProveedorType::class, $proveedor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $proveedor->setFechainicio($now);
            $proveedor->setFuechaactualizacion($now);

            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('proveedor_index');
        }

        return $this->render('proveedor/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/proveedor/editar/{id}", name="proveedor_editar")
     */
    public function editar(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $proveedor = $em->getRepository(Proveedor::class)->find($id);

        if (!$proveedor) {
            throw $this->createNotFoundException('Proveedor no encontrado');
        }

        $form = $this->createForm(ProveedorType::class, $proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $proveedor->setFuechaactualizacion(new \DateTimeImmutable());
            $em->flush();

            return $this->redirectToRoute('proveedor_index');
        }

        return $this->render('proveedor/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    public function borrar(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $proveedor = $em->getRepository(Proveedor::class)->find($id);

        if (!$proveedor) {
            throw $this->createNotFoundException('Proveedor no encontrado');
        }

        if ($this->isCsrfTokenValid('delete' . $proveedor->getId(), $request->request->get('_token'))) {
            $em->remove($proveedor);
            $em->flush();
        }

        return $this->redirectToRoute('proveedor_index');
    }
}
