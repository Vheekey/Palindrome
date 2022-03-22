<?php
// src/Controller/SearchController.php 

namespace App\Controller;

use App\Entity\Palindrome;
use App\Form\DocumentType;
use App\Repository\PalindromeRepository;
use App\Service\TextFileHandler;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bridge\Monolog\Logger as MonologLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private PalindromeRepository $palindromeRespository;

    public function __construct(PalindromeRepository $palindromeRespository)
    {
        $this->palindromeRespository = $palindromeRespository;
    }

  /**
   * @Route("/document/form")
   * 
   * Process file for palindrome
   */
    public function index(Request $request, TextFileHandler $textFileHandler) : Response
    {
        $palindrome = new Palindrome();
        $form = $this->createForm(DocumentType::class, $palindrome);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textFile = $form->get('document')->getData();

            /* @var UploadedFile $textFile */
            if ($textFile) {
                $textFileName = $textFileHandler->upload($textFile);
                $palindrome->setDocumentName($textFileName);
            }

            $palindrome->setDocumentName(
                new File($this->getParameter('documents_directory').'/'.$palindrome->getDocumentName())
            );

            try {
                list($words, $count, $strings) = $textFileHandler->searchPalindrome($palindrome->getDocumentName());

                $palindrome->setFrequency($count);
                $palindrome->setSentence($words);
                $palindrome->setPalindrome(json_encode($strings));

                $this->palindromeRespository->add($palindrome);

                $this->addFlash(
                    'notice',
                    'Operation successful!'
                );

            } catch (\Exception $e) {

                (new Logger())->log(LogLevel::DEBUG, $e);

                $this->addFlash(
                    'notice',
                    'Operation Failed!'
                );
            }

        }

        return $this->renderForm('form/index.html.twig', [
            'form' => $form,
        ]);
    }
}